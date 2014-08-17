$(document).ready(function() {

$("#changepass_form").submit(function(event) {
    /* stop form from submitting normally */
    event.preventDefault(); 

    $.post('action.php', $('#changepass_form').serialize(), function (data) {
	if (data == "MISMATCH") {
	    $("#message").empty().append("兩次密碼輸入不一致！").addClass('alert-error').fadeOut().fadeIn(); 
	    $("#input03, #input04").parents(".control-group").addClass('error');
	    $("#input04").attr("value","");
	}
	if (data == "WRONG") {
	    $("#message").empty().append("密碼錯誤！").addClass('alert-error').fadeOut().fadeIn();
	    $("#input02").parents(".control-group").addClass('error');
	}
    });
  });




});
