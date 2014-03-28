<?php
define("SIMULATES_WEBSERVER", true);
$_SERVER["SERVER_SOFTWARE"] = "blabla apache blabla"; // Legit Apache version
$_SERVER["DOCUMENT_ROOT"] = str_replace("/tests", "", dirname(__FILE__));
$_SERVER["SCRIPT_FILENAME"] = $_SERVER["DOCUMENT_ROOT"] . "/index.php";
$_SERVER["REMOTE_ADDR"] = "127.0.0.1";
$_SERVER["REMOTE_PORT"] = 80;
// simulates HTTP request
class XHR {
  private static $request_made = false;
  private static $response_code = 200;
  
	public static function request($path, $method) {
    self::$request_made = true;
    ob_start();
	  $_SERVER["REQUEST_URI"] = $path;
	  $_SERVER["REQUEST_METHOD"] = $method;
	}
  public static function response_code($code = 0) {
    if($code != 0) {
      XHR::$response_code = $code;
    }
    return XHR::$response_code;
  }
  public static function response() {
    if(self::$request_made == false) {
      throw new Exception("No request was made, unable to get request");
    } else {
      self::$request_made = false;
      return [XHR::$response_code, headers_list(), ob_get_clean()];
    }
  }
}