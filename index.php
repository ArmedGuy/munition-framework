<?php
require_once 'framework/munition.php';

$app = new \framework\base\App("./app/", "./config/routes.php");
$app->run();
