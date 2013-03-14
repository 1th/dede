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