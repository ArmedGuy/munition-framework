<?php
namespace framework\base;
class Router {

  private $routes = null;
  private $patterns = null;
  
  protected $base = "";
  
  private $controllers = null;
  
  protected $initial_scope = [];
  
  public $app = null;
  
  function __construct() {
    
    $this->routes = [];
    $this->patterns = [];
    
    $this->controllers = [];
  }
  
  public function pattern($name, $pattern) {
    $name = str_replace(":", "", $name);
    $this->patterns[":".$name] = "(?P<".$name.">".$pattern.")";
  }
  
  public function request($path, $type, $controller, $params = []) {
    $r = [
      "path" => $path,
      "method" => $type,
      "controller" => $controller,
      "params" => 0
    ];
    $regex = preg_quote($path, "/");
    foreach($params as $k=>$p) {
      $params[":".$k] = "(?P<".$k.">".$p.")";
    }
    foreach(array_merge($this->patterns, $params) as $name => $pat) {
      if(strpos($path, $name) !== false) {
        $regex = str_replace("\\".$name, $pat, $regex);
        $r["params"]++;
      }
    }
    if(substr($regex, -1) === "/") {
      $regex .= "?";
    } else {
      $regex .= "\\/?";
    }
    $regex = "/^" . $regex . "(?P<_request_format>\.[a-zA-Z0-9]{1,4})?$/";
    $r["regex"] = $regex;
    try {
      preg_match($r["regex"], ""); // This *should* cache the regex
    } catch(Exception $e) {
      return;
    }
    
    $this->routes[$type.":".$path] = $r;
  }
  
  public function get($path, $controller, $params = []) {
    $this->request($path, "GET", $controller, $params);
  }
  
  public function head($path, $controller, $params = []) {
    $this->request($path, "HEAD", $controller, $params);
  }
  
  public function post($path, $controller, $params = []) {
    $this->request($path, "POST", $controller, $params);
  }
  
  public function put($path, $controller, $params = []) {
    $this->request($path, "PUT", $controller, $params);
  }
  
  public function delete($path, $controller, $params = []) {
    $this->request($path, "DELETE", $controller, $params);
  }
  
  public function route($request, $method = "GET") {
    $p = [];
    $params = [];
    $f = false;
    $path = str_replace($this->base, "", $request);
    foreach($this->routes as $n=>$route) {
      if($route["method"] == $method && preg_match($route["regex"], $path, $params) === 1) {
        unset($params[0]);
        $this->call_controller_function($route["controller"], $params);
        $f = true;
        break;
      }
    }
    if($f === false) {
      if(isset($this->routes["404"])) {
        $this->call_controller_function($this->routes["404"]["controller"], ["request"=>$request, "path" => $path]);
      } else { 
        throw new \Exception("No matching route found in Router: (".$request . ":".$path.")\n" . print_r($this->routes, true));
      }
    }
  }
  
  public function error($errCode, $controller) {
    $this->routes[$errCode] = ["method"=>"ANY", "regex"=>"/^$/","controller" => $controller, "params" => 0];
  }
  
  private function call_controller_function($ctrlfn, $params) {
    $format = isset($params["_request_format"]) ? substr($params["_request_format"], 1) : "html";
    $call_params = [$ctrlfn, $this->initial_scope, $params, $format, $this->app];
    \framework\base\AppController::call_function($ctrlfn, $this->initial_scope, $params, $format, $this->app);

  }
}