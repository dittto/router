<?php
namespace Route\Tests;

use Route;

/**
 * Class RouteBuilderTest
 * Tests the route builder can build routes tries as expected
 *
 * @package Route\Tests
 */
class RouteBuilderTest extends \PHPUnit_Framework_TestCase {

    /**
     * Tests the RouteBuilder() can be instantiated
     */
    public function testInit() {
        $builder = new Route\RouteBuilder();
        $this->assertInstanceOf('Route\RouteBuilder', $builder);
    }

    /**
     * Tests that the builder can auto-init a route node, and that when passed
     * one you get the same one back
     */
    public function testGetRouteNode() {
        // init mocks
        $autoInited = new Route\RouteNode();
        $passed = new Route\RouteNode();
        $passed->test = true;

        // init builder and test that it auto-inits a route node
        $autoBuilder = new Route\RouteBuilder();
        $this->assertEquals($autoInited, $autoBuilder->GetRouteRoot());

        // init a new builder and check that the passed route is the same one out
        $passedBuilder = new Route\RouteBuilder($passed);
        $this->assertEquals($passed, $passedBuilder->GetRouteRoot());
    }

    /**
     * Test adding a route to the builder and see that the root route contains
     * the correct paths
     */
    public function testAddSingleRoute() {
        // init route link
        $testLink = new Route\RouteLink('Acme\Coyote\Meep', 'Meep', array('id' => 1));

        // init node setup
        $builder = new Route\RouteBuilder();
        $builder->Add('routeName', 'route-path', array(), array('get'), $testLink);

        // test the route name exists
        $routePath = new Route\RouteName('route-path', $testLink);
        $this->assertEquals(array('routeName' => $routePath), $builder->GetRouteNames());

        // test the route node contains the expected data
        $root = $builder->GetRouteRoot();
        $testRoute = $root->FindStatic('route-path');
        $this->assertInstanceOf('Route\RouteNode', $testRoute);

        // test the route has the expected values
        $this->assertEquals($testLink, $testRoute->FindVerb('get'));
    }

    /**
     * Test adding routes to the builder and see that the root route contains
     * the correct paths
     */
    public function testAddMultipleRoutes() {
        // init route link
        $testLink = new Route\RouteLink('Acme\Coyote\Meep', 'Meep', array('id' => 1));

        // init node setup
        $builder = new Route\RouteBuilder();
        $builder->Add('testArticle', 'test/[id]/article/article-[articleId]', array('id' => '\d+', 'articleId' => '\d+'), array('get'), $testLink);
        $builder->Add('testGallery', 'test/[id]/gallery/[galleryId]', array('id' => '\d+', 'galleryId' => '\d+'), array('post', 'get'), $testLink);
        $builder->Add('test', 'test', array(), array('get'), $testLink);

        // test the route names are valid
        $routeNames = $builder->GetRouteNames();
        $this->assertEquals('test/[id]/article/article-[articleId]', $routeNames['testArticle']->getUrl());
        $this->assertEquals('test/[id]/gallery/[galleryId]', $routeNames['testGallery']->getUrl());
        $this->assertEquals('test', $routeNames['test']->GetUrl());

        // test the route nodes contains the expected data
        $root = $builder->GetRouteRoot();
        $this->assertInstanceOf('Route\RouteNode', $root->FindStatic('test'));
        $testRoute = $root->FindStatic('test');
        $this->assertInstanceOf('Route\RouteNodeMatches', $testRoute->FindDynamic('12'));
        $this->assertInstanceOf('Route\RouteNodeMatches', $testRoute->FindDynamic('100008'));
        $idRoute = $testRoute->FindDynamic('12')->GetNode();
        $this->assertInstanceOf('Route\RouteNode', $idRoute->FindStatic('article'));
        $typeRoute = $idRoute->FindStatic('article');
        $this->assertInstanceOf('Route\RouteNodeMatches', $typeRoute->FindDynamic('article-110343'));
        $articleRoute = $typeRoute->FindDynamic('article-110343')->GetNode();

        // test the verbs are as expected
        $this->assertEquals($testLink, $testRoute->FindVerb('get'));
        $this->assertEquals($testLink, $articleRoute->FindVerb('get'));
    }
}
