<?php
class Application extends \framework\base\App {
  function __construct() {
    parent::__construct();
    
    $config = $this;
    require_once 'env/' . MUNITION_ENV . '.php';
    
  }
}
return new Application();
