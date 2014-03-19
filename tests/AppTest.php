<?php

include 'xhr.php';
require './framework/munition.php';
class AppTest extends PHPUnit_Framework_TestCase {
  
  public function loadApp() {
    $app = require './config/application.php';
    $this->assertTrue($app->in_test_environment);
    
  }
}