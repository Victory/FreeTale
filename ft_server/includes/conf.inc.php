<?php
/*

@author: Victory
@site: http://dfhu.org
@copyright: dfhu.org
@report_bugs: bugs(at)dfhu.org
@feature_request: features(at)dfhu.org
@file: scrollingheatmap/includes/conf.inc.php
@license: BSD

@description:

  The Cofiguration File

*/

date_default_timezone_set('Europe/London');


// higher is louder
define("DEBUG_LEVEL",5);

// default Max Excution Time for Scripts
define("MAX_EXECUTION_TIME_SECONDS",30);

define("DB_ENGINE","sqlite");
define("TOKEN_SALT","something random 8484");

/* Smile, You are Done Editing */


global $tic_vars;
// the order of the tab separated log files
$tic_vars=
  Array('unixtime','remote_addr','id',
	'url','referer','width','height','body_height',
	'time_elapsed','clicks','mouse_movements',
	'scroll');

global $form_action_vars;
$form_action_vars=
  Array('unixtime','remote_addr','user_agent',
	'id','url','referer','input_type',
	'form_id','input_name',
	'time_elapsed','key_ups');

// if the Basedir Path is not set ...
if(!defined("PATH_BASE")){
  // then define it. Example: /dfhufoundation/. This will be stripped
  // when serving up pages.
  define("PATH_BASE",
	 substr($_SERVER['PHP_SELF'],0,-9));
}


// if the Absolute Path is not defined ...
if(!defined("PATH_ABS")){
  // ... then define it. Example /home/user/yoursite.faux/
  define("PATH_ABS",
	 substr(__FILE__,0,-21));
}

if(!defined("PATH_MAPPED")){
  define("PATH_MAPPED",
	 substr(PATH_BASE,0,-strlen(PATH_BASE)+1));
}

if(!defined("DB_DIR")){
  define("DB_DIR",PATH_ABS . "db/");
}

?>
