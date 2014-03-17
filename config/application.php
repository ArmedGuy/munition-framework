<?php
class Application extends \framework\base\App {
  function __construct() {
    parent::__construct("./app/", "./config/routes.php");
  }
}

return new Application();
