<?php

namespace Route\Tests;

use Route;

/**
 * Class RouteNodeTest
 * Handles tests for the route node
 *
 * @package Route\Tests
 */
class RouteNodeTest extends \PHPUnit_Framework_TestCase {

    /**
     * Tests the RouteNode() can be instantiated
     */
    public function testInit() {
        $node = new Route\RouteNode();
        $this->assertTrue($node instanceof Route\RouteNode);
    }

    /**
     * Tests that adding and retrieving valid static route nodes is ok. This
     * creates 3 mock objects and then fakes a value in them to make them
     * appear different to each other, so we can tell we are receiving the
     * correct mock object back
     */
    public function testAddStaticValid() {
        // init node
        $node = new Route\RouteNode();

        // test a single node
        $childNode = $this->getMock('Route\RouteNode');
        $node->AddStatic('test', $childNode);
        $this->assertEquals($childNode, $node->FindStatic('test'));

        // test multiple nodes
        $otherNode = $this->getMock('Route\RouteNode');
        $node->AddStatic('other-test', $otherNode);
        $this->assertEquals($otherNode, $node->FindStatic('other-test'));
        $this->assertEquals($childNode, $node->FindStatic('test'));

        // test overwriting a node
        $overwriteNode = $this->getMock('Route\RouteNode');
        $childNode->statics = array(1);
        $overwriteNode->statics = array(3);
        $node->AddStatic('test', $overwriteNode);
        $this->assertEquals($overwriteNode, $node->FindStatic('test'));
        $this->assertNotEquals($childNode, $node->FindStatic('test'));
    }

    /**
     * Tests what happens if add static is called with an empty path
     *
     * @expectedException Route\Exception\RouteCreateException
     */
    public function testAddStaticEmpty() {
        $node = new Route\RouteNode();
        $childNode = $this->getMock('Route\RouteNode');
        $node->AddStatic('', $childNode);
    }

    /**
     * Tests what happens if add static is called with an object
     *
     * @expectedException Route\Exception\RouteCreateException
     */
    public function testAddStaticObject() {
        $node = new Route\RouteNode();
        $childNode = $this->getMock('Route\RouteNode');
        $node->AddStatic((object)'test', $childNode);
    }

    /**
     * Tests what happens if add static is called with a null
     *
     * @expectedException Route\Exception\RouteCreateException
     */
    public function testAddStaticNull() {
        $node = new Route\RouteNode();
        $childNode = $this->getMock('Route\RouteNode');
        $node->AddStatic(null, $childNode);
    }

    /**
     * Tests what happens if add static is called with a null
     *
     * @expectedException Route\Exception\RouteMatchException
     */
    public function testFindStaticWrongPath() {
        $node = new Route\RouteNode();
        $childNode = $this->getMock('Route\RouteNode');
        $node->AddStatic('test', $childNode);
        $this->assertEquals($childNode, $node->FindStatic('not-test'));
    }

    /**
     * Tests that AddDynamic and FindDynamic work with valid options
     */
    public function testAddDynamicValid() {
        // init node
        $node = new Route\RouteNode();

        // test a single node
        $childNode = $this->getMock('Route\RouteNode');
        $node->AddDynamic('\d+', $childNode);
        $found = $node->FindDynamic('69');
        $this->assertEquals($childNode, $found->GetNode());
        $this->assertEquals(array(), $found->GetMatches());

        // test multiple nodes
        $otherNode = $this->getMock('Route\RouteNode');
        $node->AddDynamic('article-([a-z0-9]+)-(\d+)', $otherNode, array(0 => 'title', 1 => 'id'));
        $childFound = $node->FindDynamic('article-test-123');
        $this->assertEquals($otherNode, $childFound->GetNode());
        $this->assertEquals(array('title' => 'test', 'id' => '123'), $childFound->GetMatches());
        $originalFound = $node->FindDynamic('69');
        $this->assertEquals($childNode, $originalFound->GetNode());
        $this->assertEquals(array(), $originalFound->GetMatches());

        // test overwriting a node
        $overwriteNode = $this->getMock('Route\RouteNode');
        $childNode->dynamics = array(1);
        $overwriteNode->dynamics = array(3);
        $node->AddDynamic('\d+', $overwriteNode);
        $overwriteFound = $node->FindDynamic('69');
        $replacedFound = $node->FindDynamic('69');
        $this->assertEquals($overwriteNode, $overwriteFound->GetNode());
        $this->assertNotEquals($childNode, $replacedFound->GetNode());

        // test complex regex
        $complexNode = $this->getMock('Route\RouteNode');
        $node->AddDynamic('article-((\d+)-([a-z0-9\-]+-(\d+?){0,3}))', $complexNode, array(null, 'article-id', 'section-slug', 'section-id'));
        $found = $node->FindDynamic('article-1040-section-name-106');
        $args = array('article-id' => 1040, 'section-slug' => 'section-name-106', 'section-id' => 106);
        $this->assertEquals($complexNode, $found->GetNode());
        $this->assertEquals($args, $found->GetMatches());
    }

    /**
     * Tests what happens if add dynamic is called with an empty path
     *
     * @expectedException Route\Exception\RouteCreateException
     */
    public function testAddDynamicEmpty() {
        $node = new Route\RouteNode();
        $childNode = $this->getMock('Route\RouteNode');
        $node->AddStatic('', $childNode);
    }

    /**
     * Tests what happens if add dynamic is called with an object
     *
     * @expectedException Route\Exception\RouteCreateException
     */
    public function testAddDynamicObject() {
        $node = new Route\RouteNode();
        $childNode = $this->getMock('Route\RouteNode');
        $node->AddStatic((object)'test', $childNode);
    }

    /**
     * Tests what happens if add dynamic is called with a null
     *
     * @expectedException Route\Exception\RouteCreateException
     */
    public function testAddDynamicNull() {
        $node = new Route\RouteNode();
        $childNode = $this->getMock('Route\RouteNode');
        $node->AddStatic(null, $childNode);
    }

    /**
     * Tests what happens if add dynamic is called with a null
     *
     * @expectedException Route\Exception\RouteMatchException
     */
    public function testFindDynamicWrongPath() {
        $node = new Route\RouteNode();
        $childNode = $this->getMock('Route\RouteNode');
        $node->AddStatic('test', $childNode);
        $this->assertEquals($childNode, $node->FindStatic('not-test'));
    }

    /**
     * Test that the Find command for both static and dynamic nodes
     */
    public function testFindValid() {
        // init vars
        $node = new Route\RouteNode();

        // add routes
        $staticNode = $this->getMock('Route\RouteNode');
        $staticNode->static = true;
        $dynamicNode = $this->getMock('Route\RouteNode');
        $dynamicNode->static = false;
        $node->AddStatic('hello', $staticNode);
        $node->AddDynamic('hello-(\d+)', $dynamicNode, array('id'));

        // test static
        $static = $node->Find('hello');
        $this->assertTrue($static instanceof Route\RouteNode);
        $this->assertEquals($staticNode, $static);

        // test dynamic
        $dynamic = $node->Find('hello-47');
        $this->assertTrue($dynamic instanceof Route\RouteNodeMatches);
        $this->assertEquals($dynamicNode, $dynamic->GetNode());
        $this->assertEquals(array('id' => 47), $dynamic->GetMatches());

        // test that static overrides dynamic
        $staticOverrideNode = $this->getMock('Route\RouteNode');
        $staticOverrideNode->override = true;
        $node->AddStatic('hello-47', $staticOverrideNode);
        $override = $node->Find('hello-47');
        $this->assertTrue($override instanceof Route\RouteNode);
        $this->assertEquals($staticOverrideNode, $override);
    }

    /**
     * Tests what happens if Find cannot match either a static or dynamic node
     *
     * @expectedException Route\Exception\RouteMatchException
     */
    public function testFindWrongPath() {
        // init vars
        $node = new Route\RouteNode();

        // add routes
        $staticNode = $this->getMock('Route\RouteNode');
        $staticNode->static = true;
        $dynamicNode = $this->getMock('Route\RouteNode');
        $dynamicNode->static = false;
        $node->AddStatic('hello', $staticNode);
        $node->AddDynamic('hello-(\d+)', $dynamicNode, array('id'));

        // test error
        $static = $node->Find('hola');
    }
}
