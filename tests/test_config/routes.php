<?php
namespace config;
class AppRouter extends \framework\base\Router {
  function __construct() {
    parent::__construct();
    
    $this->get("/", "test#index");
    $this->get("/verify_rewrite", "install#verify_rewrite");
    
    $this->error("404", "tests#not_found");

    $this->get("/test_filters1", "install#test_filters1");
    $this->get("/test_filters2", "install#test_filters2");
  }
}