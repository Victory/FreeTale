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

$_SESSION['url']=$_GET['url'];
$_SESSION['db']=$_GET['db'];
$_SESSION['replay_motion']=0;
$_SESSION['replay_summary']=1;

if(!preg_match("/[0-9]{6}-site-freetale.sqlite/",
	       $_GET['db'])){

  echo "BAD DATABSE";
  exit;
}

$db_dir=DB_DIR;
$db=new DBx(DB_DIR . $_GET['db']);

$sql="
SELECT
  id,
  scroll,
  time_elapsed
FROM  tics
";
$db->q($sql);



function last_scroll($scroll){
  if($scroll == "")
    return False;
  $bits=explode("|",$scroll);
  return $bits[count($bits) - 1];
}

$motions=Array();
$this_id=False;
while($row=$db->f()){

  if($this_id != $row['id']){
    $this_id=$row['id'];
    $last_time=0;
    $last_scroll=0;
  }



  $motions[$row['id']][][0]=$row['time_elapsed']-$last_time;
  $last_time=$row['time_elapsed'];

  $motions[$row['id']][count($motions[$row['id']])-1][1]=last_scroll($row['scroll']);
  
  /** /
  echo "<pre>";
  print_r($row);
  echo "</pre>";
  /**/

}


/**/
echo "<pre>";
print_r($motions);
echo "</pre>";
/**/



// iterate over database, and find lowest point
?>


