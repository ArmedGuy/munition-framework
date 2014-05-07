<?php
namespace Munition;
class ModelConstraint {
  
  public static $default;
  
  public function canGet() {
    return true;
  }
  
  public function canCreate() {
    return false;
  }
  
  public function canUpdate() {
    return false;
  }
  
  public function canDelete() {
    return false;
  }
  
}
ModelConstraint::$default = new ModelConstraint();