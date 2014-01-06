(function($){
	$(document).ready(function(){
		//set css for center content div
		var left = $(window).width()/2 - $('#account-page').outerWidth()/2;
		var height= $('#account-page').height() + 100 + 40;
		$('#account-page').css('left',left);
		$('#header-landing').css('height',height);
	});
})(jQuery);
