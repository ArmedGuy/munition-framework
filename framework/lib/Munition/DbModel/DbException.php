<?php
namespace Munition\DbModel;
class DbException extends \Exception
{
  function __construct($str) {
    parent::__construct($str);
  }
}