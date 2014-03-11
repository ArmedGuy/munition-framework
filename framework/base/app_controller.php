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
  
  protected static function render($scope, $settings = null ) {
    if($settings == null) {
      $settings = $scope;
    }
	
	
    if(isset($settings[0]) && is_numeric($settings[0])) {
      http_response_code($settings[0]);
    }
    if(isset($settings["nothing"]) && $settings["nothing"] === true) {
      exit;
    }
    if(isset($settings["json"])) {
      header("Content-type: application/json");
      echo $settings["json"];
    }
    if(isset($settings["template"])) {
      require (self::$template_base . $settings["template"] . ".php");
    }
  }
}