<?php
use \Lclzr\Locale as L;
class DbModelTest extends PHPUnit_Framework_TestCase {
  public function testLoadAndShowLocale() {
    L::loadFolder("./tests/test_config/locales/");
    L::$current = "en";
    $str = L::t("user.new");
    $this->assertEquals("New User", $str);
  }
}