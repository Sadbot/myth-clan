// JavaScript Document
function Show_window(){
		
	$("#centerunit").append('<div id="parent_popup"><div id="top_popup"><img src="img/top_popup.png"></img></div><div id="popup"><!--Результат проверки регистрации по скрипту registration.js--></div><div id="bottom_popup"><img src="img/bottom_popup.png"></img></div></div>');
	
	var parent_popup=$("#parent_popup"), popup=$("#popup");
	
	parent_popup.fadeIn('slow');
	popup.slideDown('slow');
};

$(document).ready(function(){


	$("#registration").submit(function(){
		Show_window();
		
		

		
	var url = $("#registration").attr("action");


	$.ajax("testreg.php",   {
		data: $("#registration :input").serializeArray(),
	    dataType: "json",
	    success: popup.html('Вы вошли!'),
		error: alert('Ошибка')
	});
		  
	return false;
	});
});