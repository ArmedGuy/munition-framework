<?php
namespace Munition;
class App {
  public $config = null;
  
  public $router = null;
  public $db = null;
  
  public $postprocess = null;
  
  function __construct($appFolder = "./app/", $router = "./config/routes.php") {
    
    spl_autoload_register(function($class) use ($appFolder) {
        $class = classname_to_filename(str_replace('\\', '/', $class));
        if(file_exists($appFolder . "/models/" . $class . '.php')) {
          require_once($appFolder . "/models/" . $class . '.php');
          return;
        }
        if(file_exists($appFolder . "/lib/" . $class . '.php')) {
          require_once($appFolder . "/lib/" . $class . '.php');
          return;
        }
    });
    
    $this->config = [];
    if(!file_exists($appFolder)) {
      throw new \InvalidArgumentException("Unable to access App directory");
    }
    
    if(!file_exists($router)) {
      throw new \InvalidArgumentException("Router file not found");
    }
    
    $this->router = require_once $router;
    $this->router->app = $this;
    
    \Munition\AppController::$template_base = $appFolder . "/templates/";
    \Munition\AppController::$controller_base = $appFolder . "/controllers/";
    
    $this->postprocess = new \Munition\PostProcessingEngine();
    
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
    
    ob_start();
    
    $this->router->route($path, $method);
    
    if(MUNITION_ENV != "test") {
      if(ob_get_level() !== 0) {
        ob_end_flush();
      }
      flush();
      
      if(function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
      }
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