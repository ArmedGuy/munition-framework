<?php
class AppTest extends PHPUnit_Framework_TestCase {
  
  public function testLoadApp() {
    $app = require './config/application.php';
    $this->assertTrue($app->in_test_environment);
    
  }
}