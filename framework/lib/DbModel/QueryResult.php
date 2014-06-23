<?php
namespace DbModel;
// see http://www.php.net/manual/en/class.arrayobject.php
class QueryResult extends \ArrayObject {
  function __construct($array) {
    parent::__construct($array);
  }
  
  public function each(callable $callable) {
    foreach($this as $value) {
      $callable($value);
    }
  }
  public function __get($name) {
    switch($name) {
      case "first":
        $v = $this->first(1);
        if(count($v) == 1) {
          return $v[0];
        } else {
          return QueryRow::$nullRow;
        }
        break;
      case "last":
        $v = $this->last(1);
        if(count($v) == 1) {
          return $v[0];
        } else {
          return QueryRow::$nullRow;
        }
        break;
      default:
        throw new DbException("Unknown getter name $name");
        break;
    }
  }

  public function toArray() {
      return $this->getArrayCopy();
  }
  public function first($num) {
    return array_slice($this->getArrayCopy(), 0, $num);
  }
  
  public function last($num) {
    return array_slice($this->getArrayCopy(), -$num);
  }
}