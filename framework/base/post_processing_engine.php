<?php
namespace framework\base;
class PostProcessingEngine {
  private $queue = null;
  function __construct() {
    $this->queue = [];
  }
  
  public function queue($fn) {
    $this->queue[] = $fn;
  }
  
  public function process() {
    foreach($this->queue as $item) {
      $item();
    }
  }
}