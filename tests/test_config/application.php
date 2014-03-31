<?php
if(!class_exists('TestApplication')):
class TestApplication extends \Munition\App {
  function __construct() {
    parent::__construct("./tests/test_app/", "./tests/test_config/routes.php");
    
    $this->db = new \DbModel\AppDbManager();
    $config = $this;
    
    // TODO: do your configuration, load all neccesary libraries etc
    
    require_once 'env/' . MUNITION_ENV . '.php';
  }
}
endif;
return new TestApplication();
