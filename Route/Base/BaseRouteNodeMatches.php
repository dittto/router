<?php

namespace Route\Base;

use Route;

/**
 * Class BaseRouteNodeMatches
 * This is a wrapper for a dynamic route node together with it's matches, in
 * the format of array('id' => '23')
 *
 * @package Route\Base
 */
abstract class BaseRouteNodeMatches {

    /**
     * A store for the RouteNode
     * @var Route\RouteNode
     */
    private $node;

    /**
     * A store for the matches
     * @var string[]
     */
    private $matches;

    /**
     * The constructor
     *
     * @param Route\RouteNode $node The RouteNode to store with matches
     * @param string[] $matches An array of matches to store
     */
    public function __construct(Route\RouteNode $node = null, array $matches = array()) {
        if ($node !== null) {
            $this->SetNode($node);
        }
        $this->SetMatches($matches);
    }

    /**
     * Sets the node for later
     *
     * @param Route\RouteNode $node The RouteNode to store against matches
     */
    public function SetNode(Route\RouteNode $node) {
        $this->node = $node;
    }

    /**
     * Gets the RouteNode
     *
     * @return Route\RouteNode
     */
    public function GetNode() {
        return $this->node;
    }

    /**
     * Sets the matches for later recall
     *
     * @param string[] $matches An array of matches to store
     */
    public function SetMatches(array $matches) {
        $this->matches = $matches;
    }

    /**
     * Gets the matches
     *
     * @return string[]
     */
    public function GetMatches() {
        return $this->matches;
    }
}
