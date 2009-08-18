<?php
require("../includes/conf.inc.php");
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
    "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Change Password</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="css/ccenter.css" >
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.validate.min.js"></script>

<script type="text/javascript">
  $(document).ready(function(){
    $("#change_password_form")
      .validate({
	rules: {
	  password: "required",
	  pw1: {required: "true",
		minlength: "8"
	       },
	  pw2: {required: "true",
		minlength: "8",
		equalTo: "#pw1"
	       }
	 
	}});
  });
</script>


</head>
<body>
<div id="login_box">
  <h2>Change Password</h2>

<?php //<!--
  /*
   say_flash();
  */
//-->
?>

    <form id="change_password_form" action="login_change_do.php" method="POST">
      <table>
            
      <tr>
	<td>Current Password:</td>
	<td><input type="password" name="password"></td>
      </tr>

            
      <tr>
	<td>New Password:</td>
	<td><input id="pw1" type="password" name="pw1"></td>
      </tr>

            
      <tr>
	<td>Retype New Password:</td>
	<td><input id="pw2" type="password" name="pw2"></td>
      </tr>
      

      <tr>
	<td colspan="2"><input type="submit" name="submit" value="Change Password"></td>
      </tr>
    
      </table>  
      
    </form>

    <p id="change_password">
      Back to <a href="./login.php">login page</a>.
    </p>

</div>


</body>
</html>
