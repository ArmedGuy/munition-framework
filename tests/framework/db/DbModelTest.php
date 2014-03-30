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
  
  /**
   * @depends testLast
   */
  public function testDestroy() {
    $u = User::where(["name" => "Spelfilip"])->take->instance();
    $u->destroy();
    
    $this->assertEquals(3, count(User::all()));
  }
  
  public function testCustomWhere() {
    $u = User::where("name = ?", "ArmedGuy")->take;
    $this->assertEquals("ArmedGuy", $u->name);
  }
  
  public function testWhereIn() {
    $u = User::where(["name" => ["ArmedGuy", "Hannzas"]])->all;
    $this->assertEquals(2, count($u));
  }
  
  public function testLimitOffset() {
    $users = User::limit(2)->offset(1)->all;
    $this->assertEquals(2, count($users));
  }
  
  /**
   * @expectedException \framework\db\DbException
   */
  public function testInvalidFetchFunction() {
    $u = User::where(["name" => "asdf"])->bake;
  }
}