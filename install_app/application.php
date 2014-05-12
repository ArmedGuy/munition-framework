<?php
if(!class_exists('InstallApplication')):
class InstallApplication extends \Munition\App {
  function __construct() {
    parent::__construct("./framework/install_app/");
    
    $this->db = new \DbModel\AppDbManager();
    $r = $this->router;
    require_once 'routes.php';

    $config = $this;
    require_once 'env/' . MUNITION_ENV . '.php';
  }
}
endif;
return new InstallApplication();
