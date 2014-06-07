<?php
class Post extends \Munition\DbModel\Base {
  public function relations() {
    $this->belongs_to("user");
  }
}