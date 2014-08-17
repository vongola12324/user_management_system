<?php
/* loginController.php
 *
 * @desc	Just a login controller, using session. xD
 * @author	Davy
 * @lastedit	2012/04/06 23:37  Ver.01
 */

global $login;

if ( !isset($_SESSION['login']) )
  $login = array('logined' => false);
else if ( !isset($_SESSION['login']['logined']) || ($_SESSION['login']['logined'] == false) ||
        !isset($_SESSION['login']['username']) )
  $login = array('logined' => false);
else
  $login = $_SESSION['login'];
?>
