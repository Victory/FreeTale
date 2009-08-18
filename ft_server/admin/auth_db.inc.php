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

require("../includes/db.inc.php");
$auth_file_path= PATH_ABS . "db/auth.sqlite";

/** /
if(!file_exists($auth_file_path)){
  $fh=fopen($auth_file_path,'w');
  fclose($fh);
}
/**/

$auth_db=new DBx($auth_file_path);
unset($auth_file_path);

?>
