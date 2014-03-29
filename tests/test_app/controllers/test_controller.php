<?php
class TestController extends \framework\base\AppController {
  function __construct() {
    $this->before_action([$this, "filter_all_actions"]);
    
    
    $this->before_action([$this, "filter_some_actions"], "test_filters1");
    
    $this->before_action([$this, "filter_allbutsome_actions"],
    ["not" => [
      "test_filters2",
      "home",
      "verify_rewrite"
    ]]);
    
  }
  
  // Test actions
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