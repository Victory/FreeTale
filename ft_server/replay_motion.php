<?php

echo '
var freeTale_replay=0;
';

require('./includes/db.inc.php');


if(!isset($_SESSION['db']) or
   !file_exists("./db/{$_SESSION['db']}")){
  exit;
}
$db=new DBx("./db/{$_SESSION['db']}",'sqlite');

$sql="
SELECT * FROM tics 
  WHERE
    id=:id and url=:url 
";
$db->p($sql);
$db->exec(Array(":id"=>$_SESSION['id'],
                ":url"=>$_SESSION['url']));


function quote_extend(&$vals,$extend){
  foreach($extend as $e){
    $vals[]="'$e'";
  }
}

function quote_dimensions($dim){
  $bits=explode("x",$dim);
  return "Array('{$bits[0]}','{$bits[1]}')";
}

function quote_dim($dim){
  return "'$dim'";
}

function print_js_array($array_name,$vals){
  echo "  var $array_name = Array(";
  echo implode(",",$vals);
  echo ");\n";
}


$tics=array();
$scrolls=array();
$mouses=array();
$clicks=array();
while($row=$db->f()){
  $tics[]=$row['time_elapsed'];
  $height=$row['height'];

  // TODO: refactor mouse/clicks
  $tmp=explode("|",$row['mouse_movements']);
  if(count($tmp) > 1){
    $mouses[]=
      "Array(" . 
      implode(",",
              array_map('quote_dimensions',$tmp)) .
      ")";
  }else{
    $mouses[]=quote_dimensions($tmp[0]);;
  }

  
  $tmp=explode("|",$row['clicks']);
  if(count($tmp) > 1){
    $clicks[]=
      "Array(" . 
      implode(",",
              array_map('quote_dimensions',$tmp)) .
      ")";
  }else{
    $clicks[]=quote_dimensions($tmp[0]);;
  }


  $tmp=
    array_map(create_function('$s','return max(0,$s -' . $height . ');'),
              explode("|",$row['scroll']));
  if(count($tmp) > 1 ){
    $scrolls[]="Array(" . implode(",",$tmp) . ")";
  }else{
    $scrolls[]="{$tmp[0]}";
  }

}

?>

jQuery(function($){
  $.scrollTo(0);

  $('body').append("<img id='dfhu_dot' style='position:absolute;top:0;left:0;z-index:80' src='../ft_server/dot.png'>");
  
<?php
  print_js_array("scrollMotion",$scrolls);
  print_js_array("mouseMotion",$mouses);        
  print_js_array("clicks",$clicks);
  print_js_array("tics",$tics);
?>

  function placeClick(pos){
    if(!pos[0] ||  !pos[1])
      return;

    // these corrections are just eyeballed
    //pos[0]=(pos[0]*1)-35;
    //pos[1]=(pos[1]*1)-12;

    $('body').append("<img id='dfhu_dot' " +
                     "style='position:absolute;left:" + 
                     pos[0] + "px" +  
                     ";top:" +  
                     pos[1] + "px" +
                     ";z-index:79'" +
                     " src='../ft_server/cdot.png'>");
  }

  function moveMouse(pos){
    if(!pos[0] || !pos[1])
      return;

    // these corrections are just eyeballed
    //pos[0]=(pos[0]*1)-35;
    //pos[1]=(pos[1]*1)-12;


    $("#dfhu_dot")
      .animate({
        left: pos[0] + "px",
        top: pos[1] + "px"},
               20);
          
    $("#dfhu_dot").css('left',pos[0] + "px");
    $("#dfhu_dot").css('top',pos[1] + "px");

    return;
  }

    
  var tic=0;
  var step=0;
  function move(){

    if(tic >= scrollMotion.length){
      return;
    }

    // wait until the step time catches up to the current step
    step=step+5;
    if(tics[tic] > step){
      return;
    }

    if(scrollMotion[tic] != 0){
      if(typeof(scrollMotion[tic]) == "object"){
        for(ii in scrollMotion[tic]){
          $.scrollTo(scrollMotion[tic][ii],20);
        }
      }else{
        $.scrollTo(scrollMotion[tic],20);
      }
    }

    if(typeof(mouseMotion[tic]) == "object"){
      for(ii in mouseMotion[tic]){
        moveMouse(mouseMotion[tic][ii]);
      }
    }else{
      moveMouse(mouseMotion[tic]);
    }

    if(typeof(clicks[tic]) == "array"){
      for(ii in clicks[tic]){
        placeClick(clicks[tic][ii]);
      }
    }else{
      placeClick(clicks[tic]);
    }

    tic=tic+1;
  }// move()
      

  setInterval(move,250);

});