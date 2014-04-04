<?php
namespace config;
class TestRouter extends \Munition\Router {
  function __construct() {
    parent::__construct();
    
    $this->get("/", "test#index");
    
    $this->error("404", "test#not_found");

    $this->get("/test_filters1", "test#test_filters1");
    $this->get("/test_filters2", "test#test_filters2");
  }
}
return new TestRouter();