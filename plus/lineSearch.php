<?php
/**
 *
 * 搜索页
 *
 * @version        $Id: search.php 1 15:38 2010年7月8日Z tianya $
 * @package        DedeCMS.Site
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC."/arc.lineSearchview.class.php");

$pagesize = (isset($pagesize) && is_numeric($pagesize)) ? $pagesize : 10;

$channeltype = 17;

$s_typeid   = (isset($s_typeid) && is_numeric($s_typeid)) ? $s_typeid : 0;
$s_prices = isset($s_prices) ? strval($s_prices) : '';
$s_days   = isset($s_days) ? strval($s_days) : '';
$s_aimPlace = isset($s_aimPlace) ? strval($s_aimPlace) : '';
$s_startDate = isset($s_startDate) ? strval($s_startDate) : '';
$s_endDate = isset($s_endDate) ? strval($s_endDate) : '';

$sp = new LineSearchView($s_prices, $s_aimPlace, $s_days, $s_endDate, $s_startDate, $s_typeid);
$sp->Display();
exit();





if($cfg_notallowstr !='' && preg_match("#".$cfg_notallowstr."#i", $keyword))
{
	ShowMsg("你的搜索关键字中存在非法内容，被系统禁止！","-1");
	exit();
}

if(($keyword=='' || strlen($keyword)<2) && empty($typeid))
{
	ShowMsg('关键字不能小于2个字节！','-1');
	exit();
}

//检查搜索间隔时间
$lockfile = DEDEDATA.'/time.lock.inc';
$lasttime = file_get_contents($lockfile);
if(!empty($lasttime) && ($lasttime + $cfg_search_time) > time())
{
	ShowMsg('管理员设定搜索时间间隔为'.$cfg_search_time.'秒，请稍后再试！','-1');
	exit();
}

//开始时间
if(empty($starttime)) $starttime = -1;
else
{
	$starttime = (is_numeric($starttime) ? $starttime : -1);
	if($starttime>0)
	{
		$dayst = GetMkTime("2008-1-2 0:0:0") - GetMkTime("2008-1-1 0:0:0");
		$starttime = time() - ($starttime * $dayst);
	}
}

$t1 = ExecTime();

$sp = new SearchView($typeid,$keyword,$orderby,$channeltype,$searchtype,$starttime,$pagesize,$kwtype,$mid);
$keyword = $oldkeyword;
$sp->Display();


PutFile($lockfile, time());

//echo ExecTime() - $t1;