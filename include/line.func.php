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
?>