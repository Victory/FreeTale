jQuery(function($){

  var focus=new Array();
  var blur=new Array();
  var keyupCount={};
  var freetaleCookieName='freetale_id';

  function now(){
    var now=new Date().getTime();
    return Math.ceil(now/1000);
  }
  function getParentFormId(o){
    var id="";
    id=o.parent("form").attr("id")
    if(typeof(id) == 'undefined'){
      id=o.attr("id");
    }
    return id;
  }
  function getName(o){
    
   /*
    
   TODO: deal with foo[] type names, maybe by replaing '[' and ']'
   with like '||' and '|||'

   */

    return o.attr("name");
  }

  var initTime=-1;
  function getElapsedTime(){
    /*
       return the elapsed time since Init. initTime start on the first
       keyup on an input/textarea element
    */
    return now()-initTime;
  }
  function isClocking(){
    return initTime != -1;
  }
  function startClocking(){
    initTime=now();
  }

  function getClockString(thisObject){
    /* 

       Returns Formated strings 

       @param $(this) thisObject - element in a form

       @return string - something slike focus::theform::email::30
    */
    return getParentFormId(thisObject) +
      "::" +
      getName(thisObject) +
      "::" +
      getElapsedTime();
  }

  function updateKeyupCount(name){
    if(typeof(keyupCount[name]) == 'undefined')
      keyupCount[name]=0;
    else
      keyupCount[name]=keyupCount[name]+1;
  }

  function getKeyupCountString(name){
    return name + "::" + keyupCount[name];
  }

  function getActionQuery(actionType,actionArray){
    var val = "";
    for(ii in actionArray){
      val = val + "&" +
	actionType + "[]=" +
	urlencode(actionArray[ii]);
    }

    if(val == ""){
      val="&" + actionType + "[]=";
    }
    return val;
  }

  function getKeyQuery(actionType,actionObject){
    var val= "";
    for(name in actionObject){
      val= val + "&" + 
	actionType + 
	"[" + urlencode(name) + "]=" +
	urlencode(actionObject[name]);
    }
    return val;
  }

  function getFormActionQuery(isSubmit){
    var q;

    q="?" +
      "i=" + $.cookie(freetaleCookieName) +
      "&r=" + urlencode(document.referrer) +
      "&l=" + urlencode(window.location) +
      "&s=" + isSubmit +
      getActionQuery('b',blur) +
      //getActionQuery('f',focus) +
      getKeyQuery('k',keyupCount)

    return q;
  }


  function timeAction(elements){
    for(ii in elements){

      $(elements[ii]).
	bind('focus',function(){
	  if(!isClocking())
	    return;
	  startClocking();

	  //focus[focus.length] = getClockString($(this));
	  //$('body').append("focus - " + getClockString($(this)) + "<br>");
	});
      $(elements[ii]).
	bind('blur',function(){
	  if(!isClocking())
	    return;
	  blur[blur.length]=getClockString($(this));
	  // send for the form_gif.php, the 'false' indicates that
	  // this is not a submit;
	  
	  $('body').append("blur - " + getClockString($(this)) + "<br>");
	  storeActions(false);

	});


      // We record the number of keyup events, for each named
      // input/textarea on the page. Recoding of form action only
      // starts when a named input starts getting input. We use this
      // istead of the first on focus because, sometimes forms get
      // focus before the user is ready to fill out the form.
      $(elements[ii]).
	bind('keyup',function(e){
	  

	  // If we haven't started clocking then ...
	  if(!isClocking())
	    // ... its about time we start
	    startClocking();


	  // the JSON object uses the name to refrence.
	  var name=$(this).attr('name');

	  // a rose by any other name is still a rose, but a rose with
	  // an undefined name is a no good, dirty tramp.
	  if(typeof(name) == 'undefined')
	    return;

	  // record the keyup event (just a counter)
	  updateKeyupCount(name);

	  //$('body').append(getKeyupCountString(name) + "<br>");
	  
	});

    }
  }// timeIt


  function storeActions(isSubmit){
    
    var q=getFormActionQuery(isSubmit);
    form_gif_location="http://site/FreeTale/ft_server/form_gif.php" 
    
    var gifLocation=form_gif_location+q;

    //var formGif=new Image();
    //formGif.src = gifLocation;

    //$('body').append("<br>" + gifLocation + "<br>");
    
    focus=new Array();
    blur=new Array();
    keyupCount={};


  }


  $(document).bind("submit",function(){
    var id=$(this).attr('id');

    storeActions(true);

    //alert(getFormActionQuery());
    
    return false;
  });


  
  var actionElements=
    new Array(
      "textarea",
      "input[type='text']",  
      "input[type='password']",
      "input[type='file']",
      "input[type='radio']",
      "input[type='checkbox']");

  timeAction(actionElements);

});