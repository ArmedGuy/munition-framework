<?php
$config->db->using("test", [
  "engine" => "mysql",
  "user" => "root",
  "password" => "",
  "db" => "munition_test"
]);
$config->in_test_environment = true;