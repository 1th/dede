<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC.'/arc.book.class.php');

$channeltype = 17;

$aid = isset($aid) ? intval($aid) : 0;
$godate = isset($godate) ? strval($godate) : '';
if(!$aid)
{
	ShowMsg("你的来源不正确, 请重新填写!","-1");
	exit();
}
if(!$godate)
{
	ShowMsg("你的日期格式填写不正确, 请重新填写!","-1");
	exit();
}
$sp = new Book($aid, $godate);
$line       = $sp->getArc();
$title = $line['title'];
$lineTime   = $sp->getLineTime();
$sp->Display();
exit();