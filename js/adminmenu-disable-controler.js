$(function(){
	$('.adminmenu-disable-controler-open').on('click',function(e){
		e.preventDefault();
		var _className = $(this).attr('href').replace('#','');
		$('.'+_className).toggle();
	});
	$('.adminmenu-disable-controler-open-all-check').on('click',function(e){
		e.preventDefault();
		var _targetClass = $(this).attr('href').replace('#','');
		$('.'+_targetClass).each(function(){
			$(this)[0].checked = true;
		});
	});
	$('.adminmenu-disable-controler-open-all-uncheck').on('click',function(e){
		e.preventDefault();
		var _targetClass = $(this).attr('href').replace('#','');
		$('.'+_targetClass).each(function(){
			$(this)[0].checked = false;
		});
	});
})