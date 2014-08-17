<?php
require('config.php');
$action = isset($_GET['act']) ? $_GET['act'] : "login";
switch ($action)
{
	case 'logout':
		$_SESSION['login'] = array( 'logined' => false );
		print 'SUCCESS';
		break;
	case 'login_do':
		if ( !isset($_POST['username']) || $_POST['username'] == '' || !isset($_POST['password']) || $_POST['password'] == '' )
			print 'EMPTY';
		else if ( !check_password_db($_POST['username'], $_POST['password']) )
			print 'WRONG';
		else
		{
			$_SESSION['login'] = array(
				'logined' => true,
				'username' => $_POST['username'],
				'nickname' => get_user_nickname($_POST['username']),
				'admin' => check_op($_POST['username'])
			);
			print 'SUCCESS';
		}
		break;
	case 'login':
	default:
?>
<div class="navbar-inner" style="border-radius: 0;">
	<h3 style="color: #FFF;">登入雪服帳號管理系統</h3>
</div>
<div class="main">
<h4>請使用您在遊戲中的帳號密碼登入</h4>
	<div class="alert" id="message"></div>
	<form class="form-horizontal" action="login.php?act=login_do" method="post" id="login_form">
		<div class="control-group">
			<label class="control-label" for="username">帳號</label>
			<div class="controls">
				<input type="text" name="username" id="username"/>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="password">密碼</label>
			<div class="controls">
				<input type="password" name="password" id="password"/>
			</div>
		</div>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary" id="submitEdit">&nbsp;登入&nbsp;</button>
		</div>
	</form>
</div>
<script>
$("#login #container").css({
	left: (($('#login').innerWidth() - $('#login #container').outerWidth()) / 2) + "px",
	top: (($('#login').innerHeight() - $('#login #container').outerHeight()) / 2) + "px"
});
$("#login_form").submit(function(event) {
	event.preventDefault();

	$.post('login.php?act=login_do', $('#login_form').serialize(), function (data) {
		if (data == "WRONG") {
			$("#login #message").empty().append("密碼錯誤！").addClass('alert-error').fadeOut().fadeIn();
			$("#password").attr("value","").parents(".control-group").addClass('error');
		}
		if (data == "EMPTY") {
			$("#login #message").empty().append("有東西沒有填喔！").addClass('alert-error').fadeOut().fadeIn();
			$("#username").parents(".control-group").removeClass('error');
		}
		if (data == "SUCCESS") {
			window.location.reload(true);
		}
	});
});
</script>
<?php
		break;
}
?>
