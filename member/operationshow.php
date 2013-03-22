<?php 
/**
 * 操作
 * 
 * @version        $Id: search.php 1 8:38 2010年7月9日Z tianya $
 * @package        DedeCMS.Member
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/operationshow.class.php");
CheckRank(0,0);
$menutype = 'mydede';
$menutype_son = 'op';
setcookie("ENV_GOBACK_URL",GetCurUrl(),time()+3600,"/");
if (!$orderid) {
	ShowMsg("订单信息为空, 请重新打开", '-1');exit();
}

/**
 *  获取状态
 *
 * @param     string  $sta  状态ID
 * @return    string
 */
function GetSta($sta){
    if($sta==0) return '未付款';
    else if($sta==1) return '已付款';
    else return '已完成';
}

$sql = "SELECT * FROM `#@__line_order` WHERE buyid='".$orderid."'";
$dlist = new operationshow($orderid);
$line = $dlist->getLine();
if (!$line) {
	ShowMsg("订单信息为空, 请重新打开", '-1');exit();
}
$dlist->SetTemplate(DEDEMEMBER."/templets/operation_show.htm");
$dlist->Display();
