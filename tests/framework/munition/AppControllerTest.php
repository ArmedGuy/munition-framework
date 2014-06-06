<?php
class AppControllerTest extends PHPUnit_Framework_TestCase {
  /**
   * @expectedException InvalidArgumentException
   */
  public function testCallFunctionInvalidController() {
    \Munition\AppController::callControllerAction("invalidcontrollerpath");
  }
  
  /**
   * @expectedException InvalidArgumentException
   */
  public function testCallFunctionNonExistantController() {
    \Munition\AppController::callControllerAction("controller#action");
  }
}