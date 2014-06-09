<?php
include './tests/test_app/models/Assignment.php';
class CreateAssignments extends \Munition\DbModel\Migration {
  public function up() {
    $this->createTable("assignments", function($t) {
      $t->integer("id");
      $t->primary("id");

      $t->string("name");
      $t->text("description");
    });
    $this->addColumn("assignments", "level", "int");
    Assignment::create(["name" => "Test Assignment", "description" => "herp", "level" => 2]);

    $this->removeColumn("assignments", "level");
  }
  public function down() {
    $this->deleteTable("assignments");
  }
}