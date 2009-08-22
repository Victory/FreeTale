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


function parse_tic($tic_string){

  // the tab delimited log file is in the following order, one row
  // UNIXTIME ID URL REFERER WIDTH HEIGHT TIMEELASPED CLICKS
  // MOUSEMOVEMENT SCROLL


  $tic=Array();
  $bits=explode("\t",$tic_string);

  // defined in ../includes/conf.inc.php
  global $tic_vars;  
  foreach($tic_vars as $ii=>$var){
    $tic[$var]=$bits[$ii];
  }

  return $tic;
}

function parse_motion_file($log_file_name){
  
  $sqlite_db_name=
    preg_replace("/.log$/",
		 ".sqlite",
		 $log_file_name);
  
  
  $db=new DBx(DB_DIR . $sqlite_db_name,
	      "sqlite");

  $sql="DROP TABLE IF EXISTS tics";
  $db->q($sql);

  $sql="
CREATE TABLE tics (
  unixtime INT NOT NULL,
  remote_addr CHAR(30),
  id INT,
  url TEXT NOT NULL,
  referer TEXT,

  -- todo: make these more legit checks

  width INT CHECK(width < 4000),
  height INT CHECK(height < 4000),
  time_elapsed INT,
  clicks TEXT,
  mouse_movements TEXT,
  scroll TEXT
)";
  $db->q($sql);

  $sql="
CREATE INDEX 
  tics_id
    ON tics(id)
";
  $db->q($sql);

  global $tic_vars;
  $contents=file_get_contents(DB_DIR . $log_file_name);
  $lines=explode("\n",$contents);
  // begin a transaction
  $db->bt();


  foreach($lines as $line){
    if(strlen($line) == 0)
      continue;

    $tic=parse_tic($line);
    $db->set('tics',$tic);

    // todo add cleanup string
    /** /
    echo "<pre>";
    print_r($tic);
    echo "</pre>";
    /**/
  }
  // commit the transaction
  $db->c();
}

// TODO, refactor, because parse_tic is the same thing
function parse_form_action($action_string){

  $form_action=Array();
  $bits=explode("\t",$action_string);

  // defined in ../includes/conf.inc.php
  global $form_action_vars;  
  foreach($form_action_vars as $ii=>$var){
    $form_action[$var]=$bits[$ii];
  }
  return $form_action;
}


function parse_form_file($log_file_name){
  
  $sqlite_db_name=
    preg_replace("/.log$/",
		 ".sqlite",
		 $log_file_name);
  
  
  $db=new DBx(DB_DIR . $sqlite_db_name,
	      "sqlite");

  $sql="DROP TABLE IF EXISTS actions";
  $db->q($sql);

  $sql="
CREATE TABLE actions (
  unixtime INT NOT NULL,
  remote_addr CHAR(30),
  id INT,
  url TEXT NOT NULL,
  referer TEXT,
  input_type TEXT,
  form_id TEXT,
  input_name TEXT,
  time_elapsed TEXT,
  key_ups INT
)";
  $db->q($sql);

  $sql="
CREATE INDEX IF NOT EXISTS 
  actions_id
    ON actions(id)
";
  $db->q($sql);

  global $form_actions;
  $contents=file_get_contents(DB_DIR . $log_file_name);
  $lines=explode("\n",$contents);
  // begin a transaction
  $db->bt();
  foreach($lines as $line){
    if(strlen($line) == 0)
      continue;

    $tic=parse_form_action($line);
    $db->set('actions',$tic);

    // todo add cleanup string
    /**/
    echo "<pre>";
    print_r($tic);
    echo "</pre>";
    /**/
  }
  $db->c();
}


echo "<pre>";
$d=dir(DB_DIR);
// We are going to go through all the files in the dir ..
while (false !== ($entry = $d->read())) {
  // ... ignoring anything thats not a iseeyou logs.
  if(!preg_match("/freetale(form)?.log$/",$entry)){
    continue;
  }

  if(preg_match("/freetale.log$/",$entry)){
    echo "<br>parseing motion log '$entry' ...<br><br>";
    parse_motion_file($entry);
  }

  if(preg_match("/freetaleform.log$/",$entry)){
    echo "<br>parseing form log '$entry' ...<br><br>";
    parse_form_file($entry);
  }

  // When we find and iseeyou log we are going to process it
}
echo "</pre>";
?>