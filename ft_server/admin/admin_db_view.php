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

if(!preg_match("/[0-9]{6}-dfhu_iseeyou.sqlite/",
	       $_GET['db'])){

  echo "BAD DATABSE";
  exit;
}


$db=new DBx(DB_DIR . $_GET['db']);

$sql="
SELECT id,url FROM tics 
 GROUP BY url
";

$db->q($sql);

$template="
<a 
 href=\"admin_replay.php?id=%id%&url=%url%&db={$_GET['db']}\"
 >%id% %url%</a>
<br>";


$db->while_row($template);


?>
