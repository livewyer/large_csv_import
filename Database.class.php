<?php
class Database {
  
  private static $dbconn;
  
  public static function dbconnect() {  
    if( !self::$dbconn ) {    
      $params = parse_ini_file('config.ini');
      if (false == $params) { die('cannot parse INI file'); } 
      self::$dbconn = new PDO( 
	'mysql:host='.$params['host'].';dbname=massive_csv', $params['user'], $params['password'], array(
	  PDO::ATTR_PERSISTENT => true
      )); 
    }
    return self::$dbconn;
  }

  public static function run_query($query,$values=array(),$stacktrace=false, $fetch_style = PDO::FETCH_ASSOC) {
    $clean = array();
    foreach( $values as $value ) {
      $clean[] = preg_replace( "/[\xa0\x80-\xFF]/", "", $value );
    }
    
    
    try {
      $prep = Database::dbconnect()->prepare($query);
      Database::dbconnect()->beginTransaction();
      $prep->execute($clean);
      if( substr($query, 0, 6) == 'INSERT' ) {
	$result = Database::dbconnect()->lastInsertId();
      } else {
	$result   = $prep->fetchAll($fetch_style);
      }
      Database::dbconnect()->commit();
    } catch(PDOException $e) {
  	  $stackTrace = print_r(debug_backtrace(),true);
	    $FH = fopen('php://stderr','w');
	    fwrite($FH,"<strong>Error : </strong>".$e->getMessage()."\n");
	    fwrite($FH,"<br><strong>Query : </strong>".$query."\n");
	    fwrite($FH,"Debug Backtrace : ".$stackTrace."\n");
	    fclose($FH);
    }
//     pg_free_result($result);
    return $result;
  } 
  
  public static function logit( $text ) {
    $FH = fopen( 'php://stderr', 'w' );
    if( is_string( $text ) || is_numeric( $text ) ) {
      fwrite( $FH, "$text\n" );
    }
    else {
      fwrite( $FH, print_r( $text, true ) );
    }
    fclose( $FH );
  }

  public static function create_vals($qty) {
    $vals = '';
    $qty = (int) $qty;
    $qty++;
    for($i=1; $i<$qty; $i++) {
      if( $i > 1 ) {
	$vals .=', ';
      }
      $vals .= "?";
    }
    return $vals;
  }

}
?>
