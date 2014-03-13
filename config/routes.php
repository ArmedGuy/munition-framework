<?php
namespace config;
class AppRouter extends \framework\base\Router {
  function __construct() {
    parent::__construct();
    
    $this->get("/", "install#home");
    $this->error("404", "install#home");
    $this->get("/verify_rewrite", "install#verify_rewrite");
  
  }
}