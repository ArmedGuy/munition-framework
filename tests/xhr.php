<?php
define("SIMULATES_WEBSERVER", true);
$_SERVER["SERVER_SOFTWARE"] = "blabla apache blabla"; // Legit Apache version
$_SERVER["DOCUMENT_ROOT"] = str_replace("/tests", "", dirname(__FILE__));
$_SERVER["SCRIPT_FILENAME"] = $_SERVER["DOCUMENT_ROOT"] . "/index.php";
$_SERVER["REMOTE_ADDR"] = "127.0.0.1";
$_SERVER["REMOTE_PORT"] = 80;
// simulates HTTP request
$_xhr_request_made = false;
function xhr($path, $method) {
  $_xhr_request_made = true;
  ob_start();
  $_SERVER["REQUEST_URI"] = $path;
  $_SERVER["REQUEST_METHOD"] = $method;
}

function xhr_response() {
  if(!$_xhr_request_made) {
    throw new Exception("No request was made, unable to get request");
  } else {
    $_xhr_request_made = true;
    return [http_response_code(), get_headers($), ob_get_clean()];
  }
}