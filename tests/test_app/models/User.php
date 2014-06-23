<?php
class User extends \DbModel\Base {

  function relations() {
    $this->has_many ("posts");

    $this->has_one("user_profile");
    
    $this->has_many("group_permissions", ["class" => "GroupPermission"]);
    $this->has_many("groups", ["through" => "group_permissions"]);
    
    $this->has_and_belongs_to_many("grps", ["class" => "Group", "table" => "user_groups"]);
  }
}