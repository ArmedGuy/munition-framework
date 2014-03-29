<?php
class AppControllerTest extends PHPUnit_Framework_TestCase {
  /**
   * @expectedException InvalidArgumentException
   */
  public function testCallFunctionInvalidController() {
    \framework\base\AppController::call_function("invalidcontrollerpath");
  }
  
  /**
   * @expectedException InvalidArgumentException
   */
  public function testCallFunctionNonExistantController() {
    \framework\base\AppController::call_function("controller#action");
  }
}