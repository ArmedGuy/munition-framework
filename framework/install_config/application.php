<?php
if(!class_exists('InstallApplication')):
class InstallApplication extends \Munition\App {
  function __construct() {
    parent::__construct("./framework/install_app/", "./framework/install_config/routes.php");
    
    $this->db = new \DbModel\AppDbManager();
    $config = $this;
    
    require_once 'env/' . MUNITION_ENV . '.php';
  }
}
endif;
return new InstallApplication();
