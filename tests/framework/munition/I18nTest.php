<?php
use \Munition\I18n as L;
class I18nTest extends PHPUnit_Framework_TestCase {
  public function testLoadAndShowLocale() {
    L::loadFolder("./tests/test_app/locales/");
    L::$current = "en";
    $str = L::t("user.new");
    $this->assertEquals("Create User", $str);
  }
  
  public function testStringFormatting() {
    L::loadFolder("./tests/test_app/locales/");
    L::$current = "en";
    $str = L::t("user.hi", "ArmedGuy");
    $this->assertEquals("Hello ArmedGuy!", $str);
  }
  
  public function testUnknownString() {
    L::loadFolder("./tests/test_app/locales/");
    L::$current = "en";
    $str = L::t("swag");
    $this->assertEquals("{Unknown locale: swag}", $str);
  }
  public function testMergeDefinition() {
    L::loadFolder("./tests/test_app/locales/");
    L::$current = "en";
    $str = L::t("user.new");
    $this->assertEquals("Create User", $str);
    
    L::definition("en", ["user" => [ "new" => "New User"] ]);
    $str = L::t("user.new");
    $this->assertEquals("New User", $str);
  }
}