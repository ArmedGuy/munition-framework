<?php
class User extends \DbModel\Base {

  function relations() {
    $this->has_many ("posts");
  }
}