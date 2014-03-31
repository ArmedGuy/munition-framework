<?php
namespace DbModel;
class QueryRow {

  public static $nullRow;

  private $_attrs = null;
  private $__className;
  
  function __construct($attrs, $to) {
    $this->_attrs = $attrs;
    $this->__className = $to;
  }
  
  
  public function __get($attr) {
    if(isset($this->_attrs[$attr])) {
      return $this->_attrs[$attr];
    } else {
      return null;
    }
  }
  
  public function instance() {
    if($this->_attrs == null) {
      return null;
    } else {
      return call_user_func(array($this->__className, "make"), $this->_attrs);
    }
  }
  public function toString() {
    return print_r($this->_attrs, false);
  }
}
QueryRow::$nullRow = new QueryRow(null, null);