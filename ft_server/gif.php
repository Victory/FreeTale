<?php
/*

@author: Victory
@site: http://dfhu.org
@copyright: dfhu.org
@report_bugs: bugs(at)dfhu.org
@feature_request: features(at)dfhu.org
@file: scrollingheatmap/iseeyou_gif.php
@license: BSD

@description:

  This file is great.

*/
//saves ip address and timestamp


$database_file="db/" . date("ymd") . "-{$_SERVER['HTTP_HOST']}-freetale.log";

//TODO, Bunch of filters, turn off error reporting etc

/*
$filters=
  Array(
	'l'=>FILTER_VALIDATE_URL,
	'r'=>FILTER_VALIDATE_URL,
	'w'=>FILTER_VALIDATE_INT,
	'h'=>FILTER_VALIDATE_INT,
	't'=>FILTER_VALIDATE_INT
	);
filter_var_array($_GET,$filters);


if(!preg_match("/^[0-9]+_[0-9]+$/",$_GET['i'])){
  gif();
}


foreach($_GET['s'] as $s){
  if(!preg_match("/^(|[0-9]+)$/",$s)){
    gif();
  }
}

function check_motion($vals){
  foreach($vals as $v){
    if(!preg_match("/^(|[0-9]+x[0-9]+)$/",$v)){
      gif();
    }
  }
}
check_motion($_GET['c']);
check_motion($_GET['m']);
*/

$str=@date("U") . 
  "\t" . $_SERVER['REMOTE_ADDR'] .
  "\t" . $_GET['i'] . 
  "\t" . $_GET['l'] .
  "\t" . $_GET['r'] .
  "\t" . $_GET['w'] . 
  "\t" . $_GET['h'] .
  "\t" . $_GET['t'] .
  "\t" . implode("|",$_GET['c']) .
  "\t" . implode("|",$_GET['m']) .
  "\t" . implode("|",$_GET['s']) .
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

?>
