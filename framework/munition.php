<?php

function filename_to_classname($file) {
  $file = strtolower($file);
  if(strpos($file, ".") !== false) {
    $file = pathinfo($file, PATHINFO_FILENAME);
  }
  if(strpos($file, "_") !== false) {
    $p = explode("_", $file);
    foreach($p as $i=>$part) {
      $p[$i] = ucfirst($part);
    }
    return implode("", $p);
  } else {
    return ucfirst($file);
  }
}

function classname_to_filename($class) {
  $filename = "";
  $last = "";
  foreach(str_split($class) as $c) {
    if(ctype_upper($c)) {
      if($filename == "" || $last == "/") {
        $filename .= strtolower($c);
      } else {
        $filename .= "_" . strtolower($c);
      }
    } else {
      $filename .= $c;
    }
    $last = $c;
  }
  return $filename;
}

spl_autoload_register(function($class){
    $class = classname_to_filename(str_replace('\\', '/', $class));
    if(file_exists('./' . $class . '.php')) {
      require_once('./' . $class . '.php');
    }
});


define("MUNITION_ENV", getenv("MUNITION_ENV") === "production" ? "production" : "development");
define("MUNITION_ROOT", dirname($_SERVER['SCRIPT_FILENAME']));
define("MUNITION_WEBPATH", str_replace($_SERVER["DOCUMENT_ROOT"], "", dirname($_SERVER["SCRIPT_FILENAME"])) . "/");

set_include_path(get_include_path() . PATH_SEPARATOR . MUNITION_ROOT . "/framework/lib");
set_include_path(get_include_path() . PATH_SEPARATOR . MUNITION_ROOT . "/app/lib");

$sw = strtolower($_SERVER['SERVER_SOFTWARE']);
$webserver = "";
foreach(["nginx", "lighttpd", "apache"] as $server) {
  if(strpos($sw, $server) !== false) {
    $webserver = $server; break;
  }
}
if($webserver == "") $webserver = "default";
define("MUNITION_WEBSERVER", $webserver);

// Used for testing RewriteRules
if(str_replace(array("/","\\"), "", __FILE__) === str_replace(array("/","\\"), "", $_SERVER["SCRIPT_FILENAME"])) {
  http_response_code(422);
}