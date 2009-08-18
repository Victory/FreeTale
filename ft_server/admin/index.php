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
require("./admin_parse_log.php");

$db_dir=DB_DIR;

$d=dir(DB_DIR);
while (false !== ($entry = $d->read())) {
  if(!preg_match("/\.sqlite$/",$entry) or
     $entry == "auth.sqlite")
    continue;
  $db_name="$db_dir$entry";
  echo "<a href=\"admin_db_view.php?db=$entry\">$entry</a><br>";
}

?>


<p>
<a href="./login_out.php">logout</a>
</p>