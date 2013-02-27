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
		titleBar:{titleBar_height:30,titleBar_bgColor:"#08355c",titleBar_alpha:0.5},
		titleFont:{TitleFont_size:12,TitleFont_color:"#FFFFFF",TitleFont_weight:"normal"},
		btn:{btn_bgColor:"#FFFFFF",btn_bgHoverColor:"#1072aa",btn_fontColor:"#000000",
			btn_fontHoverColor:"#FFFFFF",btn_borderColor:"#cccccc",
			btn_borderHoverColor:"#1188c0",btn_borderWidth:1}
	});
	$('.removeshugang').find('.shugang:eq(-1)').hide();
})