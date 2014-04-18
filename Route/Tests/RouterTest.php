<?php
namespace Route\Tests;

use Route\RouteBuilder;
use Route\RouteLink;
use Route\Router;

/**
 * Class RouterTest
 * Tests the main router class
 *
 * @package Route\Tests
 */
class RouterTest extends \PHPUnit_Framework_TestCase {
    /**
     * Builds a test route
     *
     * @return RouteLink
     */
    private function BuildTestRouteLink() {
        return new RouteLink('Acme\Coyote\Meeper', 'Meep', array('id' => 1));
    }

    /**
     * Builds some simple test routes to test the Router
     *
     * @return RouteBuilder
     */
    private function BuildTestRoutes() {
        // init test link
        $testLink = $this->BuildTestRouteLink();

        // init test builder
        $builder = new RouteBuilder();
        $builder->Add('testArticle', 'test/[id]/article/article-[articleId]', array('id' => '\d+', 'articleId' => '\d+'), array('get'), $testLink);
        $builder->Add('testGallery', 'test/[id]/gallery/[galleryId]', array('id' => '\d+', 'galleryId' => '\d+'), array('post', 'get'), $testLink);
        $builder->Add('test', 'test', array(), array('get'), $testLink);
        $builder->Add('home', '', array(), array('get'), $testLink);
        $builder->Add('testMulti', 'test/[id](/article/article-[articleId])(/gallery/gallery-[galleryId])', array('id' => '\d+', 'articleId' => '\d+', 'galleryId' => '\d+'), array('get'), $testLink);

        return $builder;
    }

    /**
     * Tests that Router() can be instantiated
     */
    public function testInit() {
        // build routes
        $builder = $this->BuildTestRoutes();

        // init router
        $router = new Router($builder->GetRouteRoot(), $builder->GetRouteNames());
        $this->assertInstanceOf('Route\Router', $router);
    }

    /**
     * Tests the Find() method
     */
    public function testFind() {
        // init router
        $builder = $this->BuildTestRoutes();
        $router = new Router($builder->GetRouteRoot(), $builder->GetRouteNames());

        // test the testArticle route
        $testArticle = $router->Find('test/12/article/article-12313', 'get');
        $this->assertEquals('Acme\Coyote\Meeper', $testArticle->GetModule());
        $this->assertEquals('Meep', $testArticle->GetController());
        $this->assertEquals(array('id' => '12', 'articleId' => '12313'), $testArticle->GetArguments());

        // test the testGallery route
        $testGallery = $router->Find('test/1/gallery/123124', 'post');
        $this->assertEquals('Acme\Coyote\Meeper', $testGallery->GetModule());
        $this->assertEquals('Meep', $testGallery->GetController());
        $this->assertEquals(array('id' => '1', 'galleryId' => '123124'), $testGallery->GetArguments());

        // test the test route
        $test = $router->Find('test', 'get');
        $this->assertEquals('Acme\Coyote\Meeper', $test->GetModule());
        $this->assertEquals('Meep', $test->GetController());
        $this->assertEquals(array('id' => '1'), $test->GetArguments());

        // test with a preceding slash
        $preceding = $router->Find('/test/1/gallery/123124', 'post');
        $this->assertEquals('Acme\Coyote\Meeper', $preceding->GetModule());
        $this->assertEquals('Meep', $preceding->GetController());
        $this->assertEquals(array('id' => '1', 'galleryId' => '123124'), $preceding->GetArguments());

        // test with a trailing slash
        $trailing = $router->Find('test/1/gallery/123124/', 'post');
        $this->assertEquals('Acme\Coyote\Meeper', $trailing->GetModule());
        $this->assertEquals('Meep', $trailing->GetController());
        $this->assertEquals(array('id' => '1', 'galleryId' => '123124'), $trailing->GetArguments());

        // test with multiple slashes in the middle
        $multiple = $router->Find('test/1/////gallery/123124', 'post');
        $this->assertEquals('Acme\Coyote\Meeper', $multiple->GetModule());
        $this->assertEquals('Meep', $multiple->GetController());
        $this->assertEquals(array('id' => '1', 'galleryId' => '123124'), $multiple->GetArguments());

        // test an empty route
        $multiple = $router->Find('', 'get');
        $this->assertEquals('Acme\Coyote\Meeper', $multiple->GetModule());
        $this->assertEquals('Meep', $multiple->GetController());
        $this->assertEquals(array('id' => '1'), $multiple->GetArguments());

    }

    /**
     * Tests the Get() method
     */
    public function testGet() {
        // init router
        $builder = $this->BuildTestRoutes();
        $router = new Router($builder->GetRouteRoot(), $builder->GetRouteNames());

        // test getting a route by name
        $byName = $router->Get('testArticle', array('id' => '123112', 'articleId' => '12'));
        $this->assertEquals('/test/123112/article/article-12', $byName);

        // test getting a route falling back to default params
        $defaults = $router->Get('testArticle', array('articleId' => '12'));
        $this->assertEquals('/test/1/article/article-12', $defaults);

        // test without a preceding slash
        $noSlash = $router->Get('testArticle', array('articleId' => '12'), false);
        $this->assertEquals('test/1/article/article-12', $noSlash);

        // test that still works even if not matching variable type, such as \d+
        $defaults = $router->Get('testArticle', array('articleId' => 'oh-no'));
        $this->assertEquals('/test/1/article/article-oh-no', $defaults);

        // test getting multiple routes
        $this->assertEquals('/test/1/gallery/gallery-12', $router->Get('testMulti', array('galleryId' => '12')));
        $this->assertEquals('/test/1/article/article-78', $router->Get('testMulti', array('articleId' => '78')));
        $this->assertEquals('/test/1/article/article-78/gallery/gallery-12', $router->Get('testMulti', array('galleryId' => '12', 'articleId' => '78')));
        $this->assertEquals('/test/1', $router->Get('testMulti'));
    }

    /**
     * Test what happens when a variable is left out
     *
     * @expectedException \Route\Exception\RouteCreateException
     */
    public function testGetFailMissingVar() {
        // init router
        $builder = $this->BuildTestRoutes();
        $router = new Router($builder->GetRouteRoot(), $builder->GetRouteNames());

        // fail by not setting the variables
        $router->Get('testArticle', array());
    }

    /**
     * Test what happens when a variable is left out
     *
     * @expectedException \Route\Exception\RouteCreateException
     */
    public function testTooManyVars() {
        // init router
        $builder = $this->BuildTestRoutes();
        $router = new Router($builder->GetRouteRoot(), $builder->GetRouteNames());

        // fail by giving too many variables
        $router->Get('testArticle', array('articleId' => '12', 'wibble' => '1', 'wobble' => 'sdsdf'));
    }

    /**
     * Test what happens if the wrong name is requested
     *
     * @expectedException \Route\Exception\RouteGetException
     */
    public function testGetWrongName() {
        // init router
        $builder = $this->BuildTestRoutes();
        $router = new Router($builder->GetRouteRoot(), $builder->GetRouteNames());

        // fail by not setting the variables
        $router->Get('testNotHere', array());
    }
}
