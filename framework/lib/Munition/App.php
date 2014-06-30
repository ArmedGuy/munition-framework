<?php
namespace Munition;
class App extends \stdClass {
  public static $application = null;

  public $config = null;
  
  public $router = null;
  public $db = null;
  
  public $postprocess = null;
  public $type = "REQUEST_URI";

  function __construct($manual = false) {
      if($manual != true) {
          $this->setup();
          if(php_sapi_name() == 'cli' && MUNITION_ENV != "test") {
              $this->cli->run();
          }
      }
  }
  public function setup() {
    $this->configure();
  }
  public function configure($appFolder = "./app/", Router $router = null) {
    $this->config = [];
    $this->appFolder = $appFolder;
    spl_autoload_register(function($class) use ($appFolder) {
        $class = str_replace('\\', '/', $class);
        if(file_exists($appFolder . "/models/" . $class . '.php')) {
          require_once($appFolder . "/models/" . $class . '.php');
          return;
        }
        if(file_exists($appFolder . "/lib/" . $class . '.php')) {
          require_once($appFolder . "/lib/" . $class . '.php');
          return;
        }
    });

    if(!file_exists($appFolder)) {
      throw new \InvalidArgumentException("Unable to access App directory");
    }
    
    if($router != null) {
      $this->router = require $router;
    } else {
      $this->router = new Router();
    }
    $this->router->app = $this;
    
    \Munition\AppController::$template_base = $appFolder . "/templates/";
    \Munition\AppController::$controller_base = $appFolder . "/controllers/";
    
    $this->postprocess = new \Munition\PostProcessingEngine();

    static::$application = $this;

    $this->cli = new \Munition\CLI();
    
  }
  
  public function run() {
    ignore_user_abort(true);
    
    $method = $_SERVER["REQUEST_METHOD"];
    $uri = $_SERVER[$this->type];
    $path = "";
    if(strpos($uri, "?") !== false) {
      list($path, $query) = explode("?", $uri, 2);
      parse_str($query, $_GET);
    } else {
      $path = $uri;
    }

    $input = file_get_contents("php://input");
    if($input != "") {
        if($input[0] == "{") {
            $this->router->initial_params = json_decode($input, true);
        } else {
            try {
                parse_str($this->router->initial_params, $input);
            } catch(\Exception $e) {

            }
        }
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