<?php
namespace framework\base;
class DbModelResult {
  private $attrs = null;
  private $__className;
  function __construct($attrs, $to) {
    $this->attrs = $attrs;
    $this->__className = $to;
  }
  
  
  public function __get($attr) {
    if(isset($this->attrs[$attr])) {
      return $attr;
    } else {
      return null;
    }
  }
  
  public function instance() {
    if($this->attrs == null) {
      return null;
    } else {
      return call_user_func(array($this->__className, "make"), $this->attrs);
    }
  }
  public function toString() {
    return print_r($this->attrs, false);
  }
}