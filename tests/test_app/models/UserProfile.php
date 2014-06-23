<?php
class UserProfile extends \DbModel\Base {
  public static $primary_key = "user_id";
  public function relations() {
    echo static::table();
    $this->belongs_to("user");
  }
}