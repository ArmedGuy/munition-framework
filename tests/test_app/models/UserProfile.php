<?php
class UserProfile extends \Munition\DbModel\Base {
  public function relations() {
    echo static::table();
    $this->belongs_to("user");
  }
}