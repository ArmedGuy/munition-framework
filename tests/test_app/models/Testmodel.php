<?php
class Testmodel extends \Munition\Model
{
  public $testValue = false;
  
  public function __construct() {
    $this->testValue = true;
  }
}