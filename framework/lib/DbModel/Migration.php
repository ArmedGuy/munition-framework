<?php
namespace DbModel;
class Migration {
  public static function register_cli_hooks(\Munition\CLI $cli) {
    $cli->registerHook("db:migrate", function() use ($cli){
        $dir = \Munition\App::$application->appFolder . "db/";
        foreach(array_diff(scandir($dir), [".", ".."]) as $file) {
            $fParts = \NamingConventions\from_lower(str_replace(".php", "", $file));
            $time = array_shift($fParts);

            $class = \NamingConventions\to_pascal($fParts);
            echo $file;
            include $dir . $file;
            $mig = new $class(\DbModel\Base::$default_db);

            $cli->writeLine("Migrating $class");
            $mig->up();
            $cli->writeLine("Successfully migrated $class");
        }
        exit();
    });

    $cli->registerHook("db:rollback", function() use ($cli) {
      $dir = \Munition\App::$application->appFolder . "db/";
      foreach(array_diff(scandir($dir, SCANDIR_SORT_DESCENDING), [".", ".."]) as $file) {
        $fParts = \NamingConventions\from_lower(str_replace(".php", "", $file));
        $time = array_shift($fParts);

        $class = \NamingConventions\to_pascal($fParts);
        include $dir . $file;
        $mig = new $class(\DbModel\Base::$default_db);
        $cli->writeLine("Rolling back migration $class");
        $mig->down();
        $cli->writeLine("Sucessfully rolled back migration $class");

      }
      exit();
    });
    $cli->registerHook("db:migration", function() use ($cli) {
        $dir = \Munition\App::$application->appFolder . "db/";
        $args = array_slice($_SERVER["argv"], 2);
        $fName = date("YmdHis") . "_" . $args[0] . ".php";
        $class = \NamingConventions\convert_case($args[0], "lower", "pascal");
        $cli->writeLine("Creating new migration $class as $fName");
        file_put_contents($dir . $fName, "<?php\r\nclass $class extends \\DbModel\\Migration {\r\n    public function up() {\r\n    }\r\n\r\n    public function down() {\r\n    }\r\n}\r\n");
        exit();
    });
  }

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