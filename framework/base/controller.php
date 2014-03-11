<?php
namespace framework\base;
class AppController {
  public static $template_base;
  
  protected static $before_filters = [];
  protected static before_filter($ctrlfn, $functions) {
    
  }
  
  
  
  
  static public function call_function($fn, $scope = [], $format = "html") {
    $c = get_called_class();
    call_user_func_array(array($c, $fn), array($scope, $format
  }
  
  static protected function render($settings) {
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