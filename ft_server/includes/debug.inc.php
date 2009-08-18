<?php
/*

@author: Victory
@site: http://dfhu.org
@copyright: dfhu.org
@report_bugs: bugs(at)dfhu.org
@feature_request: features(at)dfhu.org
@file: scrollingheatmap/includes/debug.inc.php
@license: BSD

@description:

  This file is great.

$Date:: 2009-08-15 12:38:19 #$:
$Rev ::                      $:

*/
ini_set('max_execution_time',
	intval(MAX_EXECUTION_TIME_SECONDS));

if(DEBUG_LEVEL == 0){
  if(function_exists('set_exception_handler')){
    function exceptionHandler($e){
      showError("<pre>Sorry, maintenance\n" .
		$e->getMessage() . 
		"</pre>");
    }
    // Set the global excpetion handler
    set_exception_handler('exceptionHandler');
  }
  ini_set('display_errors', "Off");
}

if(DEBUG_LEVEL == 1){
  error_reporting(E_ALL|E_STRICT);
  ini_set('display_errors', "Off");
}

if(DEBUG_LEVEL > 1){
  error_reporting(E_ALL|E_STRICT);
  ini_set('display_errors', "On");
}

if(DEBUG_LEVEL >= 5){
  function here($msg="<b>HERE</b>"){
    echo 
      "\n<pre style='font-family:mono'>" .
      "\nMesg: " . $msg .
      "\nStak: ";
    print_r(debug_backtrace());
    echo "\n</pre>";
  }
}else{
  function here($msg=""){};
}

function debug_say($msg,$debug_level){
  if($debug_level < DEBUG_LEVEL)
    return;
  echo "<br>$msg<br>";
}

?>