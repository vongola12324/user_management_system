<?php
require('config.php');
$action = 'view';
if ( isset($_GET['act']) )
  $action = $_GET['act'];
switch ( $action )
{
	case 'upload':
		if ( !isset($_GET['type']) || $_GET['type'] == "" )
			$type = "skin";
		else
			$type = $_GET['type'];
		if ( !isset($_FILES[$type.'File']) || $_FILES[$type.'File']['error'] == 4 )
			print "EMPTY";
		if ( $_FILES[$type.'File']['error'] == 1 || $_FILES[$type.'File']['error'] == 2 )
			print "WRONG:SIZE";
		else if ( $_FILES[$type.'File']['error'] != 0 )
			print "WRONG";
		else
		{
			$tmpfile = $_FILES[$type.'File']['tmp_name'];
			$imgSize = getimagesize($tmpfile);
			if ( exif_imagetype($tmpfile) != IMAGETYPE_PNG )
				print "WRONG:TYPE";
			else if ( $imgSize[0] != 64 || $imgSize[1] != 32 )
				print "WRONG:SIZE_R";
			else
			{
				$fp = fopen($tmpfile, "rb");
				$data = addslashes(fread($fp, filesize($tmpfile)));
				if ( $type == "skin" )
					$result = change_user_skin($login['username'], $data);
				else
					$result = change_user_skin_cloak($login['username'], $data);
				if ( $result )
					print 'SUCCESS';
				else
					print 'WRONG';
				fclose($fp);
				@unlink($tmpfile);
			}
		}
		break;
	case 'remove':
		return remove_user_skin($login['username']) ? 'SUCCESS' : 'WRONG';
		break;
	case 'removeCloak':
		return remove_user_skin_cloak($login['username']) ? 'SUCCESS' : 'WRONG';
		break;
  case 'load':
    $username = isset($_GET['username']) ? $_GET['username'] : "";
    if ( $username == "" )
      header("Loaction: http://s3.amazonaws.com/MinecraftSkins/.png");
    if ( get_user_skin_uploaded($username) ) {
      header("Content-type: image/png");
      print get_user_skin_data($username);
      exit();
    }
    header("Location: http://s3.amazonaws.com/MinecraftSkins/".$username.".png");
    break;
  case 'loadCloak':
    $username = isset($_GET['username']) ? $_GET['username'] : "";
    if ( $username == "" )
      header("Loaction: http://s3.amazonaws.com/MinecraftCloaks/.png");
    if ( get_user_skin_cloak_uploaded($username) ) {
      header("Content-type: image/png");
      print get_user_skin_cloak_data($username);
      exit();
    }
    header("Location: http://s3.amazonaws.com/MinecraftCloaks/".$username.".png");
		break;
	case 'loadHead':
		$username = isset($_GET['username']) ? $_GET['username'] : "";
		header("Content-type: image/png");
		if ( $username == "" || !get_user_skin_uploaded($username) )
			echo file_get_contents('./img/skin_head_default.png');
		else
		{
			$gd = imagecreatefromstring(get_user_skin_data($username));
			$rtn = imagecreate(32, 32);
			imagecopyresized($rtn, $gd, 0, 0, 8, 8, 32, 32, 8, 8);
			imagepng($rtn); // output
			imagedestroy($gd);
			imagedestroy($rtn);
		}
		break;
  case 'view':
?>
<h1>皮膚更換</h1>

<br>
<!--floatj: Add Notice, to be improved by Davy kerker.-->
請先詳閱<a href="skin_readme.php" target="_blank">使用須知</a>
<br>

<legend>目前皮膚：</legend>
<div id="skinpreview" style="float:left;"></div>
<script type="text/javascript">
<?php
		if ( get_user_skin_uploaded($login['username']) )
			echo 'var $skinPath = "./skins/'.$login['username'].'.png";';
		else
			echo 'var $skinPath = "./img/skin_default.png";';

		if ( get_user_skin_cloak_uploaded($login['username']) )
			echo 'var $skinCloakPath = "./cloaks/'.$login['username'].'.png";';
		else
			echo 'var $skinCloakPath = "./img/skin_cloak_default.png";';
?>
</script>
<script type='text/javascript' src='skinViewer.js'></script>
<legend>上傳皮膚：</legend>
<div class="form-horizontal">
	<fieldset>
	<div class="control-group">
		<label class="control-label" for="skinFile">選擇皮膚檔</label>
		<div class="controls">
			<input type="file" name="skinFile" id="skinFile" />
		</div>
	</div>
	</fieldset>

	<div class="form-actions">
		<button type="button" class="btn btn-primary" id="submitSkin">送出資料</button>
		<form action="skin.php?act=remove" method="post" enctype="multipart/form-data" id="skin_form" style="margin: 0; display: initial;" >
			<button type="submit" class="btn btn-primary" id="removeSkin">刪除皮膚</button>
		</form>
	</div>
</div>
<legend>上傳披風：</legend>
<div class="form-horizontal">
	<fieldset>
	<div class="control-group">
		<label class="control-label" for="cloakFile">選擇披風檔</label>
		<div class="controls">
			<input type="file" name="cloakFile" id="cloakFile" />
		</div>
	</div>
	</fieldset>

	<div class="form-actions">
		<button type="button" class="btn btn-primary" id="submitCloak">送出資料</button>
		<form action="skin.php?act=removeCloak" method="post" enctype="multipart/form-data" id="cloak_form" style="margin: 0; display: initial;" >
			<button type="submit" class="btn btn-primary" id="removeCloak">刪除披風</button>
		</form>
	</div>
</div>
<script>
$("#skinFile").ajaxfileupload({
	action: "skin.php?act=upload&type=skin",
	submit_button: $("#submitSkin"),
	valid_extensions: ['png'],
	onComplete: function(data) {
		if (data == "EMPTY") {
			$("#message").empty().append("請選擇皮膚檔！").addClass('alert-error').fadeOut().fadeIn();
		}
		if (data == "WRONG") {
			$("#message").empty().append("上傳失敗！").addClass('alert-error').fadeOut().fadeIn();
		}
		if (data == "WRONG:SIZE") {
			$("#message").empty().append("檔案太大導致上傳失敗！").addClass('alert-error').fadeOut().fadeIn();
		}
		if (data == "WRONG:TYPE") {
			$("#message").empty().append("皮膚檔必須為 png 格式！").addClass('alert-error').fadeOut().fadeIn();
		}
		if (data == "WRONG:SIZE_R") {
			$("#message").empty().append("皮膚大小必須為 64px x 32px！").addClass('alert-error').fadeOut().fadeIn();
		}
		if (data == "SUCCESS") {
			$("#message").empty().append("皮膚上傳成功！").removeClass('alert-error').addClass('alert-success').fadeOut().fadeIn();
			loadTab($('[href=#skin]').parents('li'));
		}
	},
	onEmpty: function() {
		$("#message").empty().append("請選擇皮膚檔！").addClass('alert-error').fadeOut().fadeIn();
	},
	onTypeNotMatch: function() {
		$("#message").empty().append("皮膚檔必須為 png 格式！").addClass('alert-error').fadeOut().fadeIn();
	}
});
$("#cloakFile").ajaxfileupload({
	action: "skin.php?act=upload&type=cloak",
	submit_button: $("#submitCloak"),
	valid_extensions: ['png'],
	onComplete: function(data) {
		if (data == "EMPTY") {
			$("#message").empty().append("請選擇披風檔！").addClass('alert-error').fadeOut().fadeIn();
		}
		if (data == "WRONG") {
			$("#message").empty().append("上傳失敗！").addClass('alert-error').fadeOut().fadeIn();
		}
		if (data == "WRONG:SIZE") {
			$("#message").empty().append("檔案太大導致上傳失敗！").addClass('alert-error').fadeOut().fadeIn();
		}
		if (data == "WRONG:TYPE") {
			$("#message").empty().append("披風檔必須為 png 格式！").addClass('alert-error').fadeOut().fadeIn();
		}
		if (data == "WRONG:SIZE_R") {
			$("#message").empty().append("披風大小必須為 64px x 32px！").addClass('alert-error').fadeOut().fadeIn();
		}
		if (data == "SUCCESS") {
			$("#message").empty().append("披風上傳成功！").removeClass('alert-error').addClass('alert-success').fadeOut().fadeIn();
			loadTab($('[href=#skin]').parents('li'));
		}
	},
	onEmpty: function() {
		$("#message").empty().append("請選擇披風檔！").addClass('alert-error').fadeOut().fadeIn();
	},
	onTypeNotMatch: function() {
		$("#message").empty().append("披風檔必須為 png 格式！").addClass('alert-error').fadeOut().fadeIn();
	}
});

$("#skin_form").submit(function(event) {
	event.preventDefault();
	$.post('skin.php?act=remove', $('#skin_form').serialize(), function (data) {
		if (data == "WRONG")
			$("#message").empty().append("網站發生錯誤，請稍後再試。").addClass('alert-error').fadeOut().fadeIn();
		if (data == "SUCCESS") {
			$("#message").empty().append("移除皮膚成功！").removeClass('alert-error').addClass('alert-success').fadeOut().fadeIn();
			loadTab($('[href=#skin]').parents('li'));
		}
	});
});

$("#cloak_form").submit(function(event) {
	event.preventDefault();
	$.post('skin.php?act=removeCloak', $('#cloak_form').serialize(), function (data) {
		if (data == "WRONG")
			$("#message").empty().append("網站發生錯誤，請稍後再試。").addClass('alert-error').fadeOut().fadeIn();
		if (data == "SUCCESS") {
			$("#message").empty().append("移除披風成功！").removeClass('alert-error').addClass('alert-success').fadeOut().fadeIn();
			loadTab($('[href=#skin]').parents('li'));
		}
	});
});
</script>
<?php
		break;
}
?>
