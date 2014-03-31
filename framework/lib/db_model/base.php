<?php
namespace DbModel;

class Base {
  
  public $id;
  
  private static $__initialized = false;
  private static $__dbtable;
  private static $__className;
  
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
  }
  
  private static function getQuery() {
    self::init();
    return new QueryBuilder(self::$__dbtable, self::$__className);
  }
  
  public static function make($data) {
    $c = get_called_class();
    $m = new $c();
    Base::crowd($m, $data);
    return $m;
  }
  
  private static function crowd($m, $data) {
    foreach($data as $k => $v) {
      $m->$k = $v; // first, set value
      $m->_values[$k] = $v;
      // TODO: add has$k() etc
    }
  }
  
  // Query Functions - proxy calls
  public static function where() {
    $q = self::getQuery();
    return call_user_func_array(array($q, "where"), func_get_args());
  }
  public static function where_not() {
    $q = self::getQuery();
    return call_user_func_array(array($q, "where_not"), func_get_args());
  }
  public static function select() {
    $q = self::getQuery();
    return call_user_func_array(array($q, "select"), func_get_args());
  }
  public static function order() {
    $q = self::getQuery();
    return call_user_func_array(array($q, "order"), func_get_args());
  }
  public static function group() {
    $q = self::getQuery();
    return call_user_func_array(array($q, "group"), func_get_args());
  }
  public static function having() {
    $q = self::getQuery();
    return call_user_func_array(array($q, "having"), func_get_args());
  }
  public static function limit() {
    $q = self::getQuery();
    return call_user_func_array(array($q, "limit"), func_get_args());
  }
  public static function offset() {
    $q = self::getQuery();
    return call_user_func_array(array($q, "offset"), func_get_args());
  }
  
  
  public static function all() {
    return self::getQuery()->all;
  }
  
  public static function first() {
    if(func_num_args() == 1) {
      return self::getQuery()->first(func_get_arg(0));
    } else {
      return self::getQuery()->first->instance();
    }
  }
  
  public static function last() {
    if(func_num_args() == 1) {
      return self::getQuery()->last(func_get_arg(0));
    } else {
      return self::getQuery()->last->instance();
    }
  }
  
  public static function take() {
    if(func_num_args() == 1) {
      return self::getQuery()->take(func_get_arg(0));
    } else {
      return self::getQuery()->take->instance();
    }
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
  
  
  public function has_many($name, $opt = []) {
    self::init();
    $className = "";
    if(!isset($opt["class"])) {
      $className = filename_to_classname(singularize($name));
    } else {
      $className = $opt["class"];
    }
    $c = strtolower(get_called_class());
    $this->$name = $className::where([ $c . "_id" => $this->id ])->all;
  }
  
  public function has_one($name, $opt) {
  }
  
  public function belongs_to($name, $opt) {
  }
  
}