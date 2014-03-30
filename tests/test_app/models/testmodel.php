<?php
class Testmodel extends \framework\base\Model
{
  public $testValue = false;
  
  public function __construct() {
    $this->testValue = true;
  }
}