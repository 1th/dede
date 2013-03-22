<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");

$aid = isset($aid) ? intval($aid) : 0;
$godate = isset($godate) ? strval($godate) : '';
if(!$aid) {
	ShowMsg("你的来源不正确, 请重新填写!","-1");
	exit();
}
if(!$godate) {
	ShowMsg("你的日期格式填写不正确, 请重新填写!","-1");
	exit();
}
$sql = "select * from `#@__line` l, `#@__archives` arc where l.aid = arc.id and l.aid = '$aid'";
$arc = $dsql->GetOne($sql);
if (!$arc) {
	ShowMsg("请不要随意修改参数!","-1");
	exit();
}
$sql    = "select * from `#@__line_time` where aid=$aid and  godate='$godate'";
$prices = $dsql->GetOne($sql);
if (!$prices) {
	ShowMsg("你的来源不正确, 请重新填写!", "-1");
	exit();
}
$allPrices  =  preg_split('/\s+/', trim($prices['prices']));

$arrDesc = explode("\n", $arc['per_description']);
if ($arrDesc) {
	foreach ($arrDesc as $k => $v) {
		if (empty($v)) {
			unset($arrDesc[$k]);
		}
	}
}
if (empty($arrDesc[0])) {
	$arrDesc[0] = "成人价|{$line['price']}|{price}|1|单人成人价格";
}
foreach ($arrDesc as $k => $v) {
	$tmp         = str_replace('{price}', $allPrices[$k], $v);
	$arrDesc[$k] = explode('|', $tmp);
}


$gettedPrices = 0;
// 检测价格是否和数据库匹配
for($i=0;$i<100;$i++) {
	if(isset($_POST['pr_'.$i])) {
		if ($_POST['pr_'.$i] != $arrDesc[$i][2]) {
			ShowMsg("你的来源不正确, 请重新填写!", "-1");
			exit();
		}
		$gettedPrices += intval($arrDesc[$i][2])*intval($_POST['p_'.$i]);
		$arrDesc[$i][3] = intval($_POST['p_'.$i]);
		$arrDesc[$i][5] = intval($arrDesc[$i][2])*intval($_POST['p_'.$i]);
	}
}
if($hotelpricesel) {
	$gettedPrices += $hotelprice;
}

$userInfos = array();
foreach ($name as $k => $v) {
	$userInfos[] = array(
		'name'=> $name[$k],
		'sex' => $sex[$k],
		'id'  => $id[$k],
		'tel' => $tel[$k]
	);
}

$__aid      = $aid;
$__userid   = $_REQUEST['DedeUserID'] ? $_REQUEST['DedeUserID'] : 0;
$__buyid    = date('Ymdhis').sprintf('%1$05d', rand(0, 99999));
$__title    = $arc['title'];
$__truename = $truename;
$__godate   = $godate;
$__price    = $gettedPrices;
$__email    = $email;
$__identity = $idno;
$__desc     = addslashes(json_encode($arrDesc));
$__userinfo = addslashes(json_encode($userInfos));
$__idtype   = $idtype;
$__hoteltype = $hotelpricesel;
$__hotelfee = $hotelprice;
$__IP       = $_SERVER['REMOTE_ADDR'];
$__addtime = time();
$__ischeck = 0;
$sql = "
	insert into `#@__line_order` (
		aid,
		userid   ,
		buyid    ,
		title    ,
		truename ,
		godate   ,
		price    ,
		email    ,
		identity ,
		idtype   ,
		hoteltype ,
		hotelfee ,
		IP       ,
		addtime ,
		ischeck,
		descs,
		userinfo
	)values (
	'$__aid',
	'$__userid',
	'$__buyid',
	'$__title',
	'$__truename',
	'$__godate',
	'$__price',
	'$__email',
	'$__identity' ,
	'$__idtype',
	'$__hoteltype' ,
	'$__hotelfee' ,
	'$__IP',
	'$__addtime' ,
	'$__ischeck',
	'$__desc',
	'$__userinfo'
	)";
if ($dsql->ExecNoneQuery($sql)) {
	ShowMsg("下单成功!", "$cfg_basehost/plus/view.php?aid=$aid");exit();
}