<?php

namespace Route\Tests;

use Route;

/**
 * Class RouteNodeArgumentsTest
 * Tests that RouteNodeArguments is working
 *
 * @package Route\Tests
 */
class RouteNodeArgumentsTest extends \PHPUnit_Framework_TestCase {

    /**
     * Tests the RouteNodeArguments() can be instantiated
     */
    public function testInit() {
        $node = new Route\RouteNodeArguments();
        $this->assertEquals(get_class($node), 'Route\RouteNodeArguments');
    }

    /**
     * Tests the SetNode and GetNode methods
     */
    public function testNode() {
        // test the constructor
        $childNode = $this->getMock('Route\RouteNode');
        $childNode->different = 1;
        $node = new Route\RouteNodeArguments($childNode);
        $this->assertEquals($childNode, $node->GetNode());

        // test the SetNode / GetNode pair
        $otherNode = $this->getMock('Route\RouteNode');
        $otherNode->different = 2;
        $node->SetNode($otherNode);
        $this->assertEquals($otherNode, $node->GetNode());
    }

    /**
     * Tests the SetArguments and GetArguments methods
     */
    public function testArgs() {
        // test the constructor
        $args = array(1 => 'one');
        $node = new Route\RouteNodeArguments(null, $args);
        $this->assertEquals($args, $node->GetArguments());

        // test the SetArguments / GetArguments pair
        $otherArgs = array(2 => 'two');
        $node->SetArguments($otherArgs);
        $this->assertEquals($otherArgs, $node->GetArguments());
    }
}
