<?php
namespace framework\base;
class MunitionDbException extends \Exception
{
  function __construct($str) {
    parent::__construct($str);
  }
}