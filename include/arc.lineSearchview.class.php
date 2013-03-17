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
class LineSearchView
{
	private $dsql;
	private $dtp;
	private $dtp2;
	private $PageSize;
	private $TotalPage;
	private $PageNo;
	private $TotalResult;
	private $TypeID;
	private $Price;
	private $AimPlace;
	private $Days;
	private $EndDate;
	private $StartDate;
	private $Flag;

	function __construct($s_prices, $s_aimPlace, $s_days, $s_endDate, $s_startDate, $s_typeid, $s_flag)
	{

		$this->Price     = $s_prices;
		$this->AimPlace  = $s_aimPlace;
		$this->Days      = $s_days;
		$this->TypeID    = $s_typeid;
		$this->StartDate = $s_startDate;
		$this->EndDate   = $s_endDate;
		$this->Flag      = $s_flag;
		//system
		$this->dsql = $GLOBALS['dsql'];
		$this->dtp = new DedeTagParse();
		$this->dtp->SetRefObj($this);
		$this->dtp->SetNameSpace("dede","{","}");
		$this->dtp2 = new DedeTagParse();
		$this->dtp2->SetNameSpace("field","[","]");
		$this->dtp3 = new DedeTagParse();
		$this->dtp3->SetNameSpace("sfield","[","]");
		foreach($GLOBALS['PubFields'] as $k=>$v)
		{
			$this->Fields[$k] = $v;
		}


		$tempfile = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir']."/".$GLOBALS['cfg_df_style']."/line_search.htm";
		if(!file_exists($tempfile)||!is_file($tempfile))
		{
			echo "模板文件不存在，无法解析！";
			exit();
		}
		$this->dtp->LoadTemplate($tempfile);

		$this->PageSize = 10;
		$this->PageNo = isset($GLOBALS['PageNo']) ? $GLOBALS['PageNo'] : 1;
		$this->TotalPage = ceil($this->TotalResult/$this->PageSize);

	}

	public function getPriceSql()
	{
		switch ($this->Price) {
			case 100:
				return "and l.price between 1 and 100";
			break;
			case 500:
				return 'and l.price between 101 and 500';
			break;
			case 1000:
				return 'and l.price between 501 and 1000';
			break;
			case 3000:
				return 'and l.price between 1001 and 3000';
			break;
			case 'm3000':
				return 'and l.price > 3000';
			break;
			default :
				return '';
			break;
		}
	}

	public function getDaysSql()
	{
		switch ($this->Price) {
			case 1:
				return 'and l.days=1';
				break;
			case 3:
				return 'and l.days between 2 and 3';
				break;
			case 5:
				return 'and l.days between 4 and 5';
				break;
			case 7:
				return 'and l.days between 6 and 7';
				break;
			case 'm7':
				return 'and l.days > 7';
				break;
			default:
				return '';
			break;
		}
	}

	public function getAimPlaceSql()
	{
		if ($this->AimPlace) {
			return " and l.aimplace = '{$this->AimPlace}'";
		} else {
			return '';
		}
	}

	public function getFlagSql()
	{
		if ($this->Flag) {
			return " and arc.flag like '%{$this->Flag}%'";
		} else {
			return '';
		}
	}

	public function getAidsSql()
	{
		if ($this->StartDate || $this->EndDate) {
			$sql = " godate > '" . date('Y-m-d')."'";
			if ($this->StartDate) {
				$sql .= " and godate > '$this->StartDate'";
			}
			if ($this->EndDate) {
				$sql .= " and godate < '$this->EndDate'";
			}
			$sql = "select distinct aid from `#@__line_time` where $sql";
			$this->dsql->SetQuery($sql);
			$this->dsql->Execute("st");
			$aids = array();
			while($row = $this->dsql->GetArray("st")) {
				$aids[] = $row['aid'];
			}
			if ($aids) {
				$return = " and l.aid in (" . implode(',', $aids) . ')';
			} else {
				$return = ' and l.aid in (0)';
			}
		} else {
			$return  = '';
		}
		return $return;
	}

	public function getTypeSql(){
		if ($this->TypeID) {
			return " and l.typeid = $this->TypeID";
		} else {
			return '';
		}
	}
	//关闭相关资源
	function Close()
	{
	}


	function Display()
	{
		foreach($this->dtp->CTags as $tagid=>$ctag)
		{
			$tagname = $ctag->GetName();
			if($tagname=="pagelist")
			{
				$list_len = trim($ctag->GetAtt("listsize"));
				if($list_len=="")
				{
					$list_len = 3;
				}
				$this->dtp->Assign($tagid,$this->GetPageListDM($list_len));
			}
			else if($tagname=="field")
			{
				//类别的指定字段
				if(isset($this->Fields[$ctag->GetAtt('name')]))
				{
					$this->dtp->Assign($tagid,$this->Fields[$ctag->GetAtt('name')]);
				}
				else
				{
					$this->dtp->Assign($tagid,"");
				}
			}
			else if($tagname=="channel")
			{
				//下级频道列表
				if($this->TypeID>0)
				{
					$typeid = $this->TypeID; $reid = $this->TypeLink->TypeInfos['reid'];
				}
				else
				{
					$typeid = 0; $reid=0;
				}
				$GLOBALS['envs']['typeid'] = $typeid;
				$GLOBALS['envs']['reid'] = $typeid;
				$this->dtp->Assign($tagid,lib_channel($ctag,$this));
			}else if($tagname=="search")
			{
				$InnerText = trim($ctag->GetInnerText());
				$this->dtp->Assign($tagid,$this->getSearchList($InnerText));
			}else if($tagname=="types")
			{
				$InnerText = trim($ctag->GetInnerText());
				$this->dtp->Assign($tagid,$this->getSearchTypes($InnerText));
			} //End if

		}
		$this->dtp->Display();
	}

	public function getSearchTypes($innerText)
	{
		$sql = "select * from `#@__arctype` where channeltype=17 and ispart=0";
		$this->dsql->SetQuery($sql);
		$this->dsql->Execute("st");
		$this->dtp2->LoadSource($innerText);
		$return = '';
		while($row = $this->dsql->GetArray("st")) {
			if(is_array($this->dtp2->CTags))
			{
				foreach($this->dtp2->CTags as $k=>$ctag)
				{
					if($ctag->GetName()=='array')
					{
						//传递整个数组，在runphp模式中有特殊作用
						$this->dtp2->Assign($k,$row);
					}
					else
					{
						if(isset($row[$ctag->GetName()]))
						{
							$this->dtp2->Assign($k,$row[$ctag->GetName()]);
						}
						else
						{
							$this->dtp2->Assign($k,'');
						}
					}
				}
			}
			$return .= $this->dtp2->GetResult();
		}
		return $return;
	}

	public function  getLimitSql()
	{
		return ' LIMIT '. ($this->PageNo-1)*$this->PageSize .','.$this->PageSize;
	}

	public function getSearchList($innerText)
	{
		$cnt  = "select count(aid) as cnt from `#@__line` l, `#@__archives` arc where l.aid = arc.id ";
		$cnt .= $this->getAidsSql();
		$cnt .= $this->getAimPlaceSql();
		$cnt .= $this->getDaysSql();
		$cnt .= $this->getPriceSql();
		$cnt .= $this->getTypeSql();
		$cnt .= $this->getFlagSql();
		$rs = $this->dsql->GetOne($cnt);
		$this->TotalResult = $rs['cnt'];
		if ($this->TotalResult) {
			$sql = "select l.aid,l.days,l.price,arc.flag, l.memberprice, arc.title from `#@__line` l, `#@__archives` arc where l.aid = arc.id ";
			$sql .= $this->getAidsSql();
			$sql .= $this->getAimPlaceSql();
			$sql .= $this->getDaysSql();
			$sql .= $this->getPriceSql();
			$sql .= $this->getTypeSql();
			$sql .= $this->getFlagSql();
			$sql .= $this->getLimitSql();
			$this->dsql->SetQuery($sql);
			$this->dsql->Execute("al");
			$this->dtp3->LoadSource($innerText);
			$artlist = '';
			while($row = $this->dsql->GetArray("al")) {
				$row['linkurl'] = $GLOBALS['cfg_cmsurl'].'/plus/view.php?aid='.$row['aid'];
				$tag = '';
				if (strpos($row['flag'], 's') !== false) {
					$tag .='<i class="mark mark-hot"></i>';
				}
				if (strpos($row['flag'], 'h') !== false) {
					$tag .='<i class="mark mark-jing"></i>';
				}
				if (strpos($row['flag'], 'a') !== false) {
					$tag .='<i class="mark mark-main"></i>';
				}
				$row['tag'] = $tag;
				if(is_array($this->dtp3->CTags)) {
					foreach($this->dtp3->CTags as $k=>$ctag) {
						if($ctag->GetName()=='array') {
							//传递整个数组，在runphp模式中有特殊作用
							$this->dtp3->Assign($k,$row);
						} else {
							if(isset($row[$ctag->GetName()])) {
								$this->dtp3->Assign($k,$row[$ctag->GetName()]);
							} else {
								$this->dtp3->Assign($k,'');
							}
						}
					}
				}
				$artlist .= $this->dtp3->GetResult();
			}
			return $artlist;
		}  else {
			return '';
		}
	}


	/**
	 *  获取动态的分页列表
	 *
	 * @access    public
	 * @param     string  $list_len  列表宽度
	 * @return    string
	 */
	function GetPageListDM($list_len)
	{
		$prepage="";
		$nextpage="";
		$prepagenum = $this->PageNo - 1;
		$nextpagenum = $this->PageNo + 1;
		if($list_len=="" || preg_match("/[^0-9]/", $list_len))
		{
			$list_len=3;
		}
		$totalpage = ceil($this->TotalResult / $this->PageSize);
		if($totalpage<=1 && $this->TotalResult>0)
		{
			return "共1页/".$this->TotalResult."条记录";
		}
		if($this->TotalResult == 0)
		{
			return "共0页/".$this->TotalResult."条记录";
		}
		$purl = $this->GetCurUrl();


		$infos = "<td style='padding:0 10px;'>共找到<b>".$this->TotalResult."</b>条记录/最大显示<b>{$totalpage}</b>页 </td>\r\n";
		if ($this->AimPlace) {
			$geturl = "s_aimPlace=".urlencode($this->AimPlace);
			$hidenform = "<input type='hidden' name='s_aimPlace' value='".rawurldecode($this->AimPlace)."'>\r\n";
		}
		if ($this->Price) {
			$geturl .= "&s_prices=".$this->Price;
			$hidenform .= "<input type='hidden' name='s_prices' value='".$this->Price."'>\r\n";
		}
		if ($this->Days) {
			$geturl .= "&s_days=".$this->Days;
			$hidenform .= "<input type='hidden' name='s_days' value='".$this->Days."'>\r\n";
		}
		if ($this->TypeID) {
			$geturl .= "&s_typeid=".$this->TypeID;
			$hidenform .= "<input type='hidden' name='s_typeid' value='".$this->TypeID."'>\r\n";
		}
		// if ($this->PageNo) {
		// 	$geturl .= "&PageNo=".$this->PageNo;
		// 	$hidenform .= "<input type='hidden' name='PageNo' value='".$this->PageNo."'>\r\n";
		// }
		if ($this->StartDate) {
			$geturl .= "&s_startDate=".$this->StartDate;
			$hidenform .= "<input type='hidden' name='s_startDate' value='".$this->StartDate."'>\r\n";
		}
		if ($this->EndDate) {
			$geturl .= "&s_endDate=".$this->EndDate."&";
			$hidenform .= "<input type='hidden' name='s_endDate' value='".$this->EndDate."'>\r\n";
		}
		if ($this->Flag) {
			$geturl .= "&s_flags=".$this->Flag."&";
			$hidenform .= "<input type='hidden' name='s_flags' value='".$this->Flag."'>\r\n";
		}
		$purl .= "?".$geturl;

		//获得上一页和下一页的链接
		if($this->PageNo != 1)
		{
			$prepage.="<td width='60'><a href='".$purl."&PageNo=$prepagenum'>上一页</a></td>\r\n";
			$indexpage="<td width='40'><a href='".$purl."&PageNo=1'>首页</a></td>\r\n";
		}
		else
		{
			$indexpage="<td><span>首页</span>&nbsp;</td>\r\n";
		}
		if($this->PageNo!=$totalpage && $totalpage>1)
		{
			$nextpage.="<td width='60'><a href='".$purl."&PageNo=$nextpagenum'>下一页</a></td>\r\n";
			$endpage="<td width='40'><a href='".$purl."&PageNo=$totalpage'>末页</a></td>\r\n";
		}
		else
		{
			$endpage="<td><span>末页</span>&nbsp;</td>\r\n";
		}

		//获得数字链接
		$listdd="";
		$total_list = $list_len * 2 + 1;
		if($this->PageNo >= $total_list)
		{
			$j = $this->PageNo - $list_len;
			$total_list = $this->PageNo + $list_len;
			if($total_list > $totalpage)
			{
				$total_list = $totalpage;
			}
		}
		else
		{
			$j=1;
			if($total_list > $totalpage)
			{
				$total_list = $totalpage;
			}
		}
		for($j; $j<=$total_list; $j++)
		{
			if($j == $this->PageNo)
			{
				$listdd.= "<td><span>$j</span>&nbsp;</td>\r\n";
			}
			else
			{
				$listdd.="<td><a href='".$purl."&PageNo=$j'> ".$j." </a>&nbsp;</td>\r\n";
			}
		}
		$plist  =  "<table border='0' cellpadding='0' cellspacing='0'>\r\n";
		$plist .= "<tr align='center' style='font-size:10pt'>\r\n";
		$plist .= "<form name='pagelist' action='".$this->GetCurUrl()."'>$hidenform";
		$plist .= $infos;
		$plist .= $indexpage;
		$plist .= $prepage;
		$plist .= $listdd;
		$plist .= $nextpage;
		$plist .= $endpage;
		if($totalpage>$total_list)
		{
			$plist.="<td width='38'><input type='text' name='PageNo' style='width:28px;height:14px' value='".$this->PageNo."' /></td>\r\n";
			$plist.="<td width='30'><input type='submit' name='plistgo' value='GO' style='width:30px;height:22px;font-size:9pt' /></td>\r\n";
		}
		$plist .= "</form>\r\n</tr>\r\n</table>\r\n";
		return $plist;
	}

	/**
	 *  获得当前的页面文件的url
	 *
	 * @access    public
	 * @return    string
	 */
	function GetCurUrl()
	{
		if(!empty($_SERVER["REQUEST_URI"]))
		{
			$nowurl = $_SERVER["REQUEST_URI"];
			$nowurls = explode("?",$nowurl);
			$nowurl = $nowurls[0];
		}
		else
		{
			$nowurl = $_SERVER["PHP_SELF"];
		}
		return $nowurl;
	}
}//End Class