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
$(function(){
	// login
	$.get(project.global.cfg_cmspath+'/member/ajax_logintop.php', {}, function(data){
		if (data) {
			$('#_login').html(data);
		}
	})
	// banner
	$("#KinSlideshow").KinSlideshow({
		moveStyle:"right",
		isHasTitleBar:false,
		isHasTitleFont:false,
		btn:{btn_bgColor:"#FFFFFF",btn_bgHoverColor:"#1072aa",btn_fontColor:"#000000",
			btn_fontHoverColor:"#FFFFFF",btn_borderColor:"#cccccc",
			btn_borderHoverColor:"#1188c0",btn_borderWidth:1}
	});
	$('.removeshugang').find('.shugang:eq(-1)').hide();
	$('#menu_'+project.typeid).addClass('active');
})

$(function(){
	$('#jp_line, #tj_line').find('li:first').addClass('active');
	$('#jp_line, #tj_line').find('li').hover(function(){
		$(this).parent('ul').find('li').removeClass('active');
		$(this).addClass('active');
	})
	$( "#s_startdate" ).datepicker({
		onSelect:function(dateText,inst){
			$("#s_enddate").datepicker("option","minDate",dateText);
		},
		defaultDate:new Date()

	});
	$('#s_enddate').datepicker({
		onSelect:function(dateText,inst){
			$("#s_startdate").datepicker("option","maxDate",dateText);
		}
	});
});

function xdChangeAndLocation(param, value, tripFile) {
	var stringObj = window.location.href;
	if (typeof tripFile == 'undefined'){
		tripFile = window.location.pathname;
	}
	var lstr = "&";

	if (stringObj.indexOf(tripFile+'?') == -1) {
		lstr = "?";
	}
	stringObj = stringObj.replace(/PageNo=[0-9]*/, '');
	var reg = new RegExp(param + "=[0-9a-zA-Z,-]*", "g"); //创建正则RegExp对象
	var urlGo = "";
	var ch = stringObj.indexOf(param+'=');
	if (ch == -1) {
		urlGo = urlGo + stringObj + lstr + param + "=" + value;
	}
	if (ch != -1) {
		urlGo = stringObj.replace(reg, param + "=" + value);
	}
	window.location = urlGo;
}

function l(p, v, f){
	xdChangeAndLocation(p, v, f);
}

function prebook($date){
	xdChangeAndLocation('godate', $date);
}

function book(aid, godate) {
//	alert(aid);

	$.getJSON(project.global.cfg_cmspath+'/plus/bookAjax.php?aid='+aid+'&godate='+godate, {}, function(data){
		var str = '<select id="book">';
		if (data.isOk) {
			for(var i in data.date) {
				var item = data.date[i];
				var strSelect = '';
				if (godate == item.godate) {
					strSelect = " selected='selected' ";
				} else {
					strSelect = '';
				}
				str += "<option value='"+item.godate+"'"+strSelect+">"+item.godate+"["+item.week+"]</option>";
			}
			str += '</select>';
			$('#book_select_w').html(str);
			$( "#dialog-book" ).dialog({
				height:180,
				width:380,
				draggable:false,
				modal: true
			});
			$('#button').click(function(){
				window.location.href=project.global.cfg_cmspath+'/plus/book.php?aid='+aid+'&godate='+$('#book').val();
			})
		}
	})
//	window.location.href=project.global.cfg_cmspath+'/plus/book.php?aid='+aid+'&godate='+godate;
}

// book
function bookLogin(){
	var taget_obj = document.getElementById('_book_userlogin');
	myajax = new DedeAjax(taget_obj,false,false,'','','');
	myajax.SendGet2(project.global.cfg_cmspath + "/member/ajax_loginbook.php");
	DedeXHTTP = null;
}

function calPrice(){
	var price = 0;
	$('.spinner').each(function(i, ele){
		var na = $(ele).attr('price');
		var pr = $('#'+na).val();
		price += $(ele).val()*pr;
		parseInt(price);
	})
	// 房价差
	if ($('input[name=hotelpricesel]:checked').val() != 0) {
		price += parseInt($('#hotelprice').val());
	}
	$('#book_price').html('￥:'+price+'元');
}
function calInfos(){
	var info =
	'<tr class="info">'+
		'<td><input type="text" name="name[]"/></td>'+
		'<td><select name="sex[]" >'+
			'<option value="男">男</option>'+
			'<option value="女">女</option>'+
			'</select></td>'+
		'<td style="text-align: center;"><input style="width:360px" type="text" name="id[]"/></td>'+
		'<td><input style="width:200px" type="text" name="tel[]"/></td>'+
	'</tr>';
	var num = 0;
	$('.spinner').each(function(i, ele){
		num += parseInt($(ele).val());
	})
	var curnum = $('#infos').find('.info').length;
	var gutter = num-curnum;
	if (gutter<0) {
		for(var i= 0;i<Math.abs(gutter);i++) {
			$('#infos').find('.info').eq(-1).remove();
		}
	}
	if (gutter>0) {
		for(var j= 0;j<Math.abs(gutter);j++) {
			$('#infos').append(info);
		}
	}
}


function checkBook(){
	var f;
	f = $('#truename').val();
	if (!f) {
		alert('请填写联系人姓名!');
		return false;
	}
	f = $('#telphone').val();
	if (!f) {
		alert('请填写联系电话!');
		return false;
	}
	f = $('#idno').val();
	if (!f) {
		alert('请填写联系人身份信息!');
		return false;
	}
	if (!$('#p_0').val()){
		alert('请填写报名人数!');
		return false;
	}
	return true;
}