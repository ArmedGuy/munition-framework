<?php
class AppTest extends PHPUnit_Framework_TestCase {
  
  public function testLoadApp() {
    $app = require './tests/test_config/application.php';
    $this->assertTrue($app->in_test_environment);  
    return $app;
  }
  
  /**
   * @depends testLoadApp
   */
  public function testRunAppWithPostProcessing($app) {
    XHR::request("/?hi=hello", "GET");
    $success = false;
    $app->postprocess->queue(function() use(&$success) {
      $success = true;
    });
    $app->run();
    
    list($code, $headers, $body) = XHR::response();
    
    $this->assertEquals(200, $code);
    $this->assertTrue($success);
    
    return $app;
  }
  
  /**
   * @depends testRunAppWithPostProcessing
   */
  public function testIncludeModel($app) {
    $t = new Testmodel();
    $this->assertTrue($t->testValue);
  }
  
  public function testControllerFilters() {
    $app = require './tests/test_config/application.php';
    
    XHR::request("/test_filters1", "GET");
    $app->run();
    
    list($code, $headers, $body) = XHR::response();
    $this->assertEquals(422, $code);
    
    
    XHR::request("/test_filters2", "GET");
    $app->run();
    list($code, $headers, $body) = XHR::response();
    $this->assertEquals(403, $code);
  }
  
  
  /**
   * @expectedException InvalidArgumentException
   */
  public function testInvalidAppDirectory() {
    $app = new \Munition\App("./asdf_app/");
  }
  
  /**
   * @expectedException InvalidArgumentException
   */
  public function testInvalidRouterFile() {
    $app = new \Munition\App("./app/", "./config/asdf_routes.php");
  }
}