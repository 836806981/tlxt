$(function(){
	var togbtn = $('.tab_tle>span.togbtn');
	
	togbtn.parent('.tab_tle').siblings('.auntTab').hide();
	
	togbtn.toggle(function(){
		$(this).html('收起&and;').parent('.tab_tle').siblings('.auntTab').slideDown();
	},function(){
		$(this).html('展开&or;').parent('.tab_tle').siblings('.auntTab').slideUp();
	});
});
