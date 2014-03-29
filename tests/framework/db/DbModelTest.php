<?php
require_once './tests/test_app/models/user.php';

class DbModelTest extends PHPUnit_Framework_TestCase {

  public function testFirst() {
    $u = User::first();
    $this->assertEquals("ArmedGuy", $u->name);
  }
}