<?php
/**
 * 分析date to unix 时间戳
 * @param $date
 * @return int
 */
function dateToUnix($date){
	$sd = date_parse($date);
	return mktime(0,0,0,$sd['month'], $sd['day'], $sd['year']);
}

function addDays($date, $days) {
	$unix =  dateToUnix($date) + $days * 86400;
	return date('Y-m-d', $unix);
}

function numtoWeek($num) {
	$weeks = array('星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日');
	return $weeks[$num];
}