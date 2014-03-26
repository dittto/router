<?php

namespace Route\Tests;

use Route;

/**
 * Class RouteLinkTest
 * Handles test for the route link
 *
 * @package Route\Tests
 */
class RouteLinkTest extends \PHPUnit_Framework_TestCase {

    /**
     * Tests the RouteLink() object can be instantiated
     */
    public function testRouteLinkType() {
        $link = new Route\RouteLink();
        $this->assertTrue($link instanceof Route\RouteLink);
    }

    /**
     * Tests that the module getting and setting is valid with strings
     */
    public function testModuleValid() {
        // test simple string
        $link = new Route\RouteLink();
        $link->SetModule('a');
        $this->assertEquals('a', $link->GetModule());

        // test longer string
        $link = new Route\RouteLink();
        $link->SetModule('abba');
        $this->assertEquals('abba', $link->GetModule());
    }

    /**
     * Tests module get/sets fail with nulls
     *
     * @expectedException Route\Exception\RouteLinkSetException
     */
    public function testModuleFailNull() {
        $link = new Route\RouteLink();
        $link->SetModule(null);
        $this->assertEquals(null, $link->GetModule());
    }

    /**
     * Tests module get/sets fail with ints
     *
     * @expectedException Route\Exception\RouteLinkSetException
     */
    public function testModuleFailInt() {
        $link = new Route\RouteLink();
        $link->SetModule(1);
        $this->assertEquals(1, $link->GetModule());
    }

    /**
     * Tests module get/sets fail with arrays
     *
     * @expectedException Route\Exception\RouteLinkSetException
     */
    public function testModuleFailArray() {
        $link = new Route\RouteLink();
        $link->SetModule(array());
        $this->assertEquals(array(), $link->GetModule());
    }

    /**
     * Tests that the controller getting and setting is valid with strings
     */
    public function testControllerValid() {
        // test simple string
        $link = new Route\RouteLink();
        $link->SetController('a');
        $this->assertEquals('a', $link->GetController());

        // test longer string
        $link = new Route\RouteLink();
        $link->SetController('abba');
        $this->assertEquals('abba', $link->GetController());
    }

    /**
     * Tests controller get/sets fail with nulls
     *
     * @expectedException Route\Exception\RouteLinkSetException
     */
    public function testControllerFailNull() {
        $link = new Route\RouteLink();
        $link->SetController(null);
        $this->assertEquals(null, $link->GetController());
    }

    /**
     * Tests controller get/sets fail with ints
     *
     * @expectedException Route\Exception\RouteLinkSetException
     */
    public function testControllerFailInt() {
        $link = new Route\RouteLink();
        $link->SetController(1);
        $this->assertEquals(1, $link->GetController());
    }

    /**
     * Tests controller get/sets fail with arrays
     *
     * @expectedException Route\Exception\RouteLinkSetException
     */
    public function testControllerFailArray() {
        $link = new Route\RouteLink();
        $link->SetController(array());
        $this->assertEquals(array(), $link->GetController());
    }

    /**
     * Tests the default args can be get and set
     */
    public function testDefaultArgs() {
        $link = new Route\RouteLink();
        $args = array('this' => 'that');
        $link->SetDefaultArgs($args);
        $this->assertEquals($args, $link->GetDefaultArgs());
    }

    /**
     * Tests the constructor works with all options
     */
    public function testConstructor() {
        // init vars
        $module = 'mod';
        $controller = 'control';
        $args = array('this' => 'that');

        // test the constructor with no defaults
        $link = new Route\RouteLink();
        $this->assertEquals('', $link->GetModule());
        $this->assertEquals('', $link->GetController());
        $this->assertEquals(array(), $link->GetDefaultArgs());

        // test the constructor with all defaults
        $link = new Route\RouteLink($module, $controller, $args);
        $this->assertEquals($module, $link->GetModule());
        $this->assertEquals($controller, $link->GetController());
        $this->assertEquals($args, $link->GetDefaultArgs());
    }
}
