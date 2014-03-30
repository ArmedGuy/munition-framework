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
  
  public function testWhereNot() {
    $u = User::where_not(["name" => "ArmedGuy"])->first;
    $this->assertEquals("EmiiilK", $u->name);
  }
  
  public function testCustomWhereNot() {
    $u = User::where_not("id > 2")->first;
    $this->assertEquals("ArmedGuy", $u->name);
  }
  
  public function testWhereNotIn() {
    $u = User::where_not(["name" => ["ArmedGuy", "Hannzas"]])->take;
    $this->assertEquals("EmiiilK", $u->name);
  }
  
  public function testSelect() {
    $u = User::select("name")->where(["name" => "ArmedGuy"])->take;
    $this->assertEquals(null, $u->password);
    $this->assertEquals("ArmedGuy", $u->name);
  }
  
  public function testGroupByOrder() {
    $q = User::group("group_id")->select(["count(*)" => "users_in_group"])->order(["users_in_group" => "DESC"]);
    print_r($q);
    $this->assertEquals("2", $q->take->users_in_group);
  }
  
  public function testHaving() {
    $u = User::having("MAX (`num_posts`)")->take;
    $this->assertEquals("Hannzas", $u->name);
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