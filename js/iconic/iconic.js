$(document).ready(function(){
	//homepage tab function for newest jobs block
	$('#tab-control .tab').click(function(){
		$('#tab-control .tab').removeClass('active');
		$(this).addClass('active');
		$('#tab-content .tab-content').fadeOut(200);
		$('#tab-content-' + $(this).data('tab')).fadeIn(200);
	});
	
	//toogle content on parent category click
	$('div.parent.has-child').click(function(e){
		e.preventDefault();
		$('ul',this).toggle();
		if($(this).hasClass('active')){
			$(this).removeClass('active');
		}else{
			$(this).addClass('active');
		}
	});
});
