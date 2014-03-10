<?php
require_once 'framework/munition.php';
include_once 'app/models/User.php';

//$app = new \framework\lib\App("./app/", "./config/routes.php");
//$app->run();

$db = new \PDO("mysql:host=localhost;dbname=munition", "root", "");

\framework\base\DbModelQuery::$db = $db;

$q = User::where(["name" => "Hammas"])->first->instance();
User::create(["name" => "Hammas", "created_at" => time());

$u = User::take();
print_r($q);