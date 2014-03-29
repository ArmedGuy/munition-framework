<?php
$config->db->using("test", [
  "engine" => "mysql",
  "user" => "root",
  "password" => "",
  "db" => "munition_test"
]);
\framework\db\DbModel::bind($config->db->test);

$config->in_test_environment = true;