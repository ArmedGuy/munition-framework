<?php
class UserProfile extends \Munition\DbModel\Base {
  public function relations() {
    $this->belongs_to("user");
  }
}