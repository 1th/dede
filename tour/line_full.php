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
		// 删除之前的
		$sql = "delete from `#@__line_time` where aid = '$artid'";
		$dsql->ExecuteNoneQuery($sql);
		// 插入现在的数据
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
				    journey='$journey'
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
		// 现在数据库中的.
		$sql = "select aid, godate , prices, id from  `#@__line_time` where aid={$artid}";
		$dsql->Execute('me', $sql);
		$result = array();
		while($arr = $dsql->GetArray('me')){
			$result[] = array(
				'start_date' => $arr['godate'].' 00:00',
				'end_date' => $arr['godate'].' 23:59',
				'text' => $arr['prices'],
				'id' => $arr['id']
			);
		}
		echo json_encode($result);
	break;
	case 'update_bj':
		// 现在数据库中的.
		$sql = "select godate , prices, id from  `#@__line_time` where aid={$artid}";
		$dsql->Execute('me', $sql);
		$result = array();
		while($arr = $dsql->GetArray('me')){
			$result[] = $arr;
		}

		// 批量添加的
		$sd = date_parse($startDate);
		$startTime = mktime(0,0,0,$sd['month'], $sd['day'], $sd['year']);
		$ed = date_parse($endDate);
		$endTime = mktime(0,0,0,$ed['month'], $ed['day'], $ed['year']);

		$events = array();
		while ($startTime < $endTime){
			if (date('w', $startTime) == $week) {
				$events[] = array(
					'godate' => date('Y-m-d', $startTime),
					'prices' => $wkprices,
					'id' => $startTime.rand(100,999)
				);
			}
			$startTime += 86400;
		}

		// 覆盖数据库获取到的用户数据
		foreach($result as $k => $v){
			foreach($events as $ek=> $ev){
				if ($v['godate'] == $ev['godate']){
					$result[$k] = $ev;
					unset($events[$ek]);
				}
			}
		}
		$result = array_merge($result, $events);

		// 获取手动添加的数据, 忽略掉据此一个月之前的数据
		preg_match_all('/(\d{4}-\d{2}-\d{2}) 00:00.*text:\\\"(.*?)\\\".*(\d{13}).*/', $ser, $matches, PREG_SET_ORDER);
		$sers = array();
		foreach($matches as $k => $v) {
			if (date('Y-m-d', time()-24*60*60*30) <= $v[1]) {
				$sers []= array(
					'godate' => $v[1],
					'prices' => $v[2],
					'id' => $v[3]
				);
			}
		}

		// 手动添加的不会被复写
		foreach($result as $k => $v){
			foreach($sers as $sk=> $sv){
				if ($v['godate'] == $sv['godate']){
					$result[$k] = $sv;
					unset($sers[$sk]);
				}
			}
		}

		$result = array_merge($result, $sers);

		// 更新数据库数据
		$sql = "delete from `#@__line_time` where aid = '$artid'";
		$dsql->ExecuteNoneQuery($sql);
		$sql = "insert into `#@__line_time`(aid, godate, prices, id) values";
		foreach($result as $k => $v) {
			$sql .= "({$artid}, '{$v['godate']}', '{$v['prices']}', '{$v['id']}'),";
		}
		$sql = rtrim($sql, ',');
		if ($dsql->ExecuteNoneQuery($sql)){
			exit( '1');
		} else {
			exit('0');
		}
	break;
}
