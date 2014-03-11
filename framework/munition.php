<?php
spl_autoload_register(function($class){
    $class = classname_to_filename(str_replace('\\', '/', $class));
    if(file_exists('./' . $class . '.php')) {
      require_once('./' . $class . '.php');
    }
});

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

define("MUNITION_ROOT", dirname($_SERVER['SCRIPT_FILENAME']));
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