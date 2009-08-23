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

if(!preg_match("/freetale(form).sqlite$/",
	       $_GET['db'])){

  echo "BAD DATABSE";
  exit;
}


$db=new DBx(DB_DIR . $_GET['db']);

if(preg_match("/freetale.sqlite$/",
	      $_GET['db'])){
  
  $sql="
SELECT id,url FROM tics 
 GROUP BY id
";
  $stats_type="replay";

}else{
  $sql="
SELECT id,url FROM actions
 GROUP BY id
";
  $stats_type="form_view";
}
$db->q($sql);

// TODO: Really need to make this stop sucking so bad
$template="
<a 
 href=\"admin_{$stats_type}.php?id=%id%&url=%url%&db={$_GET['db']}\"
 >%id% %url%</a>
<a 
  href=\"admin_{$stats_type}_summary.php?url=%url%&db={$_GET['db']}\"
 >Summary</a>
<br>";

$db->while_row($template);


?>