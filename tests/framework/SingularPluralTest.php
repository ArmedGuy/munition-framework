<?php
class SingularPluralTest extends PHPUnit_Framework_TestCase {
  public function testSingularize() {
    $this->assertEquals("human", singularize("humans"));
    $this->assertEquals("alias", singularize("aliases"));
    $this->assertEquals("story", singularize("stories"));
  }
  public function testPluralize() {
    $this->assertEquals("humans", pluralize("human"));
    $this->assertEquals("aliases", pluralize("alias"));
    $this->assertEquals("stories", pluralize("story"));


  }
}