<?php
namespace DbModel;

class Base {
  
  public $id = null;

  protected static $primary_key = "id";
  protected static $table_name = null;
  
  private $_dependants = [
    "has_one" => [],
    "has_many" => [],
    "has_and_belongs_to_many" => []
  ];
  
  protected $_values;
  
  public static function bind($db) {
    QueryBuilder::$db = $db;
  }
  
  protected static function getQuery() {
    $t = strtolower(get_called_class());
    if(strpos($t, "\\") !== false) {
      $a = array_reverse(explode("\\", $t));
      $t = $a[0];
    }
    return new QueryBuilder(
      static::$table_name == null ? $t . "s" : static::$table_name,
      get_called_class(),
      static::$primary_key
    );
  }
  
  public static function get() {
    return static::getQuery();
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
    $id = static::getQuery()->create($params);
    return static::getQuery()->where([ static::$primary_key => $id])->take;
  }
  
  public function save() {
    $diff = [];
    foreach($this->_values as $k=>$v) {
      if($this->$k != $v) {
        $diff[$k] = $this->$k;
      }
    }
    static::getQuery()->where([static::$primary_key => $this->id])->update($diff);
  }
  
  public function destroy() {
    // TODO: destroy childs
    static::getQuery()->where([static::$primary_key => $this->id])->destroy();
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
    $this->$name = $className::get()->where([ $c . "_id" => $this->id ])->all;
  }
  
  public function has_one($name, $options = []) {
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
    $this->$name = $className::get()->where([ $c . "_id" => $this->id ])->first;
  }
  
  /*
  public function has_and_belongs_to_many($name, $options) {
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
    $this->$name = $className::get()->select($name . ".*")->joins("derp");
  }
  */
  
  public function belongs_to($name, $options = []) {
    if($this->id == null)
      throw new DbException("DbModel cannot make relations before its data has been crowded. Make sure to only build relations in model::relations()");
      
    $className = "";
    if(!isset($options["class"])) {
      $className = filename_to_classname(singularize($name));
    } else {
      $className = $options["class"];
    }
    $accessor = $name."_id";
    $this->$name = $className::get()->where([ static::$primary_key => $this->$accessor])->first;
  }
  
}