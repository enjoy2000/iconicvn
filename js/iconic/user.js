(function($){
	$(document).ready(function(){
		//set css for center content div
		var left = $(window).width()/2 - $('#account-page').outerWidth()/2;
		var height= $('#account-page').height() + 100 + 40;
		$('#account-page').css('left',left);
		$('#header-landing').css('height',height);
		
		//style for select placeholder
		$('select').each(function(){
			$(this).change(function () {
			    if($(this).val() == "") $(this).css('color', '#A6A6A6');
			    else $(this).css("color","black");
			});
			
			$(this).change();
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
