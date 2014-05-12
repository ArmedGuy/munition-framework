<?php
require_once 'framework/munition.php';
if(file_exists("install.php") && MUNITION_ENV != "production") {
  require 'install.php';
  exit;
}
$app = require 'app/application.php';
$app->run();