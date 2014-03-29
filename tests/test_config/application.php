<?php
if(!class_exists('TestApplication')):
class TestApplication extends \framework\base\App {
  function __construct() {
    parent::__construct("./tests/test_app/", "./tests/test_config/routes.php");
    
    $this->db = new \framework\db\AppDbManager();
    $config = $this;
    
    // TODO: do your configuration, load all neccesary libraries etc
    
    require_once 'env/' . MUNITION_ENV . '.php';
  }
}
endif;
return new TestApplication();
