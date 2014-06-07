<?php
class MigrationTableTest extends PHPUnit_Framework_TestCase {
  public function testMigrationTable() {
    $t = new \Munition\DbModel\MigrationTable("assignments");
    $t->integer("id");
    $t->primary("id");
    $t->string("username", ["default" => "derp"]);
    $t->text("bio_raw");
    $t->datetime("created_at");
    echo $t->getSQL();
  }
}