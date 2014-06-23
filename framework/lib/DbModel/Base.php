<?php
namespace DbModel;

class Base {

  protected static $primary_key = "id";
  protected static $foreign_key = null;
  protected static $table_name = null;

  public static $default_db = null;
  
  private $_bindings = [];
  
  protected $_values;

  protected static function _getDb() {
    return static::$default_db;
  }

  protected static function _getQuery() {
    return new QueryBuilder(
      static::_getDb(),
      static::s(),
      get_called_class(),
      static::$primary_key
    );
  }

  public static function get() {
    return static::_getQuery();
  }

  public static function table() {
    if(static::$table_name == null) {
      $t = get_called_class();
      if(strpos($t, "\\") !== false) {
        $a = array_reverse(explode("\\", $t));
        $t = $a[0];
      }
      return \NamingConventions\convert_case($t, "pascal", "lower") . "s";
    } else {
      return static::$table_name;
    }
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
      return \NamingConventions\convert_case(get_called_class(), "pascal", "lower") . "_id";
    } else {
      return static::$foreign_key;
    }
  }
  
  public static function make(array $data) {
    $c = get_called_class();
    $m = new $c();
    
    Base::crowd($m, $data);
    $m->relations();
    
    return $m;
  }
  
  private static function crowd(Base $m, array $data) {
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

  public static function __callStatic($method, array $arguments) {
    if(count($arguments) != 0) {
      if($method == "find") {
        return static::get()->where([static::primary() => $arguments[0]])->first;
      }
      if(strpos($method, "find_by_") === 0) {
        $method = str_replace("find_by_", "", $method);
        $search = \NamingConventions\from_lower($method);
        $finds = [];
        for($i=0; $i < count($search); $i+=2) {
          $finds[] = "`{$search[$i]}` = ?";
        }
        $args = array_merge([implode(" OR ", $finds)], array_fill(0, count($finds), $arguments[0]));
        return call_user_func_array([static::get(), "where"], $args)->first;
      }
    }
    if(in_array($method, ["all", "first","last","take","where","whereNot","select",
      "joins","order","limit","offset","group","having"])) {
      return call_user_func_array([static::get(), $method], $arguments);
    }

    // TODO: raise exception when no match was found
  }
  
  public function relations() {
  }
  
  // TODO: don't do like this
  public static function create(array $params) {
    $id = static::_getQuery()->create($params);
    return static::_getQuery()->where([ static::primary() => $id])->take;
  }
  
  public function save() {
    $diff = [];
    foreach($this->_values as $k=>$v) {
      if($this->$k != $v) {
        $diff[$k] = $this->$k;
      }
    }
    $primary = static::primary();
    static::get()->where([ static::primary() => $this->$primary ])->update($diff);
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
    static::get()->where([ static::primary() => $this->$primary ])->destroy();
  }
  
  
  public function hasMany($name, array $options = []) {
    $hasPrimary = "has".ucfirst(static::primary());
    if(!$this->$hasPrimary())
      throw new DbException("DbModel cannot make relations before its data has been crowded. Make sure to only build relations in model::relations()");
      
    $className = "";
    if(!isset($options["class"])) {
      $className = \NamingConventions\convert_case(singularize($name), "lower", "pascal");
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

  // Rails ActiveRecord compat
  public function has_many($name, array $options = []) {
    $this->hasMany($name, $options);
  }
  
  public function hasAndBelongsToMany($name, array $options = []) {
    $hasPrimary = "has".ucfirst(static::primary());
    if(!$this->$hasPrimary())
      throw new DbException("DbModel cannot make relations before its data has been crowded. Make sure to only build relations in model::relations()");
      
    $className = "";
    if(!isset($options["class"])) {
      $className = \NamingConventions\convert_case(singularize($name), "lower", "pascal");
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

  // Rails ActiveRecord compat
  public function has_and_belongs_to_many($name, array $options = []) {
    $this->hasAndBelongsToMany($name, $options);
  }
  
  public function hasOne($name, array $options = []) {
    $hasPrimary = "has".ucfirst(static::primary());
    if(!$this->$hasPrimary())
      throw new DbException("DbModel cannot make relations before its data has been crowded. Make sure to only build relations in model::relations()");

    $className = "";
    if(!isset($options["class"])) {
      $className = \NamingConventions\convert_case(singularize($name), "lower", "pascal");
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

  // Rails ActiveRecord compat
  public function has_one($name, array $options = []) {
    $this->hasOne($name, $options);
  }

  public function belongsTo($name, array $options = []) {
    $hasPrimary = "has" . ucfirst(static::primary());
    if(!$this->$hasPrimary())
      throw new DbException("DbModel cannot make relations before its data has been crowded. Make sure to only build relations in model::relations()");
      
    $className = "";
    if(!isset($options["class"])) {
      $className = \NamingConventions\convert_case(singularize($name), "lower", "pascal");
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

  // Rails ActiveRecord compat
  public function belongs_to($name, array $options = []) {
    $this->belongsTo($name, $options);
  }
  
}