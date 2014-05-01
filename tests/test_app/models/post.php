<?php
class Post extends \DbModel\Base {
  public function relations() {
    $this->belongs_to("user");
  }
}