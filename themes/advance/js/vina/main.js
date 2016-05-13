(function($){
	$(document).ready(function() {
		
		if($(".nav-icon").length != 0){
			$('.nav-icon').click(function(){
				$(this).parent().toggleClass("open");
				return false;
			});
		}
		if($(".show-popup").length != 0){
			$('.show-popup').magnificPopup({
				type: 'inline',
				preloader: false          
			});
		}
//                $('body').click(function(){
//                    $(".nav-menu").removeClass("open");
//                    return false;
//               });
	});
        
})(jQuery);






