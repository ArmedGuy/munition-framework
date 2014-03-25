<?php
class AppTest extends PHPUnit_Framework_TestCase {
  
  public function testLoadApp() {
    $app = require './framework/install_config/application.php';
    $this->assertTrue($app->in_test_environment);  
    return $app;
  }
  
  /**
   * @depends testLoadApp
   */
  public function testRunAppWithPostProcessing($app) {
    xhr("/test", "GET");
    $success = false;
    $app->postprocess->queue(function() use(&$success) {
      $success = true;
    });
    $app->run();
    $response = xhr_response();
    
    $this->assertEqual(200, $response[0]);
    $this->assertTrue($success);
  }
  
  /**
   * @expectedException InvalidArgumentException
   */
  public function testInvalidAppDirectory() {
    $app = new \framework\base\App("./asdf_app/");
  }
  
  /**
   * @expectedException InvalidArgumentException
   */
  public function testInvalidRouterFile() {
    $app = new \framework\base\App("./app/", "./config/asdf_routes.php");
  }
}