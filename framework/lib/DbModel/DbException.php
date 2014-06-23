<?php
namespace DbModel;
class DbException extends \Exception
{
  function __construct($str) {
    parent::__construct($str);
  }
}