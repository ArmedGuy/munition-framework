<?php
namespace Munition;
class ResourceConstraint {
  
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
ResourceConstraint::$default = new ResourceConstraint();