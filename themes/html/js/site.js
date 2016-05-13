$(document).ready(function() {

	$(".backtop a").click(function(){
		$('body,html').stop().animate({scrollTop:0},600);
		return false;
	});
	$(window).load(function(){
		compareTall(".articleitem_body", ".articleitem_body");
	});
	$(window).resize(function(){
		compareTall(".articleitem_body", ".articleitem_body");
	});
	$('.carousel').carousel({
	  interval: 5000
	});
	$(".carousel-inner").swipe( {
		//Generic swipe handler for all directions
		swipeLeft:function(event, direction, distance, duration, fingerCount) {
			$(this).parent().carousel('next'); 
		},
		swipeRight: function() {
			$(this).parent().carousel('prev'); 
		},
		//Default is 75px, set to 0 for demo so any distance triggers swipe
		threshold:0
	});
	$(".answer li").mouseover(function(){
		$(this).addClass("active");
	});
	$(".answer li").mouseleave(function(){
		$(this).removeClass("active");
	});
	$(".popup").fancybox({
		maxWidth: 600,
		autoResize:true,
		autoCenter:true,
		type:'iframe' 
	});
	$("table tr:even").addClass("even");	
	$(".menu-right").click(function(){
		$("#menu_panel").animate({left:0});
		$(".bg_overlay").fadeIn(100);
		return false;
	});	
	$(".icon-search").click(function(){
		$("#search_panel").animate({right:0});
		$(".bg_overlay").fadeIn(100);
		return false;
	});	
	
	$(".bg_overlay").click(function(){
		$("#menu_panel").animate({left:'-100%'});
		$("#search_panel").animate({right:'-100%'});
		$(this).fadeOut(100);
	});
});     
 
findTall = function( id, item){
	$(item).removeAttr("style");
	var _h = $(id).height();
	$(item).height(_h);
}
compareTall = function(id, parent){
	$(id).removeAttr("style");
	var maxHeight = -1;

   $(id).each(function() {
     maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
   });

   $(parent).each(function() {
     $(this).height(maxHeight);
   });
}