<?php
class Testmodel extends \framework\base\Model
{
  public $testValue = false;
  public __construct() {
    $this->testValue = true;
  }
}