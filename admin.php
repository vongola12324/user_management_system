<?php 
require('config.php');

if ( $login['admin'] != true || !isset($login['admin']) )
{
?>
<script>
	alert('很抱歉，您不是管理員並不能進行此項操作。');
	loadTab($last_tab);
</script>
<?php
}
else if ($login['admin'] == true)
{
?>

<script>

$(document).ready(function(){
    $("#pending_registration_form").submit(function(){
        $("#submitApproveUser").attr("disabled", true);
        $.ajax({
            type: "POST",
            url: "action.php",
            data: $("#pending_registration_form").serializeArray(),
            dataType: "json",

            success: function(msg){
            if (!msg.status) {
                    $("#message").empty().append(msg.message).addClass('alert-error').fadeOut().fadeIn();
                    $("#submitApproveUser").attr("disabled", false);
                }
                else {
                    $("#message").empty().append(msg.message).removeClass('alert-error').addClass('alert-success').fadeOut().fadeIn();
                    $("#submitApproveUser").attr("disabled", false);
                }
            
            },
            error: function(){
                alert('傳送表單失敗！');
                $("#submitApproveUser").attr("disabled", false);
            }
        });
        //make sure the form doesn't post
        return false;
    });
});

$(document).ready(function(){
    $("#adminChangePassForm").submit(function(){
        $("#submitAdminChangePass").attr("disabled", true);
        $.ajax({
            type: "POST",
            url: "action.php",
            data: $("#adminChangePassForm").serializeArray(),
            dataType: "json",

            success: function(msg){
            if (!msg.status) {
                    $("#message").empty().append(msg.message).addClass('alert-error').fadeOut().fadeIn();
                    $("#submitAdminChangePass").attr("disabled", false);
                }
                else {
                    $("#message").empty().append(msg.message).removeClass('alert-error').addClass('alert-success').fadeOut().fadeIn();
                    $("#submitAdminChangePass").attr("disabled", false);
                }
            
            },
            error: function(){
                alert('傳送表單失敗！');
                $("#submitAdminChangePass").attr("disabled", false);
            }
        });
        //make sure the form doesn't post
        return false;
    });
});



/*$(document).ready(function() {
$("#pending_registration_form").submit(function(event) {
    event.preventDefault(); 

    $.post('action.php', $('#pending_registration_form').serialize(), function (data) {
	if (data == "ERROR") {
	    $("#admin_message").empty().append("發生錯誤！").addClass('alert-error').fadeOut().fadeIn();
	}
	if (data == "EMPTY") {
	    $("#admin_message").empty().append("有東西沒有填喔！").addClass('alert-error').fadeOut().fadeIn();
	}
	if (data == "SUCCESS") {
	    $("#admin_message").empty().append("使用者新增成功！").removeClass('alert-error').addClass('alert-success').fadeOut().fadeIn();
	}
    });
  });

});

function verify() {
    alert('hi');
    return false;
}*/

</script>
<form class="form-horizontal" method="post" id="pending_registration_form">
<h1>帳號審核</h1>
<h2>待審核帳號</h2>
<table class="table table-condensed table-bordered table-striped">
	<thead>
        <th>帳號</th><th>暱稱</th><th>BBS ID</th><th>email</th><th>介紹人</th><th>時間</th><th>IP</th>
		<th>自我介紹</th><th>選擇</th>
	</thead>
	<tbody>
        <?php
        $reglist = get_register_list();
        while( $reg = mysql_fetch_array($reglist) ) {
            echo "<tr>";
            echo "<td>".$reg['username']."</td>";
            echo "<td>".$reg['nick']."</td>";
            echo "<td>".$reg['bbs']."</td>";
            echo "<td>".$reg['email']."</td>";
            echo "<td>".$reg['ref']."</td>";
            echo "<td>".date("Y/m/d h:i",$reg['time'])."</td>";
			echo "<td>".$reg['ip']."</td>";
            echo "<td>".$reg['intro']."</td>";
            echo "<td><input type=\"radio\" name=\"selection\" value=\"".$reg['username']."\">";
            echo "</tr>
";
        }
        ?>
    </tbody>
</table>

<h2>動作</h2>
	<fieldset>
    
    <div class="control-group">
        <label class="control-label" for="approve">通過與否</label>
        <div class="controls">
            <label class="radio inline " for="approve_yes">
                <input type="radio" name="approve" value="yes" id="approve_yes" >同意
            </label>
            <label class="radio inline " for="approve_no">
                <input type="radio" name="approve" value="no" id="approve_no" checked="checked" >拒絕
            </label>
            &nbsp;&nbsp;<input type="text" name="reason" id="reason_text" value="填寫資料不符" />
            <label class="radio inline " for="approve_delete">
                <input type="radio" name="approve" value="delete" id="approve_delete" >刪除
            </label>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-large btn-primary" id="submitApproveUser">送出</button>
    </div>
	<input type="hidden" name="action" value="approve_user" />
    </fieldset>
</form>

<h2>更改使用者密碼</h2>
<form class="form-horizontal" action="action.php" method="post" id="adminChangePassForm" >
	<fieldset>
	<legend>輸入新密碼</legend>
	<div class="control-group">
		<label class="control-label" for="input05">帳號</label>
		<div class="controls">
			<input type="text" name="user" id="input05" />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="input06">密碼</label>
		<div class="controls">
			<input type="password" name="pass" id="input06" />
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" class="btn btn-primary" id="submitAdminChangePass">送出資料</button>
	</div>
	<input type="hidden" name="action" value="adminChangePass" />
	</fieldset>
</form>


<?php } ?>
