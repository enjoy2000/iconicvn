(function($){
	$(document).ready(function(){
		//style for select placeholder
		$('select').each(function(){
			var go = false;//switch off
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
	});
})(jQuery);
