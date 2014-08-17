<?php require('config.php'); ?>
<script>
$(document).ready(function() {

$("#edit_form").submit(function(event) {
    event.preventDefault(); 

    $.post('action.php', $('#edit_form').serialize(), function (data) {
	if (data == "WRONG") {
	    $("#message").empty().append("密碼錯誤！").addClass('alert-error').fadeOut().fadeIn();
	    $("#input06").attr("value","").parents(".control-group").addClass('error');
	}
	if (data == "EMPTY") {
	    $("#message").empty().append("有東西沒有填喔！").addClass('alert-error').fadeOut().fadeIn();
	    $("#input06").parents(".control-group").removeClass('error');
	}
	if (data == "SUCCESS") {
	    $("#message").empty().append("資料修改成功！").removeClass('alert-error').addClass('alert-success').fadeOut().fadeIn();
	    $("#input06").parents(".control-group").removeClass('error');
	}
    });
  });

});
</script>

<h1>編輯資料</h1>

<p>說明：</p>
<p>每一個欄位皆要填寫！</p>

<form class="form-horizontal" action="action.php" method="post" id="edit_form" >
	<fieldset>
	<legend>驗證身份</legend>
	<div class="control-group">
		<label class="control-label" for="input05">帳號</label>
		<div class="controls">
			<?=$login['username']?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="input06">密碼</label>
		<div class="controls">
			<input type="password" name="pass" id="input06" />
		</div>
	</div>
	<legend>編輯個人資料</legend>
	<div class="control-group">
		<label class="control-label" for="input07">暱稱</label>
		<div class="controls">
			<input type="text" name="nick" id="input07" />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="input08">BBS ID</label>
		<div class="controls">
			<input type="text" name="bbs" id="input08" />
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="input09">介紹人</label>
		<div class="controls">
			<input type="text" name="ref" id="input09" />
		</div>
	</div>	

	<div class="control-group">
		<label class="control-label" for="input10">自我介紹</label>
		<div class="controls">
			<input type="text" name="intro" id="input10" />
		</div>
	</div>	
	
	
	<div class="form-actions">
		<button type="submit" class="btn btn-primary" id="submitEdit">送出資料</button>
	</div>

	<input type="hidden" name="action" value="edit" />
	</fieldset>
</form>

