<?php
namespace Route\Tests;

use Route;

/**
 * Class RouteLinkArgumentsTest
 * Tests the route link w/ arguments class - this is returned from the router
 * class
 *
 * @package Route\Tests
 */
class RouteLinkArgumentsTest extends \PHPUnit_Framework_TestCase {

    /**
     * Tests that the class inits
     */
    public function testInit() {
        // init vars
        $link = new Route\RouteLink('mod', 'control');
        $linkArgs = new Route\RouteLinkArguments($link);
        $this->assertInstanceOf('Route\RouteLinkArguments', $linkArgs);
    }

    /**
     * Tests that arguments are retrieve correctly, and merge with the route
     * link's default values so that the arguments in RouteLinkArguments have
     * priority
     */
    public function testGetArguments() {
        // test RouteLink args only
        $link = new Route\RouteLink('mod', 'control', array('one' => 1, 'two' => 2));
        $linkArgs = new Route\RouteLinkArguments($link);
        $this->assertEquals(array('one' => 1, 'two' => 2), $linkArgs->GetArguments());

        // test RouteLinkArguments args only
        $link2 = new Route\RouteLink('mod', 'control');
        $linkArgs2 = new Route\RouteLinkArguments($link2, array('three' => 3, 'four' => 4));
        $this->assertEquals(array('three' => 3, 'four' => 4), $linkArgs2->GetArguments());

        // test merging arguments
        $link3 = new Route\RouteLink('mod', 'control', array('one' => 1, 'two' => 2, 'three' => 5));
        $linkArgs3 = new Route\RouteLinkArguments($link3, array('three' => 3, 'four' => 4));
        $this->assertEquals(array('one' => 1, 'two' => 2, 'three' => 3, 'four' => 4), $linkArgs3->GetArguments());

        // test no args on either
        $link4 = new Route\RouteLink('mod', 'control');
        $linkArgs4 = new Route\RouteLinkArguments($link4);
        $this->assertEquals(array(), $linkArgs4->GetArguments());
    }

    /**
     * Tests the module from the route link is returned correctly
     */
    public function testGetModule() {
        $link = new Route\RouteLink('mod', 'control', array('one' => 1, 'two' => 2));
        $linkArgs = new Route\RouteLinkArguments($link);
        $this->assertEquals('mod', $linkArgs->GetModule());
    }

    /**
     * Tests the controller from the route link is returned correctly
     */
    public function testGetController() {
        $link = new Route\RouteLink('mod', 'control', array('one' => 1, 'two' => 2));
        $linkArgs = new Route\RouteLinkArguments($link);
        $this->assertEquals('control', $linkArgs->GetController());
    }
}
