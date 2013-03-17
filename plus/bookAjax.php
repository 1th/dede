<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(dirname(__FILE__)."/../include/line.func.php");
$arr = array(
	'isOk' => 0,
	'date' => '',
);
if (!$aid || !$godate) {
	echo json_encode($arr);exit();
}
$today = date('Y-m-d');
$sql    = "select distinct(godate) from `#@__line_time` where aid={$aid} and godate > '{$today}' order by godate asc";
$dsql->SetQuery($sql);
$dsql->Execute("al");
$artlist = array();
while($row = $dsql->GetArray("al")) {
	$tmp = date('w', dateToUnix($row['godate']));
	$weekname = numtoWeek($tmp);
	$row['week'] = $weekname;
	$artlist[] = $row;
}

if (!$artlist) {
	echo json_encode($arr);exit();
} else {
	$arr['isOk'] = 1;
	$arr['date'] = $artlist;
	echo json_encode($arr);exit();
}


