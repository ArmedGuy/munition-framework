<?php
namespace config;
class AppRouter extends \framework\base\Router {
  function __construct() {
    parent::__construct();
    $this->base = "/munition-framework";
    
    $this->get("/", "install#home");
  
  }
}