<?php
require_once './tests/test_app/models/User.php';
require_once './tests/test_app/models/UserProfile.php';
require_once './tests/test_app/models/Post.php';
require_once './tests/test_app/models/GroupPermission.php';
require_once './tests/test_app/models/Group.php';

class DbModelTest extends PHPUnit_Framework_TestCase {

  public function testCreate() {
    User::create(["name" => "Spelfilip", "password" => "legolas"]);
    $u = User::get()->where(["name" => "Spelfilip"])->take;
    $this->assertEquals("Spelfilip", $u->name);
  }
  
  public function testUpdate() {
    $u = User::get()->first->obj();
    $u->password = "hej";
    $u->save();
    
    $u2 = User::where(["name" => $u->name])->take;
    $this->assertEquals("hej", $u2->password);
  }
  
  public function testFirst() {
    $u = User::get()->first;
    $this->assertEquals("ArmedGuy", $u->name);
  }
  
  /**
   * @depends testCreate
   */
  public function testLast() {
    $u = User::get()->last;
    $this->assertEquals("Spelfilip", $u->name);
  }
  
  /**
   * @depends testLast
   */
  public function testDestroy() {
    $u = User::find_by_name("Spelfilip")->obj();
    $u->destroy();

    print_r(User::get()->all);
    $this->assertEquals(3, count(User::get()->all) );
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
    $u = User::whereNot(["name" => "ArmedGuy"])->first;
    $this->assertEquals("EmiiilK", $u->name);
  }
  
  public function testCustomWhereNot() {
    $u = User::whereNot("id > 2")->first;
    $this->assertEquals("ArmedGuy", $u->name);
  }
  
  public function testWhereNotIn() {
    $u = User::whereNot(["name" => ["ArmedGuy", "Hannzas"]])->take;
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
    $users = User::all;
    $users->each(function($v) use(&$i){
      $i++;
    });
    $this->assertEquals(3, $i);
    
    $this->assertEquals("ArmedGuy", $users->first->name);
    $this->assertEquals("Hannzas", $users->last->name);
  }
  
  public function testHasMany() {
    $u = User::where(["name" => "ArmedGuy"])->first->obj();
    
    $this->assertCount(3, $u->posts);
    $this->assertEquals("Awesome Group", $u->groups[0]->name);
    $this->assertEquals("Awesome Group", $u->grps[0]->name);
  }
  
  public function testBelongsTo() {
    $p = Post::get()->take->obj();
    $this->assertEquals("ArmedGuy", $p->user->name);
  }
  
  /**
   * @expectedException \Munition\DbModel\DbException
   */
  public function testInvalidFetchFunction() {
    $u = User::get()->where(["name" => "asdf"])->bake;
  }

  /**
   * @expectedException \Exception
   */
  public function testInvalidQueryResultGetter() {
    $r = new \Munition\DbModel\QueryResult([]);
    $b = $r->peanuts;
  }

  public function testNullRowQueryResult() {
    $r = new \Munition\DbModel\QueryResult([]);
    $this->assertEquals(null, $r->first->obj());
    $this->assertEquals(null, $r->last->obj());
  }
}