<?php
define("SIMULATES_WEBSERVER", true);
$_SERVER["SERVER_SOFTWARE"] = "blabla apache blabla"; // Legit Apache version
$_SERVER["DOCUMENT_ROOT"] = str_replace("/tests", "", dirname(__FILE__));
$_SERVER["SCRIPT_FILENAME"] = $_SERVER["DOCUMENT_ROOT"] . "/index.php";
$_SERVER["REMOTE_ADDR"] = "127.0.0.1";
$_SERVER["REMOTE_PORT"] = 80;
// simulates HTTP request
function xhr($path, $method) {
  $_SERVER["REQUEST_URI"] = $path;
  $_SERVER["REQUEST_METHOD"] = $method;
}