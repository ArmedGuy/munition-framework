<?php
require_once 'framework/munition.php';
include_once 'app/models/User.php';

$app = new \framework\base\App("./app/", "./config/routes.php");
$app->run();