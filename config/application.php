<?php
class Application extends \framework\base\App {
  function __construct() {
    parent::__construct();
    
    $this->db = new \framework\db\AppDbManager();
    $config = $this;
    
    // TODO: do your configuration, load all neccesary libraries etc
    
    require_once 'env/' . MUNITION_ENV . '.php';
  }
}
return new Application();
