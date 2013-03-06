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
require_once(DEDEINC."/arc.partview.class.php");
require_once(DEDEINC."/arc.lineSearchview.class.php");

$pagesize = (isset($pagesize) && is_numeric($pagesize)) ? $pagesize : 10;


if(!isset($keyword)){
    if(!isset($q)) $q = '';
    $keyword=$q;
}

$keyword = addslashes(cn_substr($keyword,30));

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


$t1 = ExecTime();

$sp = new LineSearchView($q,$s_startDate, $s_endDate, $s_prices, $s_days);
$pw = new PartView();
$pw->SetTemplet('line_search');
$pw->Display();

PutFile($lockfile, time());

//echo ExecTime() - $t1;