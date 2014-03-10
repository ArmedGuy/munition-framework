<?php
namespace framework\base;
class Router {
  private $routes = null;
  private $patterns = null;
  
  private $controllers = null;
  
  function __construct() {
    $this->routes = [];
    $this->patterns = [];
    
    $this->controllers = [];
  }
  
  public function controllers($controllerFolder) {
    $items = scandir($controllerFolder);
    foreach($items as $item) {
      if(strpos($item, "_controller.php") !== false) {
        $name = str_replace("_controller.php", "", $item);
        $class = ucfirst($name);
        $this->controllers[$name] = [
          "file" => $controllerFolder . "/". $item,
          "class" => $class . "Controller"
        ];
      }
    }
  }
  
  public function pattern($name, $pattern) {
    $name = str_replace(":", "", $name);
    $this->patterns[":".$name] = "(?P<".$name.">".$pattern.")";
  }
  
  public function request($path, $type, $controller) {
    $r = [
      "path" => $path,
      "method" => $type,
      "controller" => $controller,
      "params" => 0
    ];
    $regex = preg_quote($path, "/");
    foreach($this->patterns as $name => $pat) {
      if(strpos($path, $name) !== false) {
        $regex = str_replace("\\".$name, $pat, $regex);
        $r["params"]++;
      }
    }
    $regex = "/^" . $regex . "$/";
    $r["regex"] = $regex;
    try {
      preg_match($r["regex"], ""); // This *should* cache the regex
    } catch(Exception $e) {
      return;
    }
    
    $this->routes[] = $r;
  }
  
  public function get($path, $controller) {
    $this->request($path, "GET", $controller);
  }
  
  public function head($path, $controller) {
    $this->request($path, "HEAD", $controller);
  }
  
  public function post($path, $controller) {
    $this->request($path, "POST", $controller);
  }
  
  public function put($path, $controller) {
    $this->request($path, "PUT", $controller);
  }
  
  public function delete($path, $controller) {
    $this->request($path, "DELETE", $controller);
  }
  
  public function route($path, $method) {
    $p = [];
    $params = [];
    foreach($this->routes as $route) {
      if($route["method"] == $method && preg_match($route["regex"], $path, $params) === 1) {
        if($route["params"] != 0) {
          unset($params[0]);
          $this->call_controller_function($route["controller"], $params);
        } else {
          $this->call_controller_function($route["controller"], null);
        }
      }
    }
  }
  
  private function call_controller_function($ctrlfn, $params) {
    if(strpos($ctrlfn, "#") === false || substr_count($ctrlfn, "#") !== 1) {
      throw new Exception("Invalid controller path");
    }
    list($c, $function) = explode("#", $ctrlfn);
    if(isset($this->controllers[$c])) {
      require_once $this->controllers[$c]['file'];
      call_user_func($this->controllers[$c]['class'] . "::" . $function, $params);
    } else {
      throw new Exception("Unknown controller path");
    }
  }
}