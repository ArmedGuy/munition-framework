<?php
namespace framework\db;

class DbModel {
  private static $__initialized = false;
  private static $__dbtable;
  private static $__className;
  
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
    return new DbModelQuery(self::$__dbtable, self::$__className);
  }
  
  public static function make($data) {
    $c = get_called_class();
    $m = new $c();
    DbModel::crowd($m, $data);
    return $m;
  }
  
  private static function crowd($m, $data) {
    foreach($data as $k => $v) {
      $m->$k = $v; // first, set value
      
      // TODO: add has$k() etc
    }
  }
  
  // Query Functions
  public static function where() {
    $q = self::getQuery();
    return call_user_func_array(array($q, "where"), func_get_args());
  }
  public static function select() {
    $q = self::getQuery();
    return call_user_func_array(array($q, "select"), func_get_args());
  }
  public static function order() {
    $q = self::getQuery();
    return call_user_func_array(array($q, "order"), func_get_args());
  }
  public static function limit() {
    $q = self::getQuery();
    return call_user_func_array(array($q, "limit"), func_get_args());
  }
  public static function offset() {
    $q = self::getQuery();
    return call_user_func_array(array($q, "offset"), func_get_args());
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
  
  
  public function has_many() {
  }
  
  public function has_one() {
  }
  
  public function belongs_to() {
  }
  
}