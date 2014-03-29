<?php
namespace config;
class AppRouter extends \framework\base\Router {
  function __construct() {
    parent::__construct();
    
    $this->get(MUNITION_WEBPATH, "install#home");
    $this->get(MUNITION_WEBPATH . "verify_rewrite", "install#verify_rewrite");
    
    $this->error("404", "install#home");
    
    // used in testing environment
    if(MUNITION_ENV == "test") {
      $this->get("/test_filters1", "install#test_filters1");
      $this->get("/test_filters2", "install#test_filters2");
    }
  }
}