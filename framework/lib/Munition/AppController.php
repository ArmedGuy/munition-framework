<?php
namespace Munition;
class AppController {
  public static $template_base;
  public static $controller_base;
  
  protected $app = null;
  
  private $_before_filters = [];
  
  
  protected function beforeAction($filter, $functions = []) {
    $this->_before_filters[] = [$filter, $functions];
  }
  
  
  protected function _handleAction($fn, array $context, array $params, $format) {
    foreach($this->_before_filters as $filter) {
      list($f, $cb) = $filter;
      
      if(is_array($cb)) {
        if(isset($cb["only"]) && !in_array($fn, $cb["only"]))
          continue;
        if(isset($cb["not"]) && in_array($fn, $cb["not"]))
          continue;
        
        $scope = call_user_func($f, $context);
        if($scope === null) {
          return; // Assumes the filter has handled output etc
        }
      } elseif (is_string($cb) && $fn == $cb) {
        $scope = call_user_func($f, $context);
        if($scope === null) {
          return; // Assumes the filter has handled output etc
        }
      }
    }
    $this->$fn($context, $params, $format);
  }
  
  
  public static function call_controller_function($ctrlfn, array $context = [], array $params = [],  $format = "html", $app = null) {
    list($className, $fn) = self::_load_controller($ctrlfn);
    $context["controller"] = $className;
    $context["action"] = $fn;
    
    $class = new $className();
    $class->app = $app;
    $class->_handleAction($fn, $context, $params, $format);
  }
  
  protected static function _load_controller($ctrlfn) {
    if(strpos($ctrlfn, "#") === false || substr_count($ctrlfn, "#") !== 1) {
      throw new \InvalidArgumentException("Invalid controller path");
    }
    list($c, $function) = explode("#", $ctrlfn);
    $c .= "_controller";
    $c = \NamingConventions\convert_case($c, "lower", "pascal");
    if(file_exists(self::$controller_base . $c . ".php")) {
      require_once self::$controller_base . $c . ".php";
      return [$c, $function];
    } else {
      throw new \InvalidArgumentException("Controller '$c' not found!");
    }
    
  }
  
  public static function render($context, $__render_settings = null ) {
    if($__render_settings == null) {
      $__render_settings = $context;
    } else {
      foreach($context as $k => $v) {
        $$k = $v;
      }
    }
    unset($context, $k, $v);
	
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
      if(MUNITION_ENV != "test"):
        header("Content-type: application/json");
      endif;
      echo json_encode($__render_settings["json"]);
    }
    if(isset($__render_settings["template"])) {
      $template_folder = self::$template_base;
      require (self::$template_base . $__render_settings["template"] . ".php");
    }
  }
  
  public static function redirect_to($to) {
    header("Location: $to");
  }
}