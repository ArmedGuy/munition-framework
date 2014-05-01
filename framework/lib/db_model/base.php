<?php
namespace DbModel;

class Base {
  
  public $id = null;
  
  private static $__initialized = false;
  private static $__dbtable;
  private static $__className;
  private static $__keys;
  public static function __foreign($key, $class) {
    self::init();
    self::$__keys["foreign"][] = [
      "key" => $key,
      "class" => $class
    ];
  }
  public static function __primary($key) {
    self::$__keys["primary"] = $key;
  }
  
  private $_dependants = [
    "has_one" => [],
    "has_many" => [],
    "has_and_belongs_to_many" => []
  ];
  
  protected $_values;
  
  public static function bind($db) {
    QueryBuilder::$db = $db;
  }
  
  private static function init() {
    if(self::$__initialized) return;
    
    $c = strtolower(get_called_class());
    if(strpos($c, "\\") !== false) {
      $a = array_reverse(explode("\\", $c));
      $c = $a[0];
    }
    self::$__dbtable = $c . "s";
    self::$__className = ucfirst($c);
    self::$__keys = [];
  }
  
  private static function getQuery() {
    self::init();
    return new QueryBuilder(self::$__dbtable, self::$__className);
  }
  
  public static function q() {
    return self::getQuery();
  }
  
  public static function make($data) {
    $c = get_called_class();
    $m = new $c();
    
    Base::crowd($m, $data);
    $m->relations();
    
    return $m;
  }
  
  private static function crowd($m, $data) {
    foreach($data as $k => $v) {
      $m->$k = $v; // first, set value
      $m->_values[$k] = $v;
      // TODO: add has$k() etc
    }
  }
  
  public function relations() {
  }
  
  public static function create($params) {
    $id = self::getQuery()->create($params);
    return self::getQuery()->where(["id" => $id])->take;
  }
  
  public function save() {
    $diff = [];
    foreach($this->_values as $k=>$v) {
      if($this->$k != $v) {
        $diff[$k] = $this->$k;
      }
    }
    self::getQuery()->where(["id" => $this->id])->update($diff);
  }
  
  public function destroy() {
    // TODO: destroy childs
    self::getQuery()->where(["id" => $this->id])->destroy();
  }
  
  
  public function has_many($name, $options = []) {
    self::init();
    if($this->id == null)
      throw new DbException("DbModel cannot make relations before its data has been crowded. Make sure to only build relations in model::relations()");
      
    $className = "";
    if(!isset($options["class"])) {
      $className = filename_to_classname(singularize($name));
    } else {
      $className = $options["class"];
    }
    if(isset($options["dependent"]) && $options["dependent"] == true) {
      $this->_dependants[] = $name;
    }
    $c = strtolower(get_called_class());
    $this->$name = $className::where([ $c . "_id" => $this->id ])->all;
  }
  
  public function has_one($name, $options) {
    self::init();
    if($this->id == null)
      throw new DbException("DbModel cannot make relations before its data has been crowded. Make sure to only build relations in model::relations()");
      
    $className = "";
    if(!isset($options["class"])) {
      $className = filename_to_classname(singularize($name));
    } else {
      $className = $options["class"];
    }
    if(isset($options["dependent"]) && $options["dependent"] == true) {
      $this->_dependants[] = $name;
    }
    $c = strtolower(get_called_class());
    $this->$name = $className::q()->where([ $c . "_id" => $this->id ])->first;
  }
  
  /*
  public function has_and_belongs_to_many($name, $options) {
    self::init();
    if($this->id == null)
      throw new DbException("DbModel cannot make relations before its data has been crowded. Make sure to only build relations in model::relations()");
      
    $className = "";
    if(!isset($options["class"])) {
      $className = filename_to_classname(singularize($name));
    } else {
      $className = $options["class"];
    }
    if(isset($options["dependent"]) && $options["dependent"] == true) {
      $this->_dependants[] = $name;
    }
    $c = strtolower(get_called_class());
    $this->$name = $className::where([ $c . "_id" => $this->id ])->first;
  }
  */
  
  public function belongs_to($name, $opt) {
    self::init();
    if($this->id == null)
      throw new DbException("DbModel cannot make relations before its data has been crowded. Make sure to only build relations in model::relations()");
      
    $className = "";
    if(!isset($options["class"])) {
      $className = filename_to_classname(singularize($name));
    } else {
      $className = $options["class"];
    }
    $accessor = $name."_id";
    $this->$name = $className::q()->where([ "id" => $this->$accessor])->first;
  }
  
}