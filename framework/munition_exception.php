<?php
class MunitionException extends Exception {
  function __construct($errno, $errstr, $errfile = null, $errline = 0, $errcontext = null) {
    if($errfile != null) {
      $errstr .= " in '" . $errfile . " (line ". $errline.")'";
    }
    parent::__construct($errstr, $errno);
  }
}