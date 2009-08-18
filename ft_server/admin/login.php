<?php
session_start();
require("../includes/conf.inc.php");
$_SESSION['logging_in']="1";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
    "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Login To CCenter</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="css/ccenter.css" >
</head>
<body>
<div id="login_box">
  <h2>Login to CCenter</h2>

<?php //<!--
  //say_flash();

  echo "<p>" . $_SESSION['flash'] . "</p>";
$_SESSION['flash']="";


//-->
?>

    <form action="login_is.php" method="POST">
      <table>
            
      <tr>
	<td>Password:</td>
	<td><input type="password" name="password"></td>
      </tr>
      
      <tr>
	<td colspan="2"><input type="submit" name="submit" value="Login into CCenter"></td>
      </tr>
    
      </table>  
      
    </form>
    <p id="change_password">
<?php
  


require('./auth_db.inc.php');
$pass_sql=sha1('dfhu.org');
$sql="
select ROWID from `auth` 
 where
  pass_hash='$pass_sql'";
$auth_db->q($sql);

if($row=$auth_db->f()){
   echo "You are still using the default username password which is 'dfhu.org'";
   echo " This is a bad idea to leave as the default, you should ";
}else{
   echo "You may change ";
}
?>
      <a href="login_change.php">change your Password</a>.
    </p>
    
  
</div>


</body>
</html>
