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

$database_file="db/" . date("ymd") . "-freetale-form.log";

$str=@date("U") . 
  "\t" . $_SERVER['REMOTE_ADDR'] .
  "\t" . $_GET['i'] . 
  "\t" . $_GET['l'] .
  "\t" . $_GET['r'] .
  "\t" . $_GET['s'] .
  "\t" . implode("|",$_GET['b']) .
  "\t" . implode("|",$_GET['k']) .
  "\n";

file_put_contents($database_file, $str, FILE_APPEND);


function gif(){
  header("content-type: image/gif");
  //43byte 1x1 transparent pixel gif
  
  echo 
    base64_decode("R0lGODlhAQABAIAAAAAAAAAAACH5B" .
		  "AEAAAAALAAAAAABAAEAAAICRAEAOw==");
  exit;
}
gif();

echo "<pre>";
print_r($_GET);
echo "</pre>";

?>