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
function pluralize($name) {
  if(substr($name, count($name)-1) == "s")
    return $name;
  else
    return $name . "s";
}
function singularize($name) {
  if(substr($name, count($name)-1) == "s")
    return substr($name, 0, count($name)-1);
  else
    return $name;
}

spl_autoload_register(function($class){
    $class = classname_to_filename(str_replace('\\', '/', $class));
    if(file_exists('./framework/lib/' . $class . '.php')) {
      require_once('./framework/lib/' . $class . '.php');
    }
});

$env = getenv("MUNITION_ENV");
define("MUNITION_ENV", in_array($env, ["production", "development", "test"])? $env : "development");
define("MUNITION_ROOT", dirname($_SERVER['SCRIPT_FILENAME']));

set_include_path(get_include_path() . PATH_SEPARATOR . MUNITION_ROOT . "/framework/lib");

if(MUNITION_ENV == "test" && !defined('SIMULATES_WEBSERVER')) return;

require 'web_constants.php';

// Used for testing RewriteRules
if(str_replace(array("/","\\"), "", __FILE__) === str_replace(array("/","\\"), "", $_SERVER["SCRIPT_FILENAME"])) {
  http_response_code(422);
}