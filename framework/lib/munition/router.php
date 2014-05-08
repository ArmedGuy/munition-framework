<?php
namespace Munition;
class Router {

  private $_routes = null;
  private $_patterns = null;
  
  
  private $_prepend = false;
  private $_scope = "";
  
  protected $base = "";
  protected $initial_context = [];
  
  public $app = null;
  
  function __construct() {
    
    $this->_routes = [];
    $this->_patterns = [];
  }
  
  public function pattern($name, $pattern) {
    $name = str_replace(":", "", $name);
    $this->_patterns[":".$name] = "(?P<".$name.">".$pattern.")";
  }
  
  public function prepend($cb) {
    $this->_prepend = true;
    $cb($this);
    $this->_prepend = false;
  }
  public function append($cb) {
    $this->_prepend = false;
    $cb($this);
  }
  public function map($cb) {
    $this->_routes = [];
    $this->_prepend = false;
    $cb($this);
  }
  public function scope($scope, $cb) {
    $old = $this->_scope;
    $this->_scope .= "/". $scope;
    $cb($this);
    $this->_scope = $old;
  }
  
  public function request($path, $type, $action, $params = []) {
    $path = $this->_scope . $path;
    $r = [
      "path" => $path,
      "method" => $type,
      "action" => $action,
      "params" => 0
    ];
    $regex = preg_quote($path, "/");
    foreach($params as $k=>$p) {
      $params[":".$k] = "(?P<".$k.">".$p.")";
    }
    foreach(array_merge($this->_patterns, $params) as $name => $pat) {
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
    preg_match($r["regex"], "");
    
    if($this->_prepend == true)
      array_unshift($this->_routes, $r);
    else
      $this->_routes[] = $r;
  }
  
  public function get($path, $action, $params = []) {
    $this->request($path, "GET", $action, $params);
  }
  
  public function head($path, $action, $params = []) {
    $this->request($path, "HEAD", $action, $params);
  }
  
  public function post($path, $action, $params = []) {
    $this->request($path, "POST", $action, $params);
  }
  
  public function put($path, $action, $params = []) {
    $this->request($path, "PUT", $action, $params);
  }
  
  public function delete($path, $action, $params = []) {
    $this->request($path, "DELETE", $action, $params);
  }
  
  
  
  public function resources($path, $options = []) {
    $this->scope($path, function($r) use ($path, $options){
      
      $regex = "[0-9]+";
      if(isset($options["id"])) {
        $regex = $options["id"];
      }
      $id = singularize($path) . "_id";
      $params = [ $id => $regex ];
      
      if(!isset($options["except"]))
        $options["except"] = [];
      
      !in_array("index", $options["except"]) &&
        $r->get("/", $path . "#index");
        
      !in_array("new", $options["except"]) &&
        $r->get("/new", $path . "#new");
        
      !in_array("create", $options["except"]) &&
        $r->post("/", $path . "#create");
        
      !in_array("show", $options["except"]) &&
        $r->get("/:{$id}", $path . "#show", $params);
        
      !in_array("edit", $options["except"]) &&
        $r->get("/:{$id}/edit", $path . "#edit", $params);
        
      !in_array("update", $options["except"]) &&
        $r->put("/:{$id}", $path . "#update", $params);
        
      !in_array("delete", $options["except"]) &&
        $r->delete("/:{$id}", $path . "#delete", $params);
    
    });
  }
  
  public function resource($path, $options = []) {
    $this->scope($path, function($r) use ($path, $options){
      
      if(!isset($options["except"]))
        $options["except"] = [];
      
      !in_array("index", $options["except"]) &&
        $r->get("/", $path . "#index");
        
      !in_array("new", $options["except"]) &&
        $r->get("/new", $path . "#new");
        
      !in_array("create", $options["except"]) &&
        $r->post("/", $path . "#create");
        
      !in_array("edit", $options["except"]) &&
        $r->get("/edit", $path . "#edit");
        
      !in_array("update", $options["except"]) &&
        $r->put("/", $path . "#update");
        
      !in_array("delete", $options["except"]) &&
        $r->delete("/", $path . "#delete");
    
    });
  }
  
  /*
  public function model($path, $class, $options = []) {
    $this->scope($path, function($r) use ($path, $class, $options) {
      $regex = "[0-9]+";
      if(isset($options["id"])) {
        $regex = $options["id"];
      }
      $id = singularize($path) . "_id";
      $params = [ $id => $regex ];
      
      $constraint = isset($options["constraint"]) ? $options["constraint"] : ModelConstraint::$default;
      
      $r->get("/:id", function($context, $params) use($constraint) {
        if($constraint->canGet()) {
          $params["id"];
          
        } else {
          AppController::render([404, "json" => ["error" => true]]);
        }
      }, $params);
      
      $r->post("/:id", function($context, $params) use($constraint) {
        if($constraint->canCreate()) {
        } else {
          AppController::render([404, "json" => ["error" => true]]);
        }
      }, $params);
      
      $r->put("/:id", function($context, $params) use($constraint) {
        if($constraint->canUpdate()) {
        } else {
          AppController::render([404, "json" => ["error" => true]]);
        }
      }, $params);
      
      $r->delete("/:id", function($context, $params) use($constraint) {
        if($constraint->canDelete()) {
          $class::
        } else {
          AppController::render([404, "json" => ["error" => true]]);
        }
      }, $params);
    });
  }
  */
  
  public function route($request, $method = "GET") {
    $p = [];
    $params = [];
    $f = false;
    $path = str_replace($this->base, "", $request);
    foreach($this->_routes as $route) {
      if($route["method"] == $method && preg_match($route["regex"], $path, $params) === 1) {
        unset($params[0]);
        $this->call_action($route["action"], $params);
        $f = true;
        break;
      }
    }
    if($f === false) {
      if(isset($this->_routes["404"])) {
        $this->call_action($this->_routes["404"]["action"], ["request"=>$request, "path" => $path]);
      } else { 
        throw new \Exception("No matching route found in Router: (".$request . ":".$path.")\n" . print_r($this->_routes, true));
      }
    }
  }
  
  public function error($errCode, $controller) {
    $this->_routes[$errCode] = ["method"=>"ANY", "regex"=>"/^$/","controller" => $controller, "params" => 0];
  }
  
  private function call_action($ctrlfn, $params) {
    $format = isset($params["_request_format"]) ? substr($params["_request_format"], 1) : "html";
    if(is_callable($ctrlfn)) {
      $ctrlfn($this->initial_context, $params, $format, $this->app);
    } else {
      AppController::call_controller_action($ctrlfn, $this->initial_context, $params, $format, $this->app);
    }

  }
}