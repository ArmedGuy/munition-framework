<?php
require 'xhr.php';
require './framework/autoload.php';

\Munition\DbModel\Base::$default_db = new PDO("mysql:dbname=munition_test;", "root", "");