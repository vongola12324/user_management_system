<?php 
//session_start();
require('config.php'); 

if ( !isset($_POST['action']) )
{
    die('error!');
    exit();
}

if ( $_POST['action'] == 'change' )
{
	$password = $_POST['oldpass'];
	$np1 = $_POST['newpass1'];
	$np2 = $_POST['newpass2'];

	$is_correct = check_password_db($login['username'], $password);
	if ($is_correct) {
		if ( empty($np1) || empty($np2) ) $return = "MISMATCH";
		else if ( mb_strlen($np1) < 4 || mb_strlen($np1) > 20) $return = "MISMATCH";
		else if ( strcmp($np1, $np2) == 0 )
		{
			$act = change_password($login['username'], $np1);
			($act) ? $return = "SUCCESS" : $return = "ERROR";
		}
		else $return = "MISMATCH";
	}
	else $return = "WRONG";

	echo $return;
}

if ( $_POST['action'] == 'edit' )
{
	$password = $_POST['pass'];

	$is_correct = check_password_db($login['username'], $password);
	if ( $is_correct )
	{	
		if ( !empty($_POST['pass']) &&
			!empty($_POST['nick']) &&
			!empty($_POST['bbs']) &&
			!empty($_POST['ref']) && 
			!empty($_POST['intro']) )
		{
			$edt = edit_info(mysql_real_escape_string($login['username']),
			    htmlspecialchars($_POST['nick'],ENT_QUOTES,"UTF-8"),
		 	    htmlspecialchars($_POST['bbs'],ENT_QUOTES,"UTF-8"),
			    htmlspecialchars($_POST['ref'],ENT_QUOTES,"UTF-8"),
			    htmlspecialchars($_POST['intro'],ENT_QUOTES,"UTF-8"));
			if ( $edt )
			{
				$_SESSION['login']['nickname'] = $_POST['nick'];
				$return = "SUCCESS";
			}
			else
				$return = "ERROR";
		}
		else $return = "EMPTY";	
	}
	else $return = "WRONG";
	
	echo $return;
}

if ( $_POST['action'] == 'add_new_user' )
{
	if ($login['admin'] == true) {
		if ( !empty($_POST['useradd'] ) &&
			!empty($_POST['passadd']) &&
			!empty($_POST['nick']) &&
			!empty($_POST['bbs']) &&
			!empty($_POST['ref']) && 
			!empty($_POST['intro']) &&
            !empty($_POST['email']) &&
			mb_strlen($_POST['passadd'])>3 &&
			mb_strlen($_POST['passadd'])<21 )
		{
			$add = add_new_user($_POST['useradd'], $_POST['passadd'], $_POST['email'], $_POST['nick'], $_POST['bbs'], $_POST['ref'], $_POST['intro']);
			($add) ? $return = "SUCCESS" : $return = "ERROR";
		}
		else $return = "EMPTY";
	}
	else $return = "ERROR";

	echo $return;
}

// 管理員更改密碼

if ( $_POST['action'] == 'adminChangePass' )
{
    $response_array['status'] = false;
    $response_array['message'] = "";
    
	if ($login['admin'] == true) {
		if ( !empty($_POST['user'] ) &&
			!empty($_POST['pass']) &&
			mb_strlen($_POST['pass'])>3 &&
			mb_strlen($_POST['pass'])<21 )
		{
			$add = change_password($_POST['user'], $_POST['pass']);
			if ($add) {
                $response_array['status'] = true;
                $response_array['message'] = "修改成功";
            }
		}
		else $response_array['message'] = "資料不全";
	}
	else $response_array['message'] = "錯誤";

	echo json_encode($response_array);
}

/****************** 新送出註冊單 *******************/
if ( $_POST['action'] == 'add_new_user_pending' )
{
    $response_array['status'] = false;
    $response_array['message'] = "";
    $email_regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 

    if ( !empty($_POST['useradd'] ) &&
        !empty($_POST['passadd']) &&
        !empty($_POST['nick']) &&
        !empty($_POST['bbs']) &&
        !empty($_POST['ref']) && 
        !empty($_POST['intro']) &&
        !empty($_POST['email']) &&
        mb_strlen($_POST['passadd'])>3 &&
        mb_strlen($_POST['passadd'])<21)
    {
        if (!preg_match($email_regex, $_POST['email'])) { $response_array['message'] = "E-mail 格式不正確！"; }
        else if (is_username_used($_POST['useradd'])) { $response_array['message'] = "帳號已被使用！"; }
        else if (is_username_registing($_POST['useradd'])) { $response_array['message'] = "等待審核中，請勿重複送出申請！"; }
        else {
        
            $add = add_new_user_pending(
                htmlspecialchars($_POST['useradd'],ENT_QUOTES,"UTF-8"),
                encrypt_pass($_POST['passadd']), 
                $_POST['email'], 
                $_SERVER['REMOTE_ADDR'], 
                time(), 
                htmlspecialchars($_POST['nick'],ENT_QUOTES,"UTF-8"),
                htmlspecialchars($_POST['bbs'],ENT_QUOTES,"UTF-8"),
                htmlspecialchars($_POST['ref'],ENT_QUOTES,"UTF-8"),
                htmlspecialchars($_POST['intro'],ENT_QUOTES,"UTF-8"));
				
            $to = "taya86334@gmail.com";
            $subject = "=?UTF-8?B?" . base64_encode("[SnowServer] 新註冊單") . "?=";
            $headers = 'MIME-Version: 1.0' . "\r\n" .
                "Content-type: text/html; charset=utf-8\r\n" .
                "From: snowserver@mine.snowtec.org\r\n" .
                "Reply-to: taya86334@gmail.com";
            $message='<html><body>'.
            '<p><strong>'.htmlspecialchars($_POST['nick'],ENT_QUOTES,"UTF-8").'</strong> '.
            '填寫了註冊單。</p>'.
            '<p>請上<a href="http://mine.snowtec.org/pwd">網站</a>審核。</p>'.
            '</body></html>';
            mail($to,$subject,$message,$headers);
				
            if ($add) {
                $response_array['status'] = true;
                $response_array['message'] = "成功送出申請！";
            }
            else {
                $response_array['message'] = "資料庫連線錯誤！請通知管理員。";
            }
        }
    }
    else $response_array['message'] = "有欄位沒有填喔！";
    
    echo json_encode($response_array);
	//echo $return;
}

// 審核註冊單
if ( $_POST['action'] == 'approve_user') {

    if (!isset($_POST['selection'])) {
        $response_array['status'] = false;
        $response_array['message'] = "請選擇使用者！";
        echo json_encode($response_array);
        exit();        
    }

    if ($login['admin'] == true) {
        
        $selection = $_POST['selection'];
        $approve = $_POST['approve'];
        $reason = $_POST['reason'];
        
        $result = get_pending_user_data($selection);
        $res = mysql_fetch_array($result);
        
        
        $username = $res['username'];
        $nick = $res['nick'];
        $return_res = "false";
        // 寄信 setup
        $to=$res['email'];
        $subject="[SnowServer] 註冊資訊 Registeration Info";
        $subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
        
        if ($approve == "yes") {
            //若同意
            $message='<html><body>'.
                '<p><strong>'.$nick.'</strong> 您好, 您在雪服的帳號已經開通，請使用您註冊的帳號以及密碼登入遊戲。</p>'.
                '<p>您的帳號：'.$username.'</p>'.
                '<p>您同時也可以使用同一個帳號密碼登入網站編修網頁，<br/>若要更改密碼或個人資訊請至帳號管理網站。</p>'.
                '<p>登入前請確保您閱讀了伺服器規定。</p>'.
                '<p>我們的網站：<a href="http://mine.snowtec.org">http://mine.snowtec.org</a><br/>'.
                'Facebook粉絲專頁：<a href="https://www.facebook.com/SnowServer">https://www.facebook.com/SnowServer</a></p>'.
                '<p>雪服 OP 群祝您遊玩愉快！</p>'.
                '</body></html>';
                
            delete_pending_user($username);
            add_new_user_approved($username, 
                $res['password'], 
                $res['email'], 
                $res['nick'], 
                $res['bbs'], 
                $res['ref'], 
                $res['intro']);
              
        }
        else if ($approve == "no"){
            //不同意
            $message='<html><body>'.
                '<p><strong>'.$nick.'</strong> 您好, 您的帳號註冊申請已被退回。</p>'.
                '<p>理由：'.$reason.'</p>'.
                '<p>請確定您符合註冊帳號之要求。</p>'.
                '<p>我們的網站：<a href="http://mine.snowtec.org">http://mine.snowtec.org</a><br/>'.
                'Facebook粉絲專頁：<a href="https://www.facebook.com/SnowServer">https://www.facebook.com/SnowServer</a></p>'.
                '<p>雪服 OP 群敬上</p>'.
                '</body></html>';
            
            delete_pending_user($username);
            
        }
        else {
            //刪除
            delete_pending_user($username);
            $response_array['status'] = true;
            $response_array['message'] = "成功刪除！";
            echo json_encode($response_array);
            exit();    
        }

        $headers = 'MIME-Version: 1.0' . "\r\n" .
            "Content-type: text/html; charset=utf-8\r\n" .
            "From: snowserver@mine.snowtec.org\r\n" .
            "Reply-to: taya86334@gmail.com\r\n".
            "BCC: taya86334@gmail.com";
        if (mail($to,$subject,$message,$headers)) {
            $response_array['status'] = true;
            $response_array['message'] = "信件成功寄出！";
        }
        else {
            $response_array['status'] = false;
            $response_array['message'] = "寄信失敗！";
        }
        echo json_encode($response_array);   
    }
}

?>