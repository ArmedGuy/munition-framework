<?php
namespace DbModel;
class Migration {
  private $db;
  public function __construct($db) {
    $this->db = $db;
  }
  public function up() {
  }
  public function down() {
  }
  
  protected function createTable($name, callable $structure = null) {
    $t = new MigrationTable($name);
    if($structure != null && is_callable($structure)) {
      $structure($t);
    }
    $this->execute($t->getSQL());
  }
  protected function deleteTable($name) {
    $this->execute("DROP TABLE `{$name}`");
  }
  protected function addColumn($table, $name, $type, array $options = []) {
    switch(strtolower($type)) {
      case "varchar": case "string":
        if(!isset($options["limit"])): $options["limit"] = 255; endif;
        $type = "varchar({$options['limit']})";
        break;
      case "int":case "integer":
        if(!isset($options["limit"])): $options["limit"] = 11; endif;
        $type = "int({$options['limit']})";
        break;
    }
    $sql = "ALTER TABLE `{$table}` ADD COLUMN `{$name}` {$type}";
    if(in_array("first", $options)) {
      $options["location"] = "FIRST";
    }
    if(isset($options["after"])) {
      $options["location"] = "AFTER {$options['after']}";
    }
    if(isset($options["location"])) {
      $sql .= " ".$options["location"];
    }
    $this->execute($sql);
  }
  protected function removeColumn($table, $column) {
    $this->execute("ALTER TABLE `{$table}` DROP COLUMN `{$column}`");
  }

  protected function execute($sql) {
    $this->db->query($sql);
  }
  
}