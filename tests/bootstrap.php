<?php
require 'xhr.php';
require './framework/munition.php';

\DbModel\Base::bind(new PDO("mysql:dbname=munition_test;", "root", ""));