<?php
class User extends \DbModel\Base {
  function __construct() {
    $this->has_many ("posts");
  }
}