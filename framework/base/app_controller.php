<?php
namespace framework\base;
class AppController {
  public static $template_base;
  public static $controller_base;
  
  protected $before_filters = [];
  
  protected function before_filter($ctrlfn, $functions) {
    
  }
  
  
  
  public static function call_function($ctrlfn, $params = [], $scope = [], $format = "html") {
    if(strpos($ctrlfn, "#") === false || substr_count($ctrlfn, "#") !== 1) {
      throw new \Exception("Invalid controller path");
    }
    
    list($className, $fn) = self::load_controller($ctrlfn);
    
    $class = new $className();
    foreach($class->before_filters as $f => $cb) {
      if(in_array($fn, $cb)) {
        //TODO: handle before filter
      }
    }
    call_user_func_array(array($class, $fn), array($scope, $params, $format));
  }
  
  private static function load_controller($ctrlfn) {
    list($c, $function) = explode("#", $ctrlfn);
    $c .= "_controller";
    if(file_exists(self::$controller_base . $c . ".php")) {
      require_once self::$controller_base . $c . ".php";
      return [filename_to_classname($c), $function];
    } else {
      throw new \Exception("Controller not found!");
    }
    
  }
  
  protected static function render($scope, $__render_settings = null ) {
    if($__render_settings == null) {
      $__render_settings = $scope;
    } else {
      foreach($scope as $k => $v) {
        $$k = $v;
      }
    }
    unset($scope, $k, $v);
	
    if(isset($__render_settings[0]) && is_numeric($__render_settings[0])) {
      http_response_code($settings[0]);
    }
    if(isset($__render_settings["nothing"]) && $__render_settings["nothing"] === true) {
      exit;
    }
    if(isset($__render_settings["json"])) {
      header("Content-type: application/json");
      echo $__render_settings["json"];
    }
    if(isset($__render_settings["template"])) {
    
      require (self::$template_base . $__render_settings["template"] . ".php");
    }
  }
}