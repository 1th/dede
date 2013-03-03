jQuery(function($){
	$.datepicker.regional['zh-CN'] = {
		closeText: '关闭',
		prevText: '&#x3c;上月',
		nextText: '下月&#x3e;',
		currentText: '今天',
		monthNames: ['一月','二月','三月','四月','五月','六月',
			'七月','八月','九月','十月','十一月','十二月'],
		monthNamesShort: ['一','二','三','四','五','六',
			'七','八','九','十','十一','十二'],
		dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
		dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
		dayNamesMin: ['日','一','二','三','四','五','六'],
		weekHeader: '周',
		dateFormat: 'yy-mm-dd',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: true,
		yearSuffix: '年'};
	$.datepicker.setDefaults($.datepicker.regional['zh-CN']);
});

$(function() {
	$( "#start" ).datepicker({
//		defaultDate: +1,
		onSelect:function(dateText,inst){
			$("#end").datepicker("option","minDate",dateText);
		}
	});
	$('#end').datepicker({
		onSelect:function(dateText,inst){
			$("#start").datepicker("option","maxDate",dateText);
		}
	})
});
function addQuickDates(){
	if (!$('#start').val() || !$('#end').val()) {
		alert('请选择日期');
	}
	if (!$.trim($('#wkprices').val())){
		alert('请选择时间范围');
	}
	scheduler.addEvent({
		start_date: "1984-03-16",
		end_date: "1984-03-19",
		text:"Some",
		custom_data:"some data"
	});
//	/*
	$.post("line_full.php?artid="+_project.artid+"&do=update_bj", {
		startDate:$('#start').val(),
		endDate:$('#end').val(),
		wkprices:$.trim($('#wkprices').val()),
		week:$('#week').val(),
		ser:scheduler.toJSON()
	}, function(data){
		if(data == 1) {
			scheduler.clearAll();
			scheduler.load("line_full.php?artid="+_project.artid+"&do=load_json", 'json');
		}
	})
//	*/
}
function checkSubmit(){
	var ser = scheduler.toJSON();
	$('#scheduler').val(ser);
	return true;
}
function showTime(){
	$('#head_time').attr('background', 'images/itemnote1.gif');
	$('#head_days').attr('background', 'images/itemnote2.gif');
	$('#content_time').show();
	$('#content_days').hide();
}
function showDays(){
	$('#head_days').attr('background', 'images/itemnote1.gif');
	$('#head_time').attr('background', 'images/itemnote2.gif');
	$('#content_time').hide();
	$('#content_days').show();
}

function addJourney(){
	var editor = CKEDITOR.instances.journey;
	var thead = '<table border="0" cellpadding="1" cellspacing="1" style="width: 660px;background:url(../templets/default/image/bg_show_detail_title.jpg)">'
		+'<tr>'
		+'<td style="width: 65px;text-align: center;font-weight: bold;color: #fff;margin-right: 8px;font-size:14px;" width="65">'
		+' 第&nbsp;天</td>'
		+ '<td style="font-size:14px;" width="400">'
		+'&nbsp;</td>'
		+'<td align="right" style="font-size:12px;font-weight:bold;">'
		+'</td>'
		+'</tr>'
		+'</table>'
		+'<div class="inner-content"><p>在此填写新一段行程的介绍...</p></div>';
	editor.setData(editor.getData()+thead);
}