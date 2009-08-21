jQuery(function($){


  var blur="";
  var keyupCount=0;
  var freetaleCookieName='freetaleform_id';

  function cookieCheck(){

    // if we already have a cookie, just use it
    if(freetale_id=$.cookie(freetaleCookieName)){
      return;
    }
    // other wise we generate one
    var rndId=
      Math.floor(999999*Math.random());
    
    $.cookie(freetaleCookieName,
	     rndId + "_1", 
	     {expires: 7});
    
  };
  cookieCheck();


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

       @return string - something slike theform||firstname||30
    */
    return getParentFormId(thisObject) +
      "||" +
      getName(thisObject) +
      "||" +
      getElapsedTime();
  }

  function updateKeyupCount(name){
      keyupCount=keyupCount+1;
  }

  function getKeyupCountString(){
    return keyupCount;
  }

  function getBlurQuery(blur){
    var bits=blur.split("||");

    var val = 
      "&f=" + bits[0] +
      "&n=" + bits[1] +
      "&t=" + bits[2];
    
    return val;
  }

  function getFormActionQuery(isSubmit){
    var q;

    q="?" +
      "i=" + $.cookie(freetaleCookieName) +
      "&r=" + urlencode(document.referrer) +
      "&l=" + urlencode(window.location) +
      "&s=" + isSubmit +
      getBlurQuery(blur) +
      "&k=" + keyupCount;

    return q;
  }

  function inputFocus(){
    if(!isClocking())
      return;
    startClocking();
  }

  function inputBlur(){
    if(!isClocking())
      return;
    blur=getClockString($(this));
    
    $('body').append("blur - " + getClockString($(this)) + "<br>");
    
    // send for the form_gif.php
    // TODO: need to fix this for textarea
    storeActions($(this).attr('type'));

  }

  function inputKeyup(){

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
    
  }

  function bindActions(elements){
    for(ii in elements){

      // We just brute force unbind all the elements, so we dont'
      // windup doing multiple binds. Yes we call bindActions after
      // every call to storeActions. Why? Well, because if your web
      // 2.0 deally ads new elements to the form on some action like
      // "choose state->choose town" then we need to pick up the new
      // elements too.
      $(elements[ii]).unbind('focus',inputFocus);
      $(elements[ii]).unbind('blur',inputBlur);
      $(elements[ii]).unbind('keyup',inputKeyup);

      // on focus we (re)start the clock, read the keyup comment too.
      $(elements[ii]).
	bind('focus',inputFocus);

      // when we blur we do a storeActions.
      $(elements[ii]).
	bind('blur',inputBlur);

      // We record the number of keyup events, for each named
      // input/textarea on the page. Recoding of form action only
      // starts when a named input starts getting input. We use this
      // istead of the first on focus because, sometimes forms get
      // focus before the user is ready to fill them out.
      $(elements[ii]).
	bind('keyup',inputKeyup);

    }
  }// timeIt

  function storeActions(isSubmit){
    
    var q=getFormActionQuery(isSubmit);
    form_gif_location="http://site/FreeTale/ft_server/form_gif.php" 
    
    var gifLocation=form_gif_location+q;

    var formGif=new Image();
    formGif.src = gifLocation;

    $('body').append("<br>" + gifLocation + "<br>");
    
    blur=new Array();
    keyupCount=0;

    /* 

    We rebind, incase the users action results in new form elements
    being added. Like if the user selected "state" and so now there is
    a "city" element.
 
   */
    bindActions(actionElements);
  }

  $(document).bind("submit",function(){
    var id=$(this).attr('id');
    storeActions('submit');    
    //return false;
  });

  
  var actionElements=
    new Array(
      "textarea",
      "select",
      "input[type='text']",  
      "input[type='password']",
      "input[type='file']",
      "input[type='radio']",
      "input[type='checkbox']");

  bindActions(actionElements);

  // we start off with a faux blur just to say, "hey! i landed." I
  // thought of the idea while standing in baggage reclaim.
  storeActions('landing');

});