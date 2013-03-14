<?php   if(!defined('DEDEINC')) exit("Request Error!");
/**
 * 搜索视图类
 *
 * @version        $Id: arc.searchview.class.php 1 15:26 2010年7月7日Z tianya $
 * @package        DedeCMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(DEDEINC."/typelink.class.php");
require_once(DEDEINC."/dedetag.class.php");
require_once(DEDEINC."/splitword.class.php");
require_once(DEDEINC."/taglib/hotwords.lib.php");
require_once(DEDEINC."/taglib/channel.lib.php");

@set_time_limit(0);
@ini_set('memory_limit', '512M');

/**
 * 搜索视图类
 *
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

	function __construct($aid, $bookDate)
	{

		$this->Aid      = $aid;
		$this->BookDate = $bookDate;

		//system
		$this->dsql = $GLOBALS['dsql'];
		$this->dtp = new DedeTagParse();
		$this->dtp->SetRefObj($this);
		$this->dtp->SetNameSpace("dede","{","}");
		$this->dtp2 = new DedeTagParse();
		$this->dtp2->SetNameSpace("field","[","]");



		$tempfile = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir']."/".$GLOBALS['cfg_df_style']."/book.htm";
		if(!file_exists($tempfile)||!is_file($tempfile))
		{
			echo "模板文件不存在，无法解析！";
			exit();
		}
		$this->dtp->LoadTemplate($tempfile);


	}



	public function getArc()
	{
		$sql = "select * from `#@__line` l, `#@__archives` arc where l.aid = arc.id and l.aid = '$this->Aid'";
		return $this->dsql->GetOne($sql);
	}

	public function getLineTime()
	{
		$sql = "select * from `#@__line_time` where aid={$this->Aid} and  godate='$this->BookDate'";
		return $this->dsql->GetOne($sql);
	}

	//关闭相关资源
	function Close()
	{
	}


	function Display()
	{
		if (!$this->getLineTime()) {
			ShowMsg("来源不正确, 请重新填写!","-1");
			exit();
		}
		$this->dtp->Display();
	}


}//End Class