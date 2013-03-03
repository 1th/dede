<?php
defined('IN_DEDE') or exit('Wrong info used for reject');
include DEDEINC . '/line.func.php';
$GLOBALS['line'] = $arc->addTableRow;
//$aid 文章ID

$sql = "select godate, prices from `#@__line_time` WHERE aid = $aid order by godate asc limit 1";
$rs = $dsql->GetOne($sql);
// 下次出行日期
$GLOBALS['l_bookdate'] = $rs['godate'];
// 返回日期
$GLOBALS['l_returndate'] = date('Y-m-d',strtotime("+{$line['days']}days", dateToUnix($l_bookdate)));
// 分析说明文件
$descs = explode("\n", $line['per_description']);

if ($descs) {
	foreach($descs as $k => $v){
		if (empty($v)) {
			unset($descs[$k]);
		}
	}
}
if (empty($descs[0])){
	$descs[0] = "成人价|{$line['price']}|{price}|1|单人成人价格";
}
$curprices = preg_split('/\s+/', trim($rs['prices']));
foreach($descs as $k => $v) {
	$tmp = str_replace('{price}', $curprices[$k], $v);
	$descs[$k] = explode('|', $tmp);
}

