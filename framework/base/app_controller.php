<?php
namespace framework\base;
class AppController {
  public static $template_base;
  public static $controller_base;
  
  protected $app = null;
  
  private $_before_filters = [];
  
  
  protected function before_action($filter, $functions = []) {
    $this->_before_filters[] = [$filter, $functions];
  }
  
  
  protected function handle_action($fn, $params, $scope, $format) {
    foreach($this->_before_filters as $filter) {
      list($f, $cb) = $filter;
      
      if(is_array($cb)) {
        if(isset($cb["only"]) && !in_array($fn, $cb["only"]))
          continue;
        if(isset($cb["not"]) && in_array($fn, $cb["not"]))
          continue;
        
        $scope = call_user_func($f, $scope);
        if($scope === null) {
          return; // Assumes the filter has handled output etc
        }
      } elseif (is_string($cb) && $fn == $cb) {
        $scope = call_user_func($f, $scope);
        if($scope === null) {
          return; // Assumes the filter has handled output etc
        }
      }
    }
    $this->$fn($scope, $params, $format);
  }
  
  
  public static function call_function($ctrlfn, $params = [], $scope = [], $format = "html", $app = null) {
    list($className, $fn) = self::load_controller($ctrlfn);
    $params["controller"] = $className;
    $params["action"] = $fn;
    
    $class = new $className();
    $class->app = $app;
    $class->handle_action($fn, $params, $scope, $format);
  }
  
  private static function load_controller($ctrlfn) {
    if(strpos($ctrlfn, "#") === false || substr_count($ctrlfn, "#") !== 1) {
      throw new \InvalidArgumentException("Invalid controller path");
    }
    list($c, $function) = explode("#", $ctrlfn);
    $c .= "_controller";
    if(file_exists(self::$controller_base . $c . ".php")) {
      require_once self::$controller_base . $c . ".php";
      return [filename_to_classname($c), $function];
    } else {
      throw new \InvalidArgumentException("Controller not found!");
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
      if(MUNITION_ENV != "test") {
        http_response_code($__render_settings[0]);
      } else {
        \XHR::response_code($__render_settings[0]);
      }
    }
    if(isset($__render_settings["nothing"]) && $__render_settings["nothing"] === true) {
      return;
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