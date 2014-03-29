<?php
class TestController extends \framework\base\AppController {
  function __construct() {
    $this->before_action([$this, "filter_all_actions"]);
    
    
    $this->before_action([$this, "filter_some_actions"], "test_filters1");
    
    $this->before_action([$this, "filter_allbutsome_actions"],
    ["not" => [
      "test_filters2",
      "index",
      "not_found"
    ]]);
    
  }
  
  function index($scope) {
    self::render($scope, [200, "json" => ["oi_mate" => "hellow!"]]);
  }
  
  
  function not_found($scope) {
    self::render($scope, [404, "json" => ["error" => "Not Found"]]);
  }
  
  // Test filters
  function test_filters1($scope) {
    self::render([403, "nothing" => true]);
  }
  function test_filters2($scope) {
    self::render([403, "nothing" => true]);
  }
  
  
  protected function filter_all_actions($scope) {
    return $scope;
  }
  protected function filter_some_actions($scope) {
    self::render([422, "nothing" => true]);
  }
  protected function filter_allbutsome_actions($scope) {
    self::render([422, "nothing" => true]);
  }
}