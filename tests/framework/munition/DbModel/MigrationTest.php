<?php
use \Munition\DbModel as DbModel;
include './tests/test_app/db/201406091442_create_assignments.php';

class MigrationTest extends PHPUnit_Framework_TestCase {
  public function testMigrationTable() {
    $m = new CreateAssignments(DbModel\Base::$default_db);
    $m->up();

    $this->assertCount(1, Assignment::get()->all);
    $this->assertEquals(null, Assignment::get()->first->level);

    $m->down();
  }
}