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



header("Content-type: text/javascript");
session_start();
if(!isset($_SESSION['logged_in']) or
   $_SESSION['logged_in'] != "true"){
  //echo 'var dfhu_iseeyou_replay=true;';
  exit;
}// not logged in

// TODO: do check in PHP instead of JS
?>

if(top.location != document.location){
<?php
  if($_SESSION['replay_motion'] == 1){
    include('replay_motion.php');  
  }elseif($_SESSION['replay_summary'] == 1){
    include('replay_summary.php');
  }
?>
}
