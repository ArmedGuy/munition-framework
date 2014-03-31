<?php
namespace Munition;
class PostProcessingEngine {
  private $_queue = null;
  function __construct() {
    $this->_queue = [];
  }
  
  public function queue($fn) {
    $this->_queue[] = $fn;
  }
  
  public function process() {
    try {
      foreach($this->_queue as $item) {
        $item();
      }
    } catch(Exception $e) {
      return;
    }
  }
}