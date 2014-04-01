<?php
use \Lclzr\Locale as L;
class LocalizerTest extends PHPUnit_Framework_TestCase {
  public function testLoadAndShowLocale() {
    L::loadFolder("./tests/test_config/locales/");
    L::$current = "en";
    $str = L::t("user.new");
    $this->assertEquals("Create User", $str);
  }
  
  public function testStringFormatting() {
    L::loadFolder("./tests/test_config/locales/");
    L::$current = "en";
    $str = L::t("user.hi", "ArmedGuy");
    $this->assertEquals("Hi ArmedGuy!", $str);
  }
}