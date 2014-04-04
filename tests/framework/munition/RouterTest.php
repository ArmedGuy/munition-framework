<?php
class RouterTest extends PHPUnit_Framework_TestCase {
  
  public function testRouterGet() {
    $router = new \Munition\Router();
    $success = false;
    $router->get("/", function($scope, $params, $format) use (&$success) {
      $success = true;
    });
    
    $router->route("/");
    
    $this->assertTrue($success);
    
  }
  
  public function testRouterPost() {
    $router = new \Munition\Router();
    $success = false;
    $router->post("/post", function($scope, $params, $format) use (&$success) {
      $success = true;
    });
    $router->route("/post", "POST");
    
    $this->assertTrue($success);
    
  }
  
  public function testRouterError() {
    $router = new \Munition\Router();
    $success = false;
    
    $router->error("404", function($scope, $params, $format) use (&$success) {
      $success = true;
    });
    $router->route("/asdf");
    
    $this->assertTrue($success);
  }
  
  public function testRouterPut() {
    $router = new \Munition\Router();
    $success = false;
    $router->put("/put", function($scope, $params, $format) use (&$success) {
      $success = true;
    });
    $router->route("/put", "PUT");
    
    $this->assertTrue($success);
  }
  
  public function testRouterDelete() {
    $router = new \Munition\Router();
    $success = false;
    $router->delete("/delete", function($scope, $params, $format) use (&$success) {
      $success = true;
    });
    $router->route("/delete", "DELETE");
    
    $this->assertTrue($success);
  }
  
  public function testRouterHead() {
    $router = new \Munition\Router();
    $success = false;
    $router->head("/head", function($scope, $params, $format) use (&$success) {
      $success = true;
    });
    $router->route("/head", "HEAD");
    
    $this->assertTrue($success);
  }
  
  public function testRouterPattern() {
    $router = new \Munition\Router();
    $router->pattern(":username", "[a-zA-Z0-9]*");
    $success = false;
    $router->get("/user/:username/", function($scope, $params, $format) use (&$success) {
      $this->assertArrayHasKey("username", $params);
      $this->assertEquals("ArmedGuy", $params["username"]);
      $success = true;
    });
    $router->route("/user/ArmedGuy", "GET");
    
    $this->assertTrue($success);
  }
  
  public function testRouterInlineParam() {
    $router = new \Munition\Router();
    $success = false;
    $router->get("/user/:username/", function($scope, $params, $format) use (&$success) {
      $this->assertArrayHasKey("username", $params);
      $this->assertEquals("ArmedGuy", $params["username"]);
      $success = true;
    }, ["username" => "[a-zA-Z0-9]*"]);
    $router->route("/user/ArmedGuy", "GET");
    
    $this->assertTrue($success);
  }
  
  public function testRouterFormats() {
    $router = new \Munition\Router();
    $success = false;
    
    $router->get("/get_resource", function($scope, $params, $format) use (&$success) {
      $this->assertEquals("json", $format);
      $success = true;
    });
    $router->route("/get_resource.json", "GET");
    $this->assertTrue($success);
  }
  
  
  /**
   * @expectedException MunitionException
   */
  public function testRouterInvalidRegex() {
    $router = new \Munition\Router();
    $success = false;
    
    $router->get("/user/:username", function() {
    }, ["username" => "[a-zA[-Z0-9*///"]);
  }
  
  
  /**
   * @expectedException Exception
   */
  public function testRouterNoMatch() {
    $router = new \Munition\Router();
    $router->route("/asdf", "GET");
  }
}