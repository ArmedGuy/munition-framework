<?php
include './framework/munition.php';

class RouterTest extends PHPUnit_Framework_TestCase {
  
  public function testRouterGet() {
    $router = new \framework\base\Router();
    $success = false;
    $router->get("/", function($scope, $params, $format) use (&$success) {
      $success = true;
    });
    
    $router->route("/");
    
    $this->assertTrue($success);
    
  }
  
  public function testRouterPost() {
    $router = new \framework\base\Router();
    $success = false;
    $router->post("/post", function($scope, $params, $format) use (&$success) {
      $success = true;
    });
    $router->route("/post", "POST");
    
    $this->assertTrue($success);
    
  }
  
  public function testRouterError() {
    $router = new \framework\base\Router();
    $success = false;
    
    $router->error("404", function($scope, $params, $format) use (&$success) {
      $success = true;
    });
    $router->route("/asdf");
    
    $this->assertTrue($success);
  }
}