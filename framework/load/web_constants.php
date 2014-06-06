<?php
define("MUNITION_WEBPATH", str_replace($_SERVER["DOCUMENT_ROOT"], "", dirname($_SERVER["SCRIPT_FILENAME"])) . "/");
$sw = strtolower($_SERVER['SERVER_SOFTWARE']);
$webserver = "";
foreach(["nginx", "lighttpd", "apache"] as $server) {
  if(strpos($sw, $server) !== false) {
    $webserver = $server; break;
  }
}
if($webserver == "") $webserver = "default";
define("MUNITION_WEBSERVER", $webserver);