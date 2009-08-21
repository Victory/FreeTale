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



/**/
echo "<pre>";
print_r($_GET);
echo "</pre>";
exit;
/**/



$database_file=
  "db/" . 
  date("ymd") . 
  "-{$_SERVER['HTTP_HOST']}-freetaleform.log";

//TODO: Add filters 

$filters=
  Array(
	'l'=>FILTER_VALIDATE_URL,
	'r'=>FILTER_VALIDATE_URL,
	'w'=>FILTER_VALIDATE_INT,
	'h'=>FILTER_VALIDATE_INT,
	'k'=>FILTER_VALIDATE_INT
	);
filter_var_array($_GET,$filters);

if(!preg_match("/^[0-9]+_[0-9]+$/",
	       $_GET['i'])){
  gif();
}

// TODO: Put in a proper filter, sticky because the name that input
// elements can have varies, like jquery doesn't like : or
// . but they are both valid. I will just remove the most obvious
// offenders.
$_GET['b']=trim($_GET['b']);
if(preg_match("/[\s<>\"\']/",$_GET['b'])){
  gif();
}


$str=@date("U") . 
  "\t" . $_SERVER['REMOTE_ADDR'] .
  "\t" . $_GET['i'] . 
  "\t" . $_GET['l'] .
  "\t" . $_GET['r'] .
  "\t" . $_GET['b'] .
  "\t" . $_GET['k'] .
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