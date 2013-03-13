<?php
require_once ("common.inc.php");
$sql = "select * from #@__line_time where aid='$artid'";
$dsql->SetQuery($sql);
$dsql->Execute("tm");
$dates = array();
while($row = $dsql->GetArray("tm")) {
	$dates[] = $row;
}
//  { start_date:"2013-03-01 00:00" , end_date:"2013-03-01 00:05" , text:"8888" , id:"1362059958165" }
$jsons = array();
if ($dates){
	foreach ($dates as $k => $v) {
		$arr = array(
			'start_date' => $v['godate'] .' 00:00',
			'end_date' => $v['godate'].' 23:59',
			'text' => getFirstPrices($v['prices']),
			'id' => $v['id']
		);
		$jsons[] = $arr;
	}
	echo json_encode($jsons);
} else{
	echo '[]';
}

function getFirstPrices($prices){
	$curprices = preg_split('/\s+/', trim($prices));
	return $curprices[0];
}