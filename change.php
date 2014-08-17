<?php require('config.php'); ?>
<script>
$(document).ready(function() {

    $("#changepass_form").submit(function(event) {
        /* stop form from submitting normally */
        event.preventDefault();

        $.post('action.php', $('#changepass_form').serialize(), function (data) {
            if (data == "MISMATCH") {
                $("#message").empty().append("兩次密碼輸入不一致！").addClass('alert-error').fadeOut().fadeIn();
                $("#input03, #input04").parents(".control-group").addClass('error');
                $("#input01, #input02").parents(".control-group").removeClass('error');
                $("#input04").attr("value","");
            }
            if (data == "WRONG") {
                $("#message").empty().append("密碼錯誤！").addClass('alert-error').fadeOut().fadeIn();
                $("#input01, #input02").parents(".control-group").addClass('error');
            }
            if (data == "SUCCESS") {
                $("#message").empty().append("密碼更改成功！").removeClass('alert-error').addClass('alert-success').fadeOut().fadeIn();
                $("#input01, #input02, #input03, #input04").parents(".control-group").removeClass('error');
            }
        });
    });

});
</script>
<h1>更換密碼</h1>

<p>說明：</p>
<p>密碼長度限制為 4~20 個字元，未按照正確格式輸入會產生錯誤。</p>

<form class="form-horizontal" action="action.php" method="post" id="changepass_form" >
        <fieldset>
        <legend>更換密碼</legend>

        <div class="control-group">
                <label class="control-label" for="input01">帳號</label>
                <div class="controls">
                        <?=$login['username']?>
                </div>
        </div>

        <div class="control-group">
                <label class="control-label" for="input02">舊密碼</label>
                <div class="controls">
                        <input type="password" name="oldpass" id="input02" />
                </div>
        </div>

        <div class="control-group">
                <label class="control-label" for="input03">新密碼</label>
                <div class="controls">
                        <input type="password" name="newpass1" id="input03" />
                </div>
        </div>

        <div class="control-group">
                <label class="control-label" for="input04">確認密碼</label>
                <div class="controls">
                        <input type="password" name="newpass2" id="input04" />
                </div>
        </div>

        <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="submitChangePass">更改密碼</button>
        </div>

        <input type="hidden" name="action" value="change" />
        </fieldset>
</form>
