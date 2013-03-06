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

	private $_aim;
	private $_startDate;
	private $_endDate;
	private $_price;
	private $_dayType;
	private $_table = '`#@__archives`';
	private $_line  = '`#@__line`';
	private $_lineTime = '`#@__line_time`';
	private $_condition = '';
	private $PageNo;
	private $PageSize;
	private $TotalResult;
	private $SearchMaxRc;

	function __construct($aim='', $startDate='', $endDate = '', $priceType = '', $dayType = ''){

		$this->_aim       = $aim;
		$this->_startDate = $startDate;
		$this->_endDate   = $endDate;
		$this->_priceType     = $priceType;
		$this->_dayType      = $dayType;
		$this->PageSize = 20;
		$this->SearchMaxRc = 1000;
		$this->getCondition();
	}

	function getCount() {
		global $dsql;
		$sql = "select count(distinct(lt.aid)) as count from {$this->_lineTime} lt, {$this->_line} l where l.aid = lt.aid {$this->_condition}";
		$result = $dsql->GetOne($sql);
		$this->TotalResult = $result['count'];
		return $result['count'];
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
		global $page;
		$this->PageNo = $page;
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


		//当结果超过限制时，重设结果页数
		if($this->TotalResult > $this->SearchMaxRc)
		{
			$totalpage = ceil($this->SearchMaxRc/$this->PageSize);
		}
		$infos = "<td>共找到<b>".$this->TotalResult."</b>条记录/最大显示<b>{$totalpage}</b>页 </td>\r\n";
		$geturl .= "&q=".$this->_aim;
		$hidenform .= "<input type='hidden' name='q' value='".$this->_aim."'>\r\n";
		$geturl .= "&s_prices=".$this->_price."&pagesize=".$this->PageSize;
		$hidenform .= "<input type='hidden' name='pagesize' value='".$this->PageSize."'>\r\n";
		$geturl .= "&s_startDate=".$this->_startDate."&s_endDate=".$this->_endDate."&";
		$hidenform .= "<input type='hidden' name='s_startDate' value='".$this->_startDate."'>\r\n";
		$hidenform .= "<input type='hidden' name='s_endDate' value='".$this->_endDate."'>\r\n";
		$purl .= "?".$geturl;

		//获得上一页和下一页的链接
		if($this->PageNo != 1)
		{
			$prepage.="<td width='50'><a href='".$purl."PageNo=$prepagenum'>上一页</a></td>\r\n";
			$indexpage="<td width='30'><a href='".$purl."PageNo=1'>首页</a></td>\r\n";
		}
		else
		{
			$indexpage="<td width='30'>首页</td>\r\n";
		}
		if($this->PageNo!=$totalpage && $totalpage>1)
		{
			$nextpage.="<td width='50'><a href='".$purl."PageNo=$nextpagenum'>下一页</a></td>\r\n";
			$endpage="<td width='30'><a href='".$purl."PageNo=$totalpage'>末页</a></td>\r\n";
		}
		else
		{
			$endpage="<td width='30'>末页</td>\r\n";
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
				$listdd.= "<td>$j&nbsp;</td>\r\n";
			}
			else
			{
				$listdd.="<td><a href='".$purl."PageNo=$j'>[".$j."]</a>&nbsp;</td>\r\n";
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

	function getCondition(){
		if ($this->_aim) {
			$this->_condition .= " and l.aimplace = '{$this->_aim}'";
		}
		if ($this->_startDate) {
			$this->_condition .= " and lt.godate >= {$this->_startDate}";
		}
		if ($this->_endDate) {
			$this->_condition .= " and lt.godate <= {$this->_endDate}";
		}
		if ($this->_dayType) {
			switch($this->_dayType) {
				case '5';
					$this->_condition .= " and l.days <= 5 ";
				break;
				case '10';
					$this->_condition .= " and l.days <=5 and l.days <= 10 ";
					break;
				case '15';
					$this->_condition .= " and l.days <=10 and l.days <= 15 ";
					break;
				case '30';
					$this->_condition .= " and l.days <=15 and l.days <= 30 ";
					break;
				case 'm30';
					$this->_condition .= " and l.days <=30 ";
					break;
			}

		}
		if ($this->_priceType) {
			switch($this->_priceType) {
				case '1000';
					$this->_condition .= " and l.memberprice <= 1000 ";
					break;
				case '5000';
					$this->_condition .= " and l.memberprice <=1000 and l.memberprice <= 5000 ";
					break;
				case '10000';
					$this->_condition .= " and l.memberprice <=5000 and l.memberprice <= 10000 ";
					break;
				case 'm10000';
					$this->_condition .= " and l.memberprice <=10000 ";
					break;
			}
		}
	}

	/**
	 *  获得当前的页面文件的url
	 *
	 * @access    public
	 * @return    string
	 */
	function GetCurUrl()
	{
		$nowurl = '';
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