<?php
require_once './tests/test_app/models/user.php';
require_once './tests/test_app/models/post.php';

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
  
  
  /**
   * @depends testDestroy
   */
  public function testSelectCount() {
    $r = User::select("count(*) as num")->take;
    $this->assertEquals("3", $r->num);
  }
  
  public function testOrder() {
    $u = User::order(["login_count" => "DESC"])->first;
    $this->assertEquals("1337", $u->login_count);
  }
  
  public function testHaving() {
    $u = User::group("type")->having("SUM(`login_count`) < 200")->first;
    $this->assertEquals("EmiiilK", $u->name);
  }
  
  public function testLimitOffset() {
    $users = User::limit(2)->offset(1)->all;
    $this->assertEquals(2, count($users));
  }
  
  /**
   * @depends testDestroy
   */
  public function testQueryResult() {
    $i = 0;
    $users = User::all();
    $users->each(function($v) use(&$i){
      $i++;
    });
    $this->assertEquals(3, $i);
    
    $this->assertEquals("ArmedGuy", $users->first->name);
    $this->assertEquals("Hannzas", $users->last->name);
  }
  
  public function testHaveMany() {
    $u = User::where(["name" => "ArmedGuy"])->first->instance();
    $this->assertCount(3, $u->posts);
  }
  
  /**
   * @expectedException \DbModel\DbException
   */
  public function testInvalidFetchFunction() {
    $u = User::where(["name" => "asdf"])->bake;
  }
}