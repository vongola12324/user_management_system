<script>

$(document).ready(function(){
    $("#add_user_form_pending").submit(function(){
        $("#submitAddUser").attr("disabled", true);
        $.ajax({
            type: "POST",
            url: "action.php",
            data: $("#add_user_form_pending").serializeArray(),
            dataType: "json",

            success: function(msg){
            if (!msg.status) {
                    $("#message_reg").empty().append(msg.message).addClass('alert-error').fadeOut().fadeIn();
                    $("#submitAddUser").attr("disabled", false);
                }
                else {
                    $("#message_reg").empty().append(msg.message).removeClass('alert-error').addClass('alert-success').fadeOut().fadeIn();
                    $("#submitAddUser").attr("disabled", false);
                }
            
            },
            error: function(){
                alert('傳送表單失敗！');
                $("#submitAddUser").attr("disabled", false);
            }
        });
        //make sure the form doesn't post
        return false;
    });
});

</script>
<h1>註冊帳號</h1>
<p>請先於下方表單填寫申請表，OP 審核後會將結果寄送至您所填寫之信箱。<br/>
遊戲帳號與網站帳號通用。</p>
<p>請<span style="color:red">確保填寫了正確的信箱</span>，若過久都沒有收到回應，請檢查垃圾信件夾。<br/>
也請認真填寫自我介紹，若 OP 認為過於簡略將會退回申請。</p>
<p>雪服總司令部感謝您的配合，並祝您遊玩愉快！</p>
<form class="form-horizontal" action="action.php" method="post" id="add_user_form_pending" >
	<fieldset>
	<legend>增加新使用者</legend>
    <div class="alert" style="display:none;" id="message_reg"></div>
	<div class="control-group">
		<label class="control-label" for="input05">帳號</label>
		<div class="controls">
			<input type="text" name="useradd" id="input05"/>
            <span class="help-block">用來登入遊戲的帳號</span>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="input06">密碼</label>
		<div class="controls">
			<input type="password" name="passadd" id="input06" />
            <span class="help-block">用來登入遊戲的密碼，長度限制為 4~20</span>
		</div>
	</div>
    
    <div class="control-group">
		<label class="control-label" for="input11">E-mail</label>
		<div class="controls">
			<input type="text" name="email" id="input11" />
		</div>
	</div>

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
            <span class="help-block">若無 BBS 請填寫 N/A</span>
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
        <button type="reset" class="btn btn-large">清除重填</button>
		<button type="submit" class="btn btn-large btn-primary" id="submitAddUser">送出申請</button>
	</div>

	<input type="hidden" name="action" value="add_new_user_pending" />
	</fieldset>
</form>

