<?php
namespace DbModel;

class Base {

  protected static $primary_key = "id";
  protected static $foreign_key = null;
  protected static $table_name = null;
  
  private $_bindings = [];
  
  protected $_values;
  
  public static function bind($db) {
    QueryBuilder::$db = $db;
  }
  
  protected static function getQuery() {
    return new QueryBuilder(
      static::s(),
      get_called_class(),
      static::$primary_key
    );
  }
  
  public static function get() {
    return static::getQuery();
  }
  
  public static function table() {
    $t = strtolower(get_called_class());
    if(strpos($t, "\\") !== false) {
      $a = array_reverse(explode("\\", $t));
      $t = $a[0];
    }
    return static::$table_name == null ? $t . "s" : static::$table_name;
  }
  
  // Just prettifiers
  public static function s() {
    return static::table();
  }
  
  public static function ies() {
    return static::table();
  }
  
  // keys
  public static function primary() {
    return static::$primary_key;
  }
  
  public static function foreign() {
    if(static::$foreign_key == null) {
      return classname_to_filename(get_called_class()) . "_id";
    } else {
      return static::$foreign_key;
    }
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
    }
  }
  
  public function __call($method, $arguments) {
    if(0 === strpos($method, "has")) {
      $key = strtolower(substr($method, 3));
      return isset($this->$key) && $this->$key != null && $this->$key !== "";
    }
    
    // TODO: raise exception when no match was found
  }
  
  public function relations() {
  }
  
  // TODO: don't do like this
  public static function create($params) {
    $id = static::getQuery()->create($params);
    return static::getQuery()->where([ static::primary() => $id])->take;
  }
  
  public function save() {
    $diff = [];
    foreach($this->_values as $k=>$v) {
      if($this->$k != $v) {
        $diff[$k] = $this->$k;
      }
    }
    $primary = static::primary();
    static::getQuery()->where([ static::primary() => $this->$primary ])->update($diff);
  }
  
  public function destroy() {
    foreach($this->_bindings as $name=>$bind) {
      if($bind["dependant"] == true) {
        switch($bind["type"]) {
          case "has_one":
            $this->$name->destroy();
            break;
          case "has_many":
            foreach($this->$name as $item) {
              $item->destroy();
            }
            break;
          case "has_many_through":
            break;
        }
      }
    }
    $primary = static::primary();
    static::getQuery()->where([ static::primary() => $this->$primary ])->destroy();
  }
  
  
  public function has_many($name, $options = []) {
    $hasPrimary = "has".ucfirst(static::primary());
    if(!$this->$hasPrimary())
      throw new DbException("DbModel cannot make relations before its data has been crowded. Make sure to only build relations in model::relations()");
      
    $className = "";
    if(!isset($options["class"])) {
      $className = filename_to_classname(singularize($name));
    } else {
      $className = $options["class"];
    }
    if(!isset($options["through"])) {
      $this->$name = $className::get()->where([ static::foreign() => $this->{static::primary()} ])->all;
    } else {
      $throughClassName = $this->_bindings[$options["through"]]["class"];
      $this->$name = $className::get()->joins($throughClassName::table())->select($className::table() . ".*")->where([ $throughClassName::table() . "." . static::foreign() => $this->{static::primary()} ])->all;
    }
    $this->_bindings[$name] = [
      "type" => isset($options["through"]) ? "has_many_through" : "has_many",
      "class" => $className,
      "dependant" => (isset($options["dependant"]) && $options["dependant"] == true)
    ];
  }
  
  public function has_and_belongs_to_many($name, $options = []) {
    $hasPrimary = "has".ucfirst(static::primary());
    if(!$this->$hasPrimary())
      throw new DbException("DbModel cannot make relations before its data has been crowded. Make sure to only build relations in model::relations()");
      
    $className = "";
    if(!isset($options["class"])) {
      $className = filename_to_classname(singularize($name));
    } else {
      $className = $options["class"];
    }
    
    $table = "";
    if(!isset($options["table"])) {
      $t = sort([ static::table(), $name ]);
      $table = singularize($t[0]) . "_" . pluralize($t[1]);
    } else {
      $table = $options["table"];
    }
    
    $this->$name = $className::get()->joins($table)->select($className::table() . ".*")->where([ $table . "." . static::foreign() => $this->{static::primary()} ])->all;
    
    $this->_bindings[$name] = [
      "type" => "has_and_belongs_to_many",
      "class" => $className,
      "meta" => $table,
      "dependant" => (isset($options["dependant"]) && $options["dependant"] == true)
    ];
  }
  
  public function has_one($name, $options = []) {
    $hasPrimary = "has".ucfirst(static::primary());
    if(!$this->$hasPrimary())
      throw new DbException("DbModel cannot make relations before its data has been crowded. Make sure to only build relations in model::relations()");

    $className = "";
    if(!isset($options["class"])) {
      $className = filename_to_classname(singularize($name));
    } else {
      $className = $options["class"];
    }
    
    $this->$name = $className::get()->where([ static::foreign() => $this->{static::primary()} ])->first;
    
    $this->_bindings[$name] = [
      "type" => "has_one",
      "class" => $className,
      "dependant" => (isset($options["dependant"]) && $options["dependant"] == true)
    ];
  }
  
  public function belongs_to($name, $options = []) {
    $hasPrimary = "has" . ucfirst(static::primary());
    if(!$this->$hasPrimary())
      throw new DbException("DbModel cannot make relations before its data has been crowded. Make sure to only build relations in model::relations()");
      
    $className = "";
    if(!isset($options["class"])) {
      $className = filename_to_classname(singularize($name));
    } else {
      $className = $options["class"];
    }
    $foreign = $className::foreign();
    $this->$name = $className::get()->where([ static::primary() => $this->$foreign ])->first;
    
    $this->_bindings[$name] = [
      "type" => "belongs_to",
      "class" => $className,
      "dependant" => false
    ];
  }
  
}