<?php
namespace config;
class AppRouter extends \framework\base\Router {
  function __construct() {
    parent::__construct();
    
    $this->base = "/munition-framework";
    
    $this->get("/", "install#home");
    $this->error("404", "install#home");
    $this->get("/verify_rewrite", "install#verify_rewrite");
    $this->get("/server_info", "install#server_info");
  
  }
}