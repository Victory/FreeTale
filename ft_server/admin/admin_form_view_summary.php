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


select actions.input_name,sum(actions.time_elapsed)/count(*) from
actions, (select input_name from actions group by input_name) as names
where input_type != 'landing' and actions.input_name =
names.input_name group by actions.input_name;


*/

require("./login_is.php");
require("../includes/db.inc.php");
$db_dir=DB_DIR;


function sig_figs($f){
  return sprintf("0.2f",$f);
}

function anchor_link($url){
  if($url == ""){
    return "none";
  }
  $url=filter_var($url,
		  FILTER_VALIDATE_URL, 
		  FILTER_FLAG_SCHEME_REQUIRED);
  $url_string=htmlentities($url);
  
  $url_link="
<a href=\"$url\" target=\"_blank\">$url_string</a>
";
  return $url_link;

}

if(!preg_match("/freetaleform.sqlite/",
	       $_GET['db'])){

  echo "BAD DATABSE";
  exit;
}
$db=new DBx(DB_DIR . $_GET['db']);


$prepare_vars=Array(':url'=>$_GET['url']);
$sql="
SELECT 
  *
FROM 
  actions
WHERE
  url = :url 
    AND
  input_type != 'landing'
GROUP BY
  input_name
ORDER BY
  unixtime
";
$db->p($sql . " LIMIT 1");
$db->exec($prepare_vars);
$meta_row=$db->f();

$url_link=anchor_link($meta_row['url']);
$referer_link=anchor_link($meta_row['referer']);

$db->p($sql);
$db->exec($prepare_vars);
$input_names=Array();
while($row=$db->f()){
  $input_names[]=$row['input_name'];
}


$sql="
SELECT
  input_type,
  input_name,
  key_ups,
  form_id,
  COUNT(*) AS count,
  SUM(time_elapsed) AS total_time
FROM 
  actions
WHERE
  input_name = :input_name
";
$db->p($sql);


$form_time=0;
$time_stats=Array();
foreach($input_names as $input_name){
  $db->exec(Array(":input_name"=>$input_name));
  $time_stat=$db->f();
 
  $total_time=min($time_stat['total_time'],2*60);
  $form_time+=$total_time/$time_stat['count'];
  $average_key_ups=$time_stat['key_ups']/$time_stat['count'];
  $average_time=$total_time/$time_stat['count'];
 
  $time_stat["form_time"]=sig_figs($form_time);
  $time_stat["average_key_ups"]=sig_figs($average_key_ups);
  $time_stat["average_time"]=sig_figs($average_time);
  $time_stats[]=$time_stat;


  
echo "<pre>";
print_r($time_stat);
echo "</pre>";

}




/** /
$db->p($sql);
$db->exec();
/**/


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>
<title>Form Analytics</title>
<link rel="stylesheet" href="style.css" >
<meta http-equiv="content-type" content="text/html; charset=utf-8">

<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>

<meta name="description" content="" >
<meta name="keywords" content="" >

</head>
<body>
<!--
  <h2><?php echo $meta_row['remote_addr']; ?></h2>

  <ul>

    <li><?php echo date('ymd - H:i:s',$meta_row['unixtime']); ?> </li>
    <li><?php echo $url_link; ?> </li>
    <li><?php echo $referer_link ?> </li>
    <li><?php echo htmlentities($meta_row['user_agent']); ?> </li>

  </ul>
-->
<?php



$template="
 <tr>
   <td>%form_id%</td>
   <td>%input_name%</td>
   <td>%input_type%</td>
   <td>%average_key_ups%</td>
   <td class=\"%time_length%\">%total_time%</td>
   <td>%form_time%</td>
   <td>%average_time%</td>
 </tr>

";

$vars=
  array_map(create_function('$s',
			    'return "%$s%";'),
	    array_keys($time_stats[0]));

ob_start();
echo "
<table id=\"form_analytics\">
 <thead>
  <th>Form Id</th>
  <th>Input Name</th>
  <th>Input Type</th>
  <th>Average Key Ups</th>
  <th>Time Elapsed (seconds)</th>
 </thead>
 <tfoot>
   <td colspan=\"5\" id=\"footnote\">

     Footnotes go here, possibly injected via javascript

   </td>
 </tfoot>
";
foreach($time_stats as $time_stat){

 
 
  /**/ 
  $vals=array_map('htmlentities',
		  $time_stat);
  /**/

  echo str_replace($vars,$vals,$template);
  /** /
  echo "<pre>";
  print_r($time_stat);
  echo "</pre>";
  /**/
  
}
echo "</table>";
ob_end_flush();

?>

</body>
</html>
