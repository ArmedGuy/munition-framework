<?php
class AppControllerTest extends PHPUnit_Framework_TestCase {
  /**
   * @expectedException InvalidArgumentException
   */
  public function testCallFunctionInvalidController() {
    \Munition\AppController::call_controller_action("invalidcontrollerpath");
  }
  
  /**
   * @expectedException InvalidArgumentException
   */
  public function testCallFunctionNonExistantController() {
    \Munition\AppController::call_controller_action("controller#action");
  }
}