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
if (!$step) {
	$step = 1;
}
$sp = new Book($aid, $godate, $step);

$descs = $sp->getDescs();

$sp->Display();
exit();