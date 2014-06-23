<?php
namespace Munition;
class I18n {
  private static $_localizations = [];
  public static $current = "common";
  
  public static function loadFolder($folder) {
    $contents = scandir($folder);
    $contents = array_diff($contents, ["..", "."]);
    foreach($contents as $c) {
      if(is_file($folder . "/" . $c) && strpos($c, ".php") !== false) {
        include_once $folder . "/" . $c;
      }
    }
  }
  
  public static function definition($locale, array $def) {
    if(isset(self::$_localizations[$locale])) {
      self::$_localizations[$locale] = array_merge(self::$_localizations[$locale], $def);
    } else {
      self::$_localizations[$locale] = $def;
    }
  }
  
  public static function t($src) {
    $str = self::lookup($src);
    if($str == null) {
      return "{Unknown locale: " . $src . "}";
    }
    $args = func_get_args();
    unset($args[0]);
    
    return call_user_func_array("sprintf", array_merge([$str], $args));
  }
  
  private static function lookup($src) {
    $l = self::$_localizations[self::$current];
    if(strpos($src, ".") !== false) {
      $p = explode(".", $src);
      $a = $l;
      foreach($p as $part) {
        if(isset($a[$part])) {
          if(is_array($a[$part])) {
            $a = $a[$part];
          } else {
            return $a[$part];
          }
        }
      }
      return null;
    } else {
      if(isset($l[$src])) {
        return $l[$src];
      } else {
        return null;
      }
    }
  }
}