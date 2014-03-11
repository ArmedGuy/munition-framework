<?php
namespace framework\base;
class App {
  
  public $router = null;
  
  function __construct($appFolder, $router) {
    if(!file_exists($appFolder)) {
      return;
    }
    
    try {
      require $router;
      $this->router = new \config\AppRouter();
      
      \framework\base\AppController::$template_base = $appFolder . "/templates/";
      \framework\base\AppController::$controller_base = $appFolder . "/controllers/";
    }
    catch(Exception $e) {
    }
    
  }
  
  public function run() {
    $method = $_SERVER["REQUEST_METHOD"];
    $uri = $_SERVER['REQUEST_URI'];
    $path = "";
    if(strpos($uri, "?") !== false) {
      list($path, $query) = explode("?", $uri, 2);
      parse_str($query, $_GET);
    } else {
      $path = $uri;
    }
    $this->router->route($path, $method);
  }
}