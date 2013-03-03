<?php
/**
 * 文档发布
 *
 * @version        $Id: line_full.php 1 8:26 2010年7月12日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__).'/config.php');
CheckPurview('a_New,a_AccNew');
error_reporting(E_ALL);
if(empty($do)) $do = '';
$line = $dsql->GetOne("SELECT * FROM `#@__line` WHERE aid='$artid'");
$channelid = 17;
// addhandle
switch ($do) {
	case 'ext_add':
		include DedeInclude('templets/line_full_add.htm');
	break;
	case 'ext_edit':
		include DedeInclude('templets/line_full_edit.htm');
	break;
	case 'ext_update':
		// 时间关联
		$sql = "delete from `#@__line_time` where aid = '$artid'";
		$dsql->ExecuteNoneQuery($sql);
		$sql = "insert into `#@__line_time`(aid, godate, prices, id) values";
		preg_match_all('/(\d{4}-\d{2}-\d{2}) 00:00.*text:\\\"(.*?)\\\".*(\d{13}).*/', $scheduler, $matches, PREG_SET_ORDER);
		foreach($matches as $k => $v) {
			if (date('Y-m-d') <= $v[1]) {
				$sql .= "($artid, '$v[1]', '$v[2]', '$v[3]'),";
			}
		}
		$sql = rtrim($sql, ',');
		$dsql->ExecuteNoneQuery($sql);

		// 内容存储
		$query = "update `#@__line`
				  set
				    per_description='$per_description',
				    price='$price',
				    days='$days',
				    full_people = '$full_people',
				    memberprice='$memberprice',
				    start_trans='$start_trans',
				    available_people='$available_people',
				    back_trans='$back_trans',
				    jdbz='$jdbz',
				    tbtx='$tbtx',
				    wxts='$wxts',
				    plan='$plan',
				    journey='$journey',
				    bj='$scheduler'
				  where aid ='$artid'";

		if($dsql->ExecuteNoneQuery($query)){
			$msg = "请选择你的后续操作：
			    <a href='archives_add.php?cid={$line['typeid']}'><u>继续发布文档</u></a>
			    &nbsp;&nbsp;
			    <a href='archives_edit.php?aid={$line['aid']}'><u>更改文档</u></a>
			    &nbsp;&nbsp;
			    <a href='catalog_do.php?cid={$line['typeid']}&do=listArchives'><u>已发布文档管理</u></a>
			    &nbsp;&nbsp;";
			$msg = "<div style=\"line-height:36px;height:36px\">{$msg}</div>";
			$wintitle = '成功发布文档！';
			$wecome_info = '文档管理::发布文档';
			$win = new OxWindow();
			$win->AddTitle('成功发布文档：');
			$win->AddMsgItem($msg);
			$winform = $win->GetWindow('hand', '&nbsp;', false);
			$win->Display();
		}
	break;
	case 'load_json':
		echo $line['bj'];
	break;
	case 'update_bj':
		$sd = date_parse($startDate);
		$startTime = mktime(0,0,0,$sd['month'], $sd['day'], $sd['year']);
		$ed = date_parse($endDate);
		$endTime = mktime(0,0,0,$ed['month'], $ed['day'], $ed['year']);
		$events = array();
		while ($startTime < $endTime){
			if (date('w', $startTime) == $week) {
				$events[] = array(
					'start_date' => date('Y-m-d H:i', $startTime),
					'end_date' => date('Y-m-d H:i', $startTime+300),
					'text' => $wkprices,
					'id' => $startTime.rand(100,999)
				);
			}
			$startTime += 86400;
		}

		$bj = str_replace(
			array('start_date:', 'end_date:', 'text:', 'id:'),
			array('"start_date":', '"end_date":', '"text":', '"id":'),
			stripslashes($line['bj'])
		);

		$bj = !empty($bj) ? $bj : $line['bj'];
		$bjobjs = json_decode($bj);

		$evobjs = json_decode(json_encode($events));
		if (!empty($bjobjs)) {
			// 去重操作
			foreach($bjobjs as $k => $sbj) {
				foreach($evobjs as $n=> $sev) {
					if ($sev->start_date == $sbj->start_date){
						$bjobjs[$k] = $sev;
						unset($evobjs[$k]);
					}
				}
			}
			if ($evobjs) {
				foreach($evobjs as $n=> $sev) {
					$bjobjs[] = $sev;
				}
			}

		} else {
			$bjobjs = $evobjs;
		}
		$bjjson = json_encode($bjobjs);
		$query = "update `#@__line`
				  set bj='$bjjson'
				  where aid ='$artid'";
		if($dsql->ExecuteNoneQuery($query)){
			echo '1';
		} else {
			echo '0';
		}
	break;
}
