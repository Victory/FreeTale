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

$Date:: 2009-08-16 06:07:06 #$:
$Rev:: 10                    $:
*/

require("../includes/conf.inc.php");
require("../includes/debug.inc.php");
require("./auth_db.inc.php");

$sql="CREATE TABLE IF NOT EXISTS `auth` (
 pass_hash varchar(100)
);";
$auth_db->q($sql);


$sql="
CREATE TRIGGER IF NOT EXISTS 
 only_one 
BEFORE INSERT ON 
 `auth`
BEGIN 
 DELETE FROM `auth`; 
END;
";
$auth_db->q($sql);


/*
$sql="
DELETE FROM `auth`;
";
$auth_db->q($sql);
exit;
*/

$sql="
SELECT COUNT(*) 
 AS count
 FROM `auth`
";
$auth_db->q($sql);
$row=$auth_db->f();

if((int)$row['count'] == 0){
  $pass_sql=sha1('dfhu.org');
  $sql="
INSERT INTO `auth`
 (pass_hash)
 VALUES
 ('$pass_sql')
";

  $auth_db->q($sql);
}

?>