<?php
require_once 'framework/munition.php';
$app = require 'config/application.php';
if(in_array(MUNITION_ENV, ["production", "development"])) {
  $app->run();
}