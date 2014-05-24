<?php
require 'xhr.php';
require './framework/munition.php';

\DbModel\Base::$default_db = new PDO("mysql:dbname=munition_test;", "root", "");