<?php

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

*/


require("./login_is.php");
require("../includes/db.inc.php");
$db_dir=DB_DIR;

if(!preg_match("/[0-9]{6}-site-freetale.sqlite/",
	       $_GET['db'])){

  echo "BAD DATABSE";
  exit;
}

$db=new DBx(DB_DIR . $_GET['db']);

$sql="
SELECT * FROM tics 
  WHERE
    id=:id and url=:url 
  LIMIT 1
";
$db->p($sql);
$db->exec(Array(":id"=>$_GET['id'],
		":url"=>$_GET['url']));

$init=$db->f();

$_SESSION['id']=$_GET['id'];
$_SESSION['url']=$_GET['url'];
$_SESSION['db']=$_GET['db'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>
<title>FreeTale Replay</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">

<meta name="description" content="" >
<meta name="keywords" content="" >


</head>
<body>

<h2>replay</h2>

<?php
    $width=$init['width']+90;
$height=$init['height']+30;
echo "
<iframe 
  id=\"remote\"
  width=\"$width\" 
  height=\"$height\"
  src=\"{$init['url']}\"></iframe>";
?>

</body>
</html>