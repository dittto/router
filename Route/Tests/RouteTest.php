<?php

namespace Route\Tests;

use Route;

/**
 * Class RouteTest
 * Tests the Route class
 *
 * @package Route\Tests
 */
class RouteTest extends \PHPUnit_Framework_TestCase {

    /**
     * Tests the find method against valid urls
     *
     * @param string $url The url to find the route for
     * @param string $module The module the url should match
     * @param string $controller The controller the url should match
     * @dataProvider findTestData
     */
    public function testFindValid($url, $module, $controller) {
        // init the route
        $route = new Route\Route();
        $match = $route->Find($url);

        // test the route is as expected
        $this->assertNotNull($match);
        $this->assertEquals($module, $match->getModule());
        $this->assertEquals($controller, $match->getController());
    }

    /**
     * The test data for testFind()
     *
     * @return array An array of arrays of data to pass to testFind()
     */
    public function findTestData() {
        return array(
            // array('/', 'default', 'default'),
            // array('/test', 'test', 'index'),
        );
    }
}
