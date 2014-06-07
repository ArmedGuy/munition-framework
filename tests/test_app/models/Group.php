<?php
class Group extends \Munition\DbModel\Base {
  public function relations() {
    $this->has_many("permissions", ["class" => "GroupPermission"]);
    $this->has_many("users", ["through" => "permissions"]);
  }
}