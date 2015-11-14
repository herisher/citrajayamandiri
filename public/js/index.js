$(document).ready(function(){
	setTimeout(scrollTo,100,0,1);
});
$(window).bind("resize load", function(){
	$("html").css("zoom", $(window).width()/320);
});