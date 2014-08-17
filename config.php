<?php
session_start();
require('loginController.php');
/* MySQL Config */
$server='localhost';
$user='authme';
$pass='';
$db='minecraft_authme';

$db_link = mysql_connect($server, $user, $pass) or die('Can`t connect to db because: <br />'.mysql_error());
mysql_query("SET NAMES 'utf8'");
mysql_select_db($db, $db_link) or die('Can`t find db because: <br />'.mysql_error());

function encrypt_pass($password) {
    $salt = substr(md5(uniqid(rand(), true)),0,15);
    $encryptedpass = '$SHA$' . $salt . '$' . hash('sha256',hash('sha256',$password) . $salt);
    return $encryptedpass;
}


// @return true if password and nickname match
function check_password_db($nickname,$password) {
    $a=mysql_query("SELECT password FROM authme where username = '$nickname'");
    if(mysql_num_rows($a) == 1 ) {
        $password_info=mysql_fetch_array($a);
        $sha_info = explode("$",$password_info[0]);
    } else return false;
    if( $sha_info[1] === "SHA" ) {
        $salt = $sha_info[2];
        $sha256_password = hash('sha256', $password);
        $sha256_password .= $sha_info[2];;
        if( strcasecmp(trim($sha_info[3]),hash('sha256', $sha256_password) ) == 0 ) return true;
        else return false;
    }

}

function change_password ($user, $newpass) {
    $salt = substr(md5(uniqid(rand(), true)),0,15);
    $encryptedpass = '$SHA$' . $salt . '$' . hash('sha256',hash('sha256',$newpass) . $salt);
    $query = "UPDATE `authme` SET `password` = '$encryptedpass' WHERE `username` = '$user'";
    $result = mysql_query($query);
    if ($result) return true;
    else return false;

}

function edit_info ($user, $nick, $bbs, $ref, $intro) {
    $query = "UPDATE `authme` SET
        `nick` = '$nick',
        `bbs` = '$bbs',
        `ref` = '$ref',
        `intro` = '$intro'
        WHERE `username` = '$user'";
    $result = mysql_query($query);
    if ($result) return true;
    else return false;
}

function check_op ($user) {
    $query = "SELECT * FROM `authme` WHERE `username` = '$user'";
    $result = mysql_query($query);
    $res = mysql_fetch_assoc($result);
    if ($res['op']) return true;
    else return false;
}

function add_new_user ($user, $pass, $email, $nick, $bbs, $ref, $intro) {
    $salt = substr(md5(uniqid(rand(), true)),0,15);
    $encryptedpass = '$SHA$' . $salt . '$' . hash('sha256',hash('sha256',$pass) . $salt);

    $query = "INSERT INTO `authme` (username, password, email, nick, bbs, ref, intro, op) VALUES (
        '$user', '$encryptedpass', '$email','$nick', '$bbs', '$ref', '$intro', 0)";
    $result = mysql_query($query);
    if ($result) return true;
    else return false;
}
function add_new_user_approved ($user, $pass, $email, $nick, $bbs, $ref, $intro) {
    $query = "INSERT INTO `authme` (username, password, email, nick, bbs, ref, intro, op) VALUES (
        '$user', '$pass', '$email','$nick', '$bbs', '$ref', '$intro', 0)";
    $result = mysql_query($query);
    if ($result) return true;
    else return false;
}
function add_new_user_pending ($user, $pass, $email, $ip, $time, $nick, $bbs, $ref, $intro) {
    $query = "INSERT INTO `register` (username, password, email, ip, time, nick, bbs, ref, intro, status) VALUES (
        '$user', '$pass', '$email', '$ip', '$time', '$nick', '$bbs', '$ref', '$intro', 0)";
    $result = mysql_query($query);
    if ($result) return true;
    else return false;
}

function get_user_nickname ($user)
{
    $query = "SELECT `username`, `nick` FROM `authme` WHERE `username` = '$user'";
    $result = mysql_query($query);
    if ( !$result ) return "";

    $res = mysql_fetch_assoc($result);
    return $res['nick'];
}

function get_user_skin_uploaded ($user) {
    $query = "SELECT `username`, `skin_custom` FROM `authme` WHERE `username` = '$user'";
    $result = mysql_query($query);
    if ( !$result ) return false;

    $res = mysql_fetch_assoc($result);
    if ( $res['skin_custom'] == 0 ) return false;
    return true;
}

function get_user_skin_data ($user) {
    $query = "SELECT `username`, `skin_data` FROM `authme` WHERE `username` = '$user'";
    $result = mysql_query($query);
    if ( !$result ) {
        die('Error: Skin data not found, but requested.');
        exit();
    }
    $res = mysql_fetch_assoc($result);
    return $res['skin_data'];
}

function change_user_skin ($user, $data) {
    $query = "UPDATE `authme` SET
        `skin_custom` = 1,
        `skin_data` = '$data'
        WHERE `username` = '$user'";
    $result = mysql_query($query);
    if ($result) return true;
    else return false;
}

function get_user_skin_cloak_uploaded ($user) {
    $query = "SELECT `username`, `skin_cloak_custom` FROM `authme` WHERE `username` = '$user'";
    $result = mysql_query($query);
    if ( !$result ) return false;

    $res = mysql_fetch_assoc($result);
    if ( $res['skin_cloak_custom'] == 0 ) return false;
    return true;
}


function get_user_skin_cloak_data ($user) {
    $query = "SELECT `username`, `skin_cloak_data` FROM `authme` WHERE `username` = '$user'";
    $result = mysql_query($query);
    if ( !$result ) {
        die('Error: Cloak data not found, but requested.');
        exit();
    }
    $res = mysql_fetch_assoc($result);
    return $res['skin_cloak_data'];
}

function change_user_skin_cloak ($user, $data) {
    $query = "UPDATE `authme` SET
        `skin_cloak_custom` = 1,
        `skin_cloak_data` = '$data'
        WHERE `username` = '$user'";
    $result = mysql_query($query);
    if ($result) return true;
    else return false;
}

function remove_user_skin ($user) {
    $query = "UPDATE `authme` SET
        `skin_custom` = 0
        WHERE `username` = '$user'";
    return mysql_query($query) ? true : false;
}

function remove_user_skin_cloak ($user) {
    $query = "UPDATE `authme` SET
        `skin_cloak_custom` = 0
        WHERE `username` = '$user'";
    return mysql_query($query) ? true : false;
}

function get_register_list () {
    $query = "SELECT * FROM `register` ORDER BY `time` ASC";
    $result = mysql_query($query);
    if ( !$result )
    {
        die('Error: Get error from mySQL when fetching register list.');
        @exit();
    }
    return $result;
}

function is_username_used ($user) {
    $query = "SELECT `username` FROM `authme` WHERE `username` = '$user'";
    $result = mysql_query($query);
    if ( mysql_num_rows($result) > 0) return true;
    return false;
}

function is_username_registing ($user) {
    $query = "SELECT `username` FROM `register` WHERE `username` = '$user'";
    $result = mysql_query($query);
    if ( mysql_num_rows($result) > 0) return true;
    return false;
}

function get_pending_user_data ($user) {
    $query = "SELECT * FROM `register` WHERE `username` = '$user'";
    $result = mysql_query($query);
    if ( !$result )
    {
        die('Error: Get error from mySQL when fetching register list.');
        @exit();
    }
    return $result;
}

function delete_pending_user ($user) {
    $query = "DELETE FROM `register` WHERE `username` = '$user'";
    $result = mysql_query($query);
    if ( !$result )
    {
        return false;
    }
    return true;
}


/*function add_register ($user, $pass, $ip, $time, $nick, $bbs, $ref, $intro) {
        $salt = substr(md5(uniqid(rand(), true)),0,15);
        $encryptedpass = '$SHA$' . $salt . '$' . hash('sha256',hash('sha256',$pass) . $salt);

        $query = "INSERT INTO `register` (username, password, ip, time, nick, bbs, ref, intro) VALUES (
                '$user', '$encryptedpass', '$ip', '$time', '$nick', '$bbs', '$ref', '$intro')";
        $result = mysql_query($query);
        return ( $result ? true : false );
}*/



?>
