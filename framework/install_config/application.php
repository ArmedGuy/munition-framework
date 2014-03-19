<?php
if(!class_exists('InstallApplication')):
class InstallApplication extends \framework\base\App {
  function __construct() {
    parent::__construct("./framework/install_app/", "./framework/install_config/routes.php");
    
    $this->db = new \framework\db\AppDbManager();
    $config = $this;
    
    // TODO: do your configuration, load all neccesary libraries etc
    
    require_once 'env/' . MUNITION_ENV . '.php';
  }
}
endif;
return new InstallApplication();
