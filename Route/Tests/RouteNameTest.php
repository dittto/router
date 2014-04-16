<?php
namespace Route\Tests;

use Route;

/**
 * Class RouteNameTest
 * Tests the route name class. This simply holds the route link together with route urls
 *
 * @package Route\Tests
 */
class RouteNameTest extends \PHPUnit_Framework_TestCase {
    /**
     * Tests that the class inits
     */
    public function testInit() {
        // init vars
        $name = new Route\RouteName('', $this->getMock('Route\RouteLink'));
        $this->assertInstanceOf('Route\RouteName', $name);
    }

    /**
     * Tests the url is returned correctly
     */
    public function testGetUrl() {
        $link = $this->getMock('Route\RouteLink');
        $link->test = 1;
        $name = new Route\RouteName('url-name', $link);
        $this->assertEquals('url-name', $name->GetUrl());
    }

    /**
     * Tests the route link is returned correctly
     */
    public function testGetRouteLink() {
        $link = $this->getMock('Route\RouteLink');
        $link->test = 1;
        $name = new Route\RouteName('url-name', $link);
        $this->assertEquals($link, $name->GetRouteLink());
    }
}
