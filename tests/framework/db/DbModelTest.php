<?php
require_once './tests/test_app/models/user.php';

class DbModelTest extends PHPUnit_Framework_TestCase {

  public function testCreate() {
    User::create(["name" => "Spelfilip", "password" => "legolas"]);
    $u = User::where(["name" => "Spelfilip"])->take;
    $this->assertEquals("Spelfilip", $u->name);
  }
  
  public function testUpdate() {
    $u = User::first();
    $u->password = "hej";
    $u->save();
    
    $u2 = User::where(["name" => $u->name])->take;
    $this->assertEquals("hej", $u->password);
  }
  
  public function testFirst() {
    $u = User::first();
    $this->assertEquals("ArmedGuy", $u->name);
  }
  
  public function testLast() {
    $u = User::last();
    $this->assertEquals("Hannzas", $u->name);
  }
  
  public function testChain() {
    $u = User::where(
  }
}