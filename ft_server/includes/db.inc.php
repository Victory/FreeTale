<?php

/*

@author: Victory
@site: http://dfhu.org
@copyright: dfhu.org
@report_bugs: bugs(at)dfhu.org
@feature_request: features(at)dfhu.org
@file: scrollingheatmap/includes/db.inc.php
@license: BSD

@description:

  This file is great.

$Date:: 2009-08-16 07:14:40 #$:
$Rev:: 6                     $:

*/



class DB{
  /**
     Wrapper class for sqlite and mysql
     Author: Victory
     Version: 0.01 (unstable)



     @var PDO::Connection $this->c is the connection
     @var string $this->e is the engine
     
     @var string $this->name is name of the database
     @var string $this->esc_func is the name string escape function


   **/

  var $e; // name of the engine
  var $c; // connection
  var $name; // name of the database
  var $esc_func; // 'mysql_real_escape_string' or 'sqlite_escape'
  var $stmt; // the statment

  function __construct($db_name=DB_NAME,
		       $db_engine=DB_ENGINE,
		       $die=True){
    /*

      @parama $die if True die if the sqlite db file can't be open. If
        False try to create it

      Side Effects:

       @var string $this->name is name of the database
       @var string $this->esc_func is the name string escape function
       @var string $this->e name of the engine: 'sqlite' or 'mysql'

    */

    // The database's autograph might be worth lots of money someday,
    // so lets save it just in case.
    $this->name=$db_name;
    $engine=strtolower($db_engine);
    $this->e=$engine;

    // Before we get all crazy and start PDO'ing stuff, make sure we
    // are using a supported engine ...
    if($engine != "sqlite" and
       $engine != "mysql"){
      // ... if not, bitch and bail.
      $this->_errpage(
"Supported Databases are sqlite and mysql. 
Please Check your config and try again.");
    }

    // If we are using sqlite ...
    if($engine == "sqlite"){
      // ... save the sqlite escape function name.
      $this->esc_func="sqlite_escape_string";

      // Then try to ...
      try{
	// ... open the database, ...

	$rel=($this->name[0] != "/") ?
	  "./" : "";
	
	$this->c = new PDO("sqlite:$rel{$this->name}");
	// ... but if we can't open the database ...
      }catch(Exception $e){
	// ... then check to see if we were told to $die=True when
	// calling __construct() ...
	if($die){
	  // ... in which case bitch and bail.
	  $this->_errpage(
"Could not open sqlite database
'{$_SERVER['DOCUMENT_ROOT']}/{$this->name}' Make sure the file exists
and is writeable by the web server.");
	}
	// If we are still alive, try to create the database file now.
	$this->_create_db_file();
      }
    } 

    // On the other hand, if we are using MySQL ...
    if($engine == "mysql"){
      // ... save MySQL's RaPUNzel function (get it "escape string,"
      // like her hair, hehe ... oh whatever, you're no fun).
      $this->esc_func="mysql_real_escape_string";

      // Back to the plot, turn those globals into pretty string
      // variables so that we can ...
      $host=DB_HOST;
      $user=DB_USER;
      $pass=DB_PASS;

      // ... try to ...
      try{
	// ... open up the mysql database, ...
	$this->c = new PDO("mysql:host=$host;dbname={$this->name}",
			   $user,
			   $pass);
	// ... but if we cant ...
      }catch(Exception $e){
	// ... bitch and bail.
	$this->_errpage("Error Opening MySQL Database: " . $e->getMessage());
      }
    }// using mysql

    // Set it up so when PDO messes up or more likely when YOU messup,
    // PDO throw's an exception.
    $this->c->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  }// And thus concludes the story of __construct().

  function attach($name=CONFIG_DB_NAME,
		  $nickname="config"){

    /*

      In SQLite you can ATTACH other databases to put them in global
      scope. This is very handy for using config databases that you
      upload to all your instances of the program.

      @param string $name - the complete path on the server to the
        database file
      @param string $nickname - what the database will be refrenced
        AS. I.e. the initial dabase used to construct is by default
        called 'main'
      
     */

    // if not using sqlite ...
    if($this->e != "sqlite")
      // ... BAB.
      $this->_errpage("You must Use SQLite if You wish to ATTACH");

    // Now quote the database path ...
    $name_sql=$this->quote($name);
    // ... and escape the nickname.
    $nickname_sql=$this->escape($nickname);

    // Finally, create the sql statement ...
    $sql="ATTACH $name_sql AS $nickname_sql";
    // ... so that we can query the db with it.
    $this->q($sql);

  }// attach

  function bt(){
    $this->c->beginTransaction();
  }
  function c(){
    $this->c->commit();
  }

  function q($sql,$fetch_method=PDO::FETCH_ASSOC){
    // query 
    if($sql == "")
      throw new Exception("Empty SQL Statement");
    $this->q = $this->stmt = null;
    $this->q = $this->c->query($sql);
    $this->q->setFetchMode($fetch_method);
  }


  function qf($sql,$fetch_method=PDO::FETCH_ASSOC){
    $this->q($sql,$fetch_method);
    return $this->f();
  }

  function f($fetch_method=PDO::FETCH_ASSOC){
    // fetch one 
    if($this->q){
      return $this->q->fetch($fetch_method);
    }elseif($this->stmt){
      return $this->stmt->fetch($fetch_method);
    }

    throw new Exception("Neither $stmt nor $q is set");
  }

  function p($sql){
    // prepare a stateent
    $this->q = $this->stmt = null;
    $this->stmt = $this->c->prepare($sql);
  }

  function quote($var){
    // quote a string i.e. for insert/where values
    return $this->c->quote($var);
  }

  function escape($var){
    // escape a string, for santizing table names, column names etc.
    return call_user_func($this->esc_func,$var);
  }

  function exec($vals){
    // @param array $vals - is an array of values to insert in the
    // prepared statement
    $this->stmt->execute($vals);
  }

  private function _create_db_file(){
    /* Attempt to create the database for sqlite3. Then open it with
       new PDO() */

    $fp = @fopen("./"  . $this->name, 'w');
    @fwrite($fp,"");
    @fclose($fp);

    $this->__construct($die=True);
    
  }

  private function _errpage($msg,
			    $db_err="",
			    $title="Database Error"){
    ?>
    <html><head><title><?php echo htmlentities($title) ?></title></head>
      <body>
        <p class="db_err"><?php echo nl2br(htmlentities($msg)) ?></p>
        <p class="db_err"><?php echo nl2br(htmlentities($db_err)) ?></p>
      </body>
    </html>
    <?php
      exit;
  }




}// DB


class DBx extends DB{
  /**

     Adds common tasks to DB

   **/


  private static function _wrap_with($str,$wrapper){
    return $wrapper . $str . $wrapper;
  }

  function while_row($template,
		     $p=Array()){
    /*
      
      Ye old, while($r = $q->fetch()){}  loop
        
      @param string $template - a template string with delimiters (i.e
        $templatate="%firstname% %lastname%"). The default delimiter
        is '%' or it can be set $p['delimiter']='string'
      
      @param string $keys - Array("firstname","lastname")

      @named_params array $p  -
      
       $p=array(
         "before_each"=>"<li>", // string default
	 "after_each"=>"</li>", // string default 
         "before_all"=>"<ul>", // string default
         "after_all"=>"</ul>", // string default
	 "delimiter"=>"%" // what vars in $str are seprated by. default
        ); 

      EXAMPLE: 

       Using the values above

       $D->while_row($keys,$template,$p);

       Might give something like:

       <ul>
	<li>Alice Sneaky</li>
	<li>Bob Brown</li>
	<li>Eve Zarko</li>
       </ul>

    */

    // set up the before each and after each tags
    $be=isset($p['before_each']) ? $p['before_each'] : "  <li>";
    $ae=isset($p['after_each']) ? $p['after_each'] : "</li>\n";
    // set up the delimiter for keys
    $delimiter=isset($p['delimiter']) ? $p['delimiter'] : "%";
    
    /*
    // wrap the keys with the delimiter
    $vars=array_map("DBx::_wrap_with",
		    $keys,
		    array_fill(0,
			       count($keys),
			       $delimiter));
    */

    // buffer the output
    ob_start();
    echo isset($p['before_all']) ? $p['before_all'] : "<ul>\n";
    $vars=False;
    while($vals = $this->f()){

      if(!$vars){
	// wrap the keys with the delimiter
	$vars=array_map("DBx::_wrap_with",
			array_keys($vals),
			array_fill(0,
				   count($vals),
				   $delimiter));
      }

      echo $be;
      echo str_replace($vars,$vals,$template);      
      echo $ae;
    }
    echo isset($p['after_all']) ? $p['after_all'] : "</ul>\n";
    // ok flush to the screen
    ob_flush();
    
  }// while_row


  private function _show_tables_command(){
    
    if($this->e == "sqlite"){
      $sql="select tbl_name from sqlite_master where type='table'";
    }elseif($this->e == "mysql"){
      throw new Exception("_show_tables Not Implemented for MySQL yet");
      $sql="SHOW TABLES";
    }

    return $sql;
  }
  function show_tables(){

    $sql=$this->_show_tables_command();
    
    $this->q($sql,PDO::FETCH_NUM);

    $p=Array();
    $template="%tbl_name%";
    $this->while_row($template);
  }// show_tables


  function tables(){
    // return an array with table names

    $sql=$this->_show_tables_command();
    $this->q($sql,PDO::FETCH_NUM);
    
    $tables=Array();
    while($r=$this->f()){


      $tables[]=$r['tbl_name'];
    }

    return $tables;
  }



  private function _append_vars($vars,$delimiter,$transform=False){
    /*
      Makes var1,var2,var3 lists or var1 and var2 and var3 lists,
      escaping with $transform which is a callable function such as
      mysql_real_escape_string
    */
    
    if($transform and
       !is_callable($transform,false,$callable)){
      throw new Exception("Function or Method does not exists $transform");
    }
    
    if(is_array($vars)){
      if($transform)
	$v=implode($delimiter, array_map("$callable",$vars));
      else
	$v=implode($delimiter, $vars);

    }elseif(is_string($vars)){
      if($transform)
	$v=call_user_func("$callable",$vars);
      else
	$v=$vars;

    }else{
      throw new Exception("Must be string or array " . htmlentities("$vars"));
    }
    return $v;
  }


  function wheres($wheres,$and=True){
    /*
      get the  k='val' and k2='val2' and ...
      style string. 
     */

    if($and)
      $conjunction=" AND ";
    else
      $conjunction=" OR ";

    $where=Array();
    $cnt=0;
    foreach($wheres as $k=>$v){
      $cnt+=1;

      $k_sql=$this->escape($k);
      $v_sql=$this->quote($v);

      $where[]="$k_sql=$v_sql";
      if($cnt != count($wheres)){
	$where[]=$conjunction;
      } 
    }
    return implode("\n",$where);

  }// wheres

  

  function update($table,$updates,$wheres=Array(), $and=True){
    /*
      @param $table - the name of the table to work on
      @param $updates - is new values as a key=>value array
      @param $where - is the where clauses as key=>value array
      @param $and - if True " and " else " OR ";
     */

    $table_sql=$this->escape($table);

    $updates=$this->wheres($updates);

    $wheres="where " . $this->wheres($wheres,$and);

    $sql="UPDATE $table_sql SET $updates $wheres";
    $this->q($sql);

  }


  function set($table,$p){
    /*

$p=Array("key"=>"value","key2"=>"value2");

or

$p=Array(Array("key"=>"value0_0","key2=>"value1_0"),
         Array("ignored"=>"value0_1","ignored","value1_1")...)

This Function Ignores ON DUPLICATE.

     */
    
    if(isset($p[0])){
      $kv=$p[0];
      $many=True;
    }else{
      $kv=$p;
      $many=False;
    }

    $columns_sql=$this->_append_vars(array_keys($kv),", ",$this->esc_func);
    
    $setup=$this->_append_vars(array_fill(0,count($kv),"?"),", ");

    $table_sql=$this->escape($table);

    $sql="INSERT INTO $table_sql ($columns_sql) VALUES ($setup)";
    
    $this->p($sql);

    if($many){
      foreach($p as $kv){
	$this->_ignore_duplicate($kv);
      }
    }else{
      $this->_ignore_duplicate($kv);
    }
  }//set
  
  private function _ignore_duplicate($kv){
    try {
      $this->stmt->execute(array_values($kv));
    }catch(Exception $e){
      if(!preg_match("/(is not unique$|Duplicate entry '.*' for key)/",$e->getMessage())){
	throw $e;
      }
    }
  }// _ignore_duplicate

  function get($table,   // name of the table
	       $columns, // if array listed columns, if string one or *
	       $wheres=Array(),    // array of key=>$value pairs
	       $limit=1,
	       $and=True // True = 'and' False = 'or'
	       ){
    /*
      select $columns_sql from $table WHERE $wheres_sql limit $limit; 
     */

    $columns_sql=$this->_append_vars($columns,", ",$this->esc_func);

    $table_sql=$this->escape($table);

    $limit=intval($limit);

    if(count($wheres) > 0)
      $wheres_sql="WHERE " . $this->wheres($wheres, $and);
    else
      $wheres_sql="";

    $sql="SELECT $columns_sql FROM $table_sql $wheres_sql LIMIT $limit";
    $this->q($sql);

  }// get

  function str_now(){
    if($this->e == "mysql"){
      return "NOW()";
    }
    if($this->e == "sqlite"){
      return "DATETIME('NOW')";
    }
  }// str_now

  function str_int_id(){
    /* 
       Helper function because auto increment integer id's are not
       created the same in sqlite3 and mysql, granted they are
       automagically created as ROWID in sqlite
    */
    if($this->e == "sqlite"){
      return "`id` INTEGER PRIMARY KEY,";
    }
    if($this->e == "mysql"){
      return "`id` INT PRIMARY KEY AUTO_INCREMENT,";
    }
  }// str_int_id

}// DBx

class CRUD extends DBx{
  function __construct($db_name=DB_NAME){
    parent::__construct();
  }
}

?>