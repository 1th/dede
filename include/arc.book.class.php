<?php   if (!defined('DEDEINC')) exit("Request Error!");
/**
 * 搜索视图类
 * @version        $Id: arc.searchview.class.php 1 15:26 2010年7月7日Z tianya $
 * @package        DedeCMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(DEDEINC . "/typelink.class.php");
require_once(DEDEINC . "/dedetag.class.php");
require_once(DEDEINC . "/splitword.class.php");
require_once(DEDEINC . "/taglib/hotwords.lib.php");
require_once(DEDEINC . "/taglib/channel.lib.php");
require_once(DEDEINC . '/line.func.php');


@set_time_limit(0);
@ini_set('memory_limit', '512M');

/**
 * 搜索视图类
 * @package          SearchView
 * @subpackage       DedeCMS.Libraries
 * @link             http://www.dedecms.com
 */
class Book
{
	private $dsql;
	private $dtp;
	private $dtp2;
	private $Aid;
	private $BookDate;
	private $ArcInfo;
	private $Prices;
	private $TempSource;
	private $Step;
	public $Fields;

	function __construct($aid, $bookDate, $step = 1)
	{
		// project
		$this->Aid      = $aid;
		$this->BookDate = $bookDate;

		//system
		$this->dsql = $GLOBALS['dsql'];
		$this->dtp  = new DedeTagParse();
		$this->dtp->SetRefObj($this);
		$this->dtp->SetNameSpace("dede", "{", "}");
		$this->dtp2 = new DedeTagParse();
		$this->dtp2->SetNameSpace("field", "[", "]");
		$this->Step = $step;
		// template
		$this->Fields = array();
		$this->_init();
	}

	private function _init()
	{
		$this->ArcInfo = $this->_getArc();
		$this->Prices  = $this->_getPrices();
	}

	private function _loadTemplate()
	{
		if ($this->TempSource == '') {
			$tempfile = $GLOBALS['cfg_basedir'] . $GLOBALS['cfg_templets_dir'] . "/" . $GLOBALS['cfg_df_style'] . "/book.htm";
			if (!file_exists($tempfile) || !is_file($tempfile)) {
				echo "模板文件不存在，无法解析文档！";
				exit();
			}
			$this->dtp->LoadTemplate($tempfile);
			$this->TempSource = $this->dtp->SourceString;
		} else {
			$this->dtp->LoadSource($this->TempSource);
		}
	}

	private function _getArc()
	{
		$sql = "select * from `#@__line` l, `#@__archives` arc where l.aid = arc.id and l.aid = '$this->Aid'";
		$arc = $this->dsql->GetOne($sql);
		if (!$arc) {
			ShowMsg("你的来源不正确, 请重新填写!", "-1");
			exit();
		}
		return $arc;
	}

	public function getTitle()
	{
		return $this->ArcInfo['title'];
	}

	private function _getPrices()
	{
		$sql    = "select * from `#@__line_time` where aid={$this->Aid} and  godate='$this->BookDate'";
		$prices = $this->dsql->GetOne($sql);
		if (!$prices) {
			ShowMsg("你的来源不正确, 请重新填写!", "-1");
		}
		return preg_split('/\s+/', trim($prices['prices']));

	}


	public function getDescs()
	{
		$arrDesc = explode("\n", $this->ArcInfo['per_description']);
		if ($arrDesc) {
			foreach ($arrDesc as $k => $v) {
				if (empty($v)) {
					unset($arrDesc[$k]);
				}
			}
		}
		if (empty($arrDesc[0])) {
			$arrDesc[0] = "成人价|{$line['price']}|{price}|1|单人成人价格";
		}
		foreach ($arrDesc as $k => $v) {
			$tmp         = str_replace('{price}', $this->Prices[$k], $v);
			$arrDesc[$k] = explode('|', $tmp);
		}
		return $arrDesc;
	}


	function Display()
	{
		$this->_loadTemplate();
		$this->_parseTempletsFirst();
		foreach ($this->ArcInfo as $k => $v) {
			$this->Fields[$k] = $v;
		}
		$this->Fields['startDate'] = $this->BookDate;
		$this->Fields['endDate'] = addDays($this->BookDate, $this->Fields['days']);
		$this->Fields['step'] = $this->Step;
		$this->_parseCustomFields();
		$this->dtp->Display();
	}


	/**
	 *  解析模板，对固定的标记进行初始给值
	 * @access    public
	 * @return    void
	 */
	private function _parseTempletsFirst()
	{
		MakeOneTag($this->dtp, $this, 'N');
	}

	private function _parseCustomFields(){

		if(is_array($this->dtp->CTags))
		{
			foreach($this->dtp->CTags as $i=>$ctag)
			{
				if($ctag->GetName()=='field')
				{
					$this->dtp->Assign($i, $this->GetField($ctag->GetAtt('name'), $ctag) );
				}
			}//结束模板循环

		}
	}

	 /**
	 *  获得站点的真实根路径
	 *
	 * @access    public
	 * @return    string
	 */
	function GetTruePath()
	{
		$TRUEpath = $GLOBALS["cfg_basedir"];
		return $TRUEpath;
	}

	/**
	 *  获得指定键值的字段
	 *
	 * @access    public
	 * @param     string  $fname  键名称
	 * @param     string  $ctag  标记
	 * @return    string
	 */
	function GetField($fname, $ctag)
	{
		//所有Field数组 OR 普通Field
		if($fname=='array')
		{
			return $this->Fields;
		}
		//指定了ID的节点
		else if($ctag->GetAtt('noteid') != '')
		{
			if( isset($this->Fields[$fname.'_'.$ctag->GetAtt('noteid')]) )
			{
				return $this->Fields[$fname.'_'.$ctag->GetAtt('noteid')];
			}
		}
		//指定了type的节点
		else if($ctag->GetAtt('type') != '')
		{
			if( isset($this->Fields[$fname.'_'.$ctag->GetAtt('type')]) )
			{
				return $this->Fields[$fname.'_'.$ctag->GetAtt('type')];
			}
		}
		else if( isset($this->Fields[$fname]) )
		{
			return $this->Fields[$fname];
		}
		return '';
	}
}//End Class