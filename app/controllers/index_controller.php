<?php
class IndexController extends \framework\base\Controller {
  
  static function home($scope) {
    self::render(["template" => "index"]);
  }
  public static function server_info($scope) {
    self::render(["template" => "serverinfo"]);
  }
}