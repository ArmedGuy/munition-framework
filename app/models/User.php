<?php
class User extends \framework\base\DbModel
{
  function __construct() {
    $this->describe(
      [ has_one, "account" ],
      [ has_many, "posts" ]
    );
  }
}