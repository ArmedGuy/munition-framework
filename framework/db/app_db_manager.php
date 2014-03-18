<?php
namespace framework\db;

class AppDbManager {
  public function using($name, $args) {
    $c = isset($args["engine"]) ? $args["engine"] : "mysql";
    
    if(isset($args["unix_socket"])) {
      $c .= ":unix_socket=" . $args["unix_socket"] . ";";
    } else {
      $c .= ":host=" . (isset($args["host"]) ? $args["host"] : "localhost") . ";";
      $c .= "port=" . (isset($args["port"]) ? $args["port"] : "3306") . ";";
    }
    $c .= "dbname=" . (isset($args["db"]) ? $args["db"] : "app") . ";";
    
    $this->$name = new \PDO($c, 
      (isset($args["user"]) ? $args["user"] : "app"),
      (isset($args["password"]) ? $args["password"] : "")
    );
  }
}