<?php

namespace Route\Tests;

use Route;

/**
 * Class RouteNodeMatchesTest
 * Tests that RouteNodeMatches is working
 *
 * @package Route\Tests
 */
class RouteNodeMatchesTest extends \PHPUnit_Framework_TestCase {

    /**
     * Tests the RouteNodeMatches() can be instantiated
     */
    public function testInit() {
        $node = new Route\RouteNodeMatches();
        $this->assertTrue($node instanceof Route\RouteNodeMatches);
    }

    /**
     * Tests the SetNode and GetNode methods
     */
    public function testNode() {
        // test the constructor
        $childNode = $this->getMock('Route\RouteNode');
        $childNode->different = 1;
        $node = new Route\RouteNodeMatches($childNode);
        $this->assertEquals($childNode, $node->GetNode());

        // test the SetNode / GetNode pair
        $otherNode = $this->getMock('Route\RouteNode');
        $otherNode->different = 2;
        $node->SetNode($otherNode);
        $this->assertEquals($otherNode, $node->GetNode());
    }

    /**
     * Tests the SetMatches and GetMatches methods
     */
    public function testMatches() {
        // test the constructor
        $matches = array('one' => 1);
        $node = new Route\RouteNodeMatches(null, $matches);
        $this->assertEquals($matches, $node->GetMatches());

        // test the SetMatches / GetMatches pair
        $otherMatches = array('two' => 2);
        $node->SetMatches($otherMatches);
        $this->assertEquals($otherMatches, $node->GetMatches());
    }
}
