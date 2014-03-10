<?php
namespace config;
class AppRouter extends \framework\base\Router {
  function __construct() {
    parent::__construct();
    $this->base = "/munition-framework";
    
    $this->get("/", "index#home");
    $this->get("/info", "index#server_info");
  
  }
}