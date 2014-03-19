<?php
if(file_exists("install.php")) {
  require 'install.php';
  exit;
}

require_once 'framework/munition.php';
$app = require 'config/application.php';
$app->run();