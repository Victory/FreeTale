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

$Date:: 2009-08-18 02:27:40 #$:
$Rev:: 8                     $:

*/

jQuery(function($){

  // do not record if we are in an iframe
  if(window.top != window){
    return;
  }
  
  var tic=now();
  var scrollMotion = new Array();
  scrollMotion[0] = lowestVisible();
  var mouseMotion = new Array();
  var mouseClicks = new Array();

  // store mouse movements
  $(document).mousemove(function(e){
    mouseMotion[mouseMotion.length] =
      mouseCoordinates(e);
  });

  // store scroll events
  $(document).scroll(function(e){
    scrollMotion[scrollMotion.length] =
      lowestVisible();
  });

  // store click coordinates
  $(document).click(function(e){

    // e.button
    mouseClicks[mouseClicks.length] =
      mouseCoordinates(e);
  });

  function mouseCoordinates(e){
    // get X by Y coordinates of the mouse at event e
    return e.pageX +'x'+ e.pageY;
  }

  function lowestVisible(){
    // Get the lowest Visible point in the browser 
    return $(document).scrollTop()+$(window).height();
  }

  function now(){
    var now=new Date();
    return Math.floor(now.getTime()/100);
  }


  function conciseQuery(queryName,vals,maxLength){
    /* 

    Create a &foo[]=132&foo[]=456&foo[]=784 type string from queryName
    "foo" and vals which is an array of vals
    
    @param string queryName - the name of the variable as seen in the
    query string

    @param array vals - array of values to append to query string    

    @param int maxLength - the maximum number of values to append, if
    vals.length > maxLength then we step through the array skiping
    some vals

     */
 
    // We set the step size to limit the length of query, this is just
    // the number of values we will append.
    var step=Math.ceil(vals.length/maxLength);
   
    var val="";
    // iterate over all the vals and ...
    for(i in vals){
      // ... if we are on step'th value, then ..
      if( i % step == 0){
	// ... append the &foo[]=123 type string
	val=val + "&" +
	  queryName + "[]=" +
	  vals[i];
      }
    }

    // if we had no vals, then just send an empty var
    if(val == ""){
      val="&" + queryName + "[]=";
    }
    return val;
  }

  function getMotionQuery(){
    /*

     Create a query string from the recorded montions and clicks

    */
    
    var maxLength=10;
    var windowWidth = $(window).width();
    var windowHeight = $(window).height();
    var elapsedTime = now() - tic;
    var q;

    q="?" + 
      "i=" + $.cookie("dfhu_iseeyou") +
      "&r=" + urlencode(document.referrer) +
      "&l=" + urlencode(window.location) +
      "&w=" + windowWidth +
      "&h=" + windowHeight +
      "&t=" + elapsedTime +
      conciseQuery("c",mouseClicks,maxLength) +
      conciseQuery("m",mouseMotion,maxLength) +
      conciseQuery("s",scrollMotion,maxLength);

    if(q.length > 1950){
      return "";
    }

    return q;
  }

  function recordMotion(){
    /*

    Create a new Image, which has as its query string an encoding of
    the mousemotions, clicks and scrolls if any such motions have
    taken place.

    */
    
    if(scrollMotion.length == 0 &&
       mouseMotion.length == 0 &&
       mouseClicks.length == 0){
      return;
    }

    var q=getMotionQuery();    
    var trackingImg = new Image();
    trackingImg.src = tracking_gif  + q;

    //$('body').append(q + "<br><br>");

    scrollMotion = new Array();
    mouseMotion = new Array();
    mouseClicks = new Array();
  }


  function cookieCheck(){
    
    var cookieName='dfhu_iseeyou';

    // Session if, user
    var iseeyou_id;
    if(iseeyou_id=$.cookie(cookieName)){
      //alert(iseeyou_id);

      var tail=iseeyou_id
	.substring(iseeyou_id.length-1,iseeyou_id.length)*1;
      tail=tail+1;
      iseeyou_id=
	iseeyou_id.substring(0,
			     iseeyou_id.length-1) +
	+ tail;
      $.cookie(cookieName,
	       iseeyou_id, 
	       {expires: 7});
      return;
    }
    
    var rndId=
      Math.floor(999999*Math.random());
    
    $.cookie(cookieName,
	     rndId + "_1", 
	     {expires: 7});
    
  }

  cookieCheck();

  /**/
  window.setInterval(recordMotion,
		     500);
  /**/


});