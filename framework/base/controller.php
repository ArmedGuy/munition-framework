<?php
namespace framework\base;
class Controller {
  public static $template_base;
  
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