<?php require('config.php') ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/index.css" rel="stylesheet">
<link href="css/login.css" rel="stylesheet">
<script src="jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="jquery.ajaxfileupload.js" type="text/javascript"></script>
<script src="Three.js" type="text/javascript"></script>
<script type="text/javascript">
//GA
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-33693646-1']);
_gaq.push(['_trackPageview']);

(function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();

</script>
<script type="text/javascript">
var $last_tab = null;
function loadTab (tab)
{
    var $this = $(tab),
                _clickTab = $this.find('a').attr('href');
    if (tab != $last_tab)
        $last_tab = tab;
    $this.addClass('active').siblings('.active').removeClass('active');

    if (_clickTab == '#idtable' ) { window.location.reload(); }
    if (_clickTab == '#edit' ) { $('#edit').load('edit.php'); }
        if (_clickTab == '#change' ) { $('#change').load('change.php'); }
            if (_clickTab == '#reg' ) { $('#reg').load('reg.php'); }
                if (_clickTab == '#skin' ) { $('#skin').load('skin.php?act=view'); }
                    if (_clickTab == '#admin' ) { $('#admin').load('admin.php'); }

                        $(_clickTab).stop(false, true).fadeIn().siblings().hide();
}
$(function(){
    var _showTab = 0;
    var $defaultLi = $('ul#topNav li').eq(_showTab).addClass('active');
    $($defaultLi.find('a').attr('href')).siblings().hide();
    $('ul#topNav li').click(function() {
        loadTab(this);
        return false;
    }).find('a').focus(function(){
        this.blur();
    });
});

// Init for Login Controller
$(function(){
    $('#login #blackscreen').click(function() {
        $('#login').stop(false, true).fadeOut();
    });
    $(window).resize(function() {
        $("#login #container").css({
            left: (($('#login').innerWidth() - $('#login #container').outerWidth()) / 2) + "px",
                top: (($('#login').innerHeight() - $('#login #container').outerHeight()) / 2) + "px"
        });
    });
    $('ul#topLoginC li').click(function() {
        var $this = $(this),
                        _clickTab = $this.find('a').attr('href');

        if (_clickTab == '#login' ) {
            $('#login #container').load('login.php?act=login');
            $('#login').stop(false, true).fadeIn();
        }
        if (_clickTab == '#logout' ) {
            $.post('login.php?act=logout', null, function (data) {
                if (data == "SUCCESS")
                    window.location.reload(true);
            });
        }
        return false;
    }).find('a').focus(function(){
        this.blur();
    });

/*
    $('#modskin_readme').click(function() {
                var $this = $(this),
                        _clickTab = $this.find('a').attr('href');

                if (_clickTab == '#login' ) {
                        $('#login #container').load('login.php?act=login');
                        $('#login').stop(false, true).fadeIn();
                }
                if (_clickTab == '#logout' ) {
                        $.post('login.php?act=logout', null, function (data) {
                                if (data == "SUCCESS")
                                        window.location.reload(true);
                        });
                }
                return false;
        }).find('a').focus(function(){
                this.blur();
        });


 */

});

</script>
<title>雪服帳號管理網站 - Snowman's Minecraft PWD Utility</title>
</head>
<body>

<div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
                <div class="container">
                        <a class="brand" href="index.php">雪服帳號管理系統</a>
                        <ul class="nav" id="topNav">
                                <li><a href="#idtable">ID對照表</a></li>
<?php
if ( !$login['logined'] ) { // not logined
?>
                                <li><a href="#reg">註冊帳號</a></li>
<?php
} else {
?>
                                <li><a href="#edit">編輯資料</a></li>
                                <li><a href="#change">更換密碼</a></li>
                                <li><a href="#skin">更換皮膚</a></li>
<?php
    if ( $login['admin'] == true )
    {
?>
                                <li><a href="#admin">帳號審核</a></li>
<?php
    }
}
?>
                        </ul>
                        <ul class="nav pull-right" id="topLoginC">
<?php
if ( !$login['logined'] ) { // not logined
?>
                                <li><a href="#login">登入</a></li>
<?php
} else {
?>
                                <li>
                                        <span id="userinfo"><?=$login['nickname']?></span>
                                        <span id="userhead" style="background: url(./heads/<?=$login['username']?>.png)">&nbsp;</span>
                                </li>
                                <li><a href="#logout">登出</a></li>
<?php
}
?>
                        </ul>
                </div>
        </div>
</div>
<div class="container" style="margin-top:60px;">
        <div class="content">
                <div class="alert" id="message"></div>

                <div id="idtable">
                        <h1>ID 對照表</h1>
                        <table class="table table-condensed table-bordered table-striped">
                                <thead>
                                        <th></th>
                                        <th>帳號 / 暱稱</th><th>BBS ID</th><th>介紹人</th>
                                        <th>自我介紹</th><th>上次登入</th>
                                </thead>
                                <tbody>
<?php

$sql = "SELECT * from `authme` ORDER BY `username` ASC";
$result = mysql_query($sql);
while( $res = mysql_fetch_array($result) )
{
    echo '					'. // make it beauty when output HTML
        '<tr><td><span class="icon_head" style="background: url(./heads/'.$res['username'].'.png);">&nbsp;</span></td>'.
        '<td>'.$res['username'].'<br />'.$res['nick'].'</td>'.
        '<td>'.$res['bbs'].'</td>'.
        '<td>'.$res['ref'].'</td>'.
        '<td>'.$res['intro'].'</td>'.
        '<td>'.date("Y/m/d",$res['lastlogin']/1000).'<br />'.date("h:i",$res['lastlogin']/1000).'</td></tr>
        ';
}

?>
                                </tbody>
                        </table>
                </div> <!-- end div#idtable -->

                <div id="reg">
                </div> <!-- end div#reg -->

                <div id="edit">
                </div> <!-- end div#edit -->

                <div id="change">
                </div> <!-- End div#change -->

                <div id="skin">
                </div> <!-- End div#skin -->

                <div id="admin">
                </div> <!-- End div#admin -->

        </div> <!-- End content div -->

        <div class="footer">
                <p>網站由 sntc06 撰寫 &copy; 2012, 若有疑問請聯繫 taya86334 [at] gmail.com</p>
                <p>Powered by <a href="http://twitter.github.com/bootstrap/">Bootstrap</a>, <a href="http://jquery.com/">jQuery</a>.</p>
        </div>

        <div id="login">
          <div id="blackscreen"></div>
                <div id="container"></div>
        </div> <!-- End div#login -->

</div> <!-- End container div -->
</body>
</html>
