(function($){
	$(document).ready(function(){
		//style for select placeholder
		$('select').each(function(){
			var go = true;
			doit($(this));//switch off
			$(this).on('fosus active click',function() {//on focus/active
				$(this).css('color','black');
			    doit($(this));//do it
			}).on('change', function() {
			    go=true;//go
			    doit($(this));//and doit
			}).on('blur', function() {doit($(this)); go = false; });//switch off on blur
			
			function doit(el) {
			    if (go) {//if go
			        //do stuff here with el.val()
			        if(el.val()==''){
			        	el.css('color','#A6A6A6');
			        }else{
			        	el.css('color','black');
			        }
			        go = false;//switch off
			    } else {go=true;}//else go
			}
		});
		//$('select').selectric();
		
		//dropdown menu
		$('ul li.has-child').hover(
			function(){
				$('ul.sub', this).stop(true, true).slideDown();
			}, function(){
				$('ul.sub', this).stop().slideUp();
			}
		);
		
		//set position for breadcrumbs
		$('#breadcrumbs .breadcrumbs').css('left', 0);
		
		//show loading box when submiting form
		$('form').on('submit', function(){
			$('#loading-animation').fadeIn(500);
		});
		
		//contact form email footer
		$('#contact-us').on('submit', function(e){
			e.preventDefault();
			$.ajax({
				url: '/job/index/contact',
				type: 'POST',
				data: $(this).serialize(),
				success: function(msg){
					$('#response').show().text(msg);
					$('#contact-us input').val('');
					$('#contact-us textarea').val('');
					$('#loading-animation').fadeOut(200);
				}
			});
		});
	});
})(jQuery);
