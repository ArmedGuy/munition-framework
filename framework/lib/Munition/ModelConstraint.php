<?php
namespace Munition;
class ModelConstraint {
  
  public static $default;
  
  public function can_get() {
    return true;
  }
  
  public function can_create() {
    return false;
  }
  
  public function can_update() {
    return false;
  }
  
  public function can_delete() {
    return false;
  }
  
}
ModelConstraint::$default = new ModelConstraint();