<?php
class User extends \DbModel\Base {

  function relations() {
    $this->has_many ("posts");
    
    $this->has_many("group_permissions", ["class" => "GroupPermission"]);
    $this->has_many("groups", ["through" => "group_permissions"]);
  }
}