<?php
namespace Route\Tests;

use Route\RouteBuilder;
use Route\RouteLink;
use Route\RouteNode;
use Route\RouteWriter;

/**
 * Class RouteWriterTest
 * Tests the route writer class
 *
 * @package Route\Tests
 */
class RouteWriterTest extends \PHPUnit_Framework_TestCase {
    /**
     * Tests the RouteWriter() can be instantiated
     */
    public function testInit() {
        $writer = new RouteWriter(new RouteNode(), array());
        $this->assertInstanceOf('Route\RouteWriter', $writer);
    }

    /**
     * Tests the writer output matches the routes built by the builder
     */
    public function testOutput() {
        // init a test link
        $testLink = new RouteLink('Acme\Coyote\Meep', 'Meep', array('id' => 1));

        // init the routes
        $builder = new RouteBuilder();
        $builder->Add('testArticle', 'test/[id](/article/article-[articleId])(/gallery/gallery-[galleryId])(/other)', array('id' => '\d+', 'articleId' => '\d+', 'galleryId' => '\d+'), array('get'), $testLink);
        $builder->Add('testGallery', 'test/[id]/gallery/[galleryId]', array('id' => '\d+', 'galleryId' => '\d+'), array('post', 'get'), $testLink);
        $builder->Add('test', 'test', array(), array('get'), $testLink);
        $builder->Add('home', '', array(), array('get'), $testLink);

        // init the writer and fake the output being written to cache and then parsed
        $rootNode = $routes = '';
        $writer = new RouteWriter($builder->GetRouteRoot(), $builder->GetRouteNames());
        $code = $writer->OutputCode();
        foreach ($code as $line) {
            eval($line);
        }

        // test the asserts
        $this->assertEquals($rootNode, $builder->GetRouteRoot());
        $this->assertEquals($routes, $builder->GetRouteNames());
    }
}