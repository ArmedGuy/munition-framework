<?php
require_once 'framework/munition.php';

$e = 1/0;
if(file_exists("install.php") && MUNITION_ENV != "production") {
  require 'install.php';
  exit;
}
$app = require 'config/application.php';
$app->run();