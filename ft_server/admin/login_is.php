<?php
require("../includes/conf.inc.php");
require("../includes/debug.inc.php");
session_start();

if(!isset($_SESSION['logged_in']) or
   $_SESSION['logged_in'] != "true"){
  
  require('./auth_db.inc.php');

  $pw=isset($_POST['password']) ? 
    $_POST['password'] : 
    "";

  $pass_sql=sha1($pw);
  
  $sql="
SELECT ROWID FROM `auth` 
 WHERE
  pass_hash='$pass_sql'";  

  $auth_db->q($sql);
  if(!$row=$auth_db->f()){
    $_SESSION['flash']="
Sorry the password is incorrect. 
The default password is '<code>dfhu.org</code>'. 
This script requires cookies enabled.
";
    header("Location: ./login.php");
    exit;
  }

}

if($_SESSION['logging_in'] == "1"){
  $_SESSION['logging_in']="";
  $_SESSION['logged_in']="true";
  
  $token = md5(TOKEN_SALT . uniqid(mt_rand(), true));
  $_SESSION['CSRF_delete_do']=$token;

  header("Location: ./index.php");
}

?>