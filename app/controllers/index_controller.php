<?php
class IndexController extends \framework\base\Controller {
  
  static function home() {
    self::render(["template" => "index"]);
  }
  public static function server_info() {
    self::render(["template" => "serverinfo"]);
  }
}