<?php
namespace NamingConventions;
function convert_case($text, $from, $to) {
  $from_case = "\\NamingConventions\\from_" . $from;
  $to_case = "\\NamingConventions\\to_" . $to;
  return $to_case($from_case($text));
}

// PascalCase
function from_pascal($text) {
  $rtn = [];
  $part = "";
  foreach(str_split($text) as $c) {
    if(ctype_upper($c)) {
      if(strlen($part) != 0)
        $rtn[] = $part;
      $part = strtolower($c);
    } else {
      $part .= $c;
    }
  }
  if(strlen($part) != 0)
    $rtn[] = $part;
  return $rtn;
}
function to_pascal($p) {
  foreach($p as $i=>$part) {
    $p[$i] = ucfirst($part);
  }
  return implode("", $p);
}


// lower_case
function from_lower($text) {
  return explode("_", $text);
}
function to_lower($p) {
  return implode("_", $p);
}

// Proper_Case
function from_proper($text) {
  return explode("_", strtolower($text));
}
function to_proper($p) {
  foreach($p as $i=>$part) {
    $p[$i] = ucfirst($part);
  }
  return implode("_", $p);
}

// camelCase
function from_camel($text) {
  return explode("_", strtolower($text));
}
function to_camel($p) {
  $first = true;
  foreach($p as $i=>$part) {
    if($first == true) {
      $first = false;
      continue;
    }
    $p[$i] = ucfirst($part);
  }
  return implode("", $p);
}

