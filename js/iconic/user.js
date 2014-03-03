(function($){
	$(document).ready(function(){
		//set css for center content div
		var left = $(window).width()/2 - $('#account-page').outerWidth()/2;
		var height= $('#account-page').height() + 100 + 40;
		$('#account-page').css('left',left);
		$('#header-landing').css('height',height);
		
		$('select').click(function(){
			$(this).css('color','#000');
		});
		
		//toogle content on parent category click
		$('div.parent.has-child > a').click(function(){
			e.preventDefault();
			$(this).parent().find('ul').toggle();
			if($(this).parent().hasClass('active')){
				$(this).parent().removeClass('active');
			}else{
				$(this).parent().addClass('active');
			}
			return;
		});
	});
})(jQuery);
