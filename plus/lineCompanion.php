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
require_once(DEDEINC."/arc.lineCompanion.class.php");

$pagesize = (isset($pagesize) && is_numeric($pagesize)) ? $pagesize : 10;

$channeltype = 20;

$s_typeid   = (isset($s_typeid) && is_numeric($s_typeid)) ? $s_typeid : 0;
$s_im = isset($s_im) ? strval($s_im) : '-';
$s_over   = isset($s_over) ? strval($s_over) : '-';
$s_sex = isset($s_sex) ? strval($s_sex) : '-';
$sp = new LineCompanion($s_typeid, $s_im, $s_over, $s_sex);
$sp->Display();
exit();