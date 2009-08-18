<?php //<!--
/*

@author: Victory
@site: http://dfhu.org
@copyright: dfhu.org
@report_bugs: bugs(at)dfhu.org
@feature_request: features(at)dfhu.org
@file:
@license: BSD

@description:

  This file is great.

$Date:: 2009-08-16 06:07:06 #$:
$Rev:: 10                    $:

*/

session_start();
require("../includes/conf.inc.php");

if($_POST['pw1'] != $_POST['pw2']){
  $_POST['flash']="Sorry the new passwords don't match. ";
  header("Location: ./login_change.php");
  exit;
}

if(strlen(trim($_POST['pw1'])) < 8 or
   strlen(trim($_POST['pw2'])) > 32 ){
  $_POST['flash']="Sorry the new passwords is too short. It must be at least 8 chars long, but no greater than 32.";
  header("Location: ./login_change.php");
  exit;
}


require("./auth_db.inc.php");
$pass_sql=sha1($_POST['password']);
$sql="
SELECT ROWID FROM `auth` 
 WHERE
  pass_hash='$pass_sql'";
$auth_db->q($sql);

if(!$row=$auth_db->f()){
  $_SESSION['flash']="Sorry the old password is incorrect. The default password is
'<code>ccenter_default_password</code>'. This script requires cookies
enabled.";
  header("Location: ./login_change.php");
  exit;
}

$pw_sql=sha1($_POST['pw1']);
$sql="INSERT INTO `auth` (pass_hash) VALUES ('$pw_sql')";
$auth_db->q($sql);

$_SESSION['flash']="Password Succesfully Changed. You may now login.";
header("Location: ./login.php");


//-->
?>