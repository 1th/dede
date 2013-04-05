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
CheckRank(0,0);
require_once(DEDEINC."/alipay/alipay.config.php");
require_once(DEDEINC."/alipay/lib/alipay_submit.class.php");
/**************************请求参数**************************/

//支付类型
$payment_type = "1";
//必填，不能修改
//服务器异步通知页面路径
$notify_url = "{$cfg_basehost}/plus/Linebuyaction.php";
//需http://格式的完整路径，不能加?id=123这类自定义参数

//页面跳转同步通知页面路径
$return_url = "{$cfg_basehost}/plus/Linebuyaction.php";
//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

//卖家支付宝帐户
$seller_email = $alipay_config['account'];
//必填

//商户订单号
$out_trade_no = $_POST['WIDout_trade_no'];
//商户网站订单系统中唯一订单号，必填

//订单名称
$subject = $_POST['WIDsubject'];
//必填

//付款金额
$total_fee = $_POST['WIDtotal_fee'];
//必填

//订单描述
$body = $_POST['WIDbody'];

//商品展示地址
$show_url = $_POST['WIDshow_url'];
//需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html

//防钓鱼时间戳
$anti_phishing_key = "";
//若要使用请调用类文件submit中的query_timestamp函数

//客户端的IP地址
$exter_invoke_ip = "";
//非局域网的外网IP地址，如：221.0.0.1


/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array(
	"service" => "create_direct_pay_by_user",
	"partner" => trim($alipay_config['partner']),
	"payment_type"	=> $payment_type,
	"notify_url"	=> $notify_url,
	"return_url"	=> $return_url,
	"seller_email"	=> $seller_email,
	"out_trade_no"	=> $out_trade_no,
	"subject"	=> $subject,
	"total_fee"	=> $total_fee,
	"body"	=> $body,
	"show_url"	=> $show_url,
	"anti_phishing_key"	=> $anti_phishing_key,
	"exter_invoke_ip"	=> $exter_invoke_ip,
	"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
echo $alipaySubmit->buildRequestForm($parameter,"get", "确认");
