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
    $this->assertEquals("hej", $u2->password);
  }
  
  public function testFirst() {
    $u = User::first();
    $this->assertEquals("ArmedGuy", $u->name);
  }
  
  /**
   * @depends testCreate
   */
  public function testLast() {
    $u = User::last();
    $this->assertEquals("Spelfilip", $u->name);
  }
  
  
  public function testLimitOffset() {
    $users = User::limit(2)->offset(1)->rows;
    $this->assertEquals(2, count($users));
  }
  
  
  /**
   * @expectedException \framework\db\DbException
   */
  public function testInvalidFetchFunction() {
    $u = User::where(["name" => "asdf"])->bake;
  }
}