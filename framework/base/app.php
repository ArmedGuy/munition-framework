<?php
namespace framework\base;
class App {
  
  public $config = null;
  
  public $router = null;
  public $postprocess = null;
  
  function __construct($appFolder = "./app/", $router = "./config/routes.php") {
    $this->config = [];
    if(!file_exists($appFolder)) {
      return;
    }
    
    try {
      require $router;
      $this->router = new \config\AppRouter();
      $this->router->app = $this;
      
      \framework\base\AppController::$template_base = $appFolder . "/templates/";
      \framework\base\AppController::$controller_base = $appFolder . "/controllers/";
      
      $this->postprocess = new \framework\base\PostProcessingEngine();
    }
    catch(Exception $e) {
    }
    
  }
  
  public function run() {
    ignore_user_abort(true);
    
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
    
    if(ob_get_level() !== 0) {
      ob_end_flush();
    }
    flush();
    
    if(function_exists('fastcgi_finish_request')) {
      fastcgi_finish_request();
    }
    
    $this->postprocess->process();
    
  }
  
  public function __set( $name, $value) {
    $this->config[$name] = $value;
  }
  public function __get( $name ) {
    return $this->config[$name];
  }
}