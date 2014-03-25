<?php

namespace Route\Base;

use Route;

/**
 * Class RouteNodeArguments
 * A store for a route node and it's corresponding arguments array. A dynamic
 * RouteNode can be of the format `article-(\d+)`, so the arguments in this
 * case would `array(0 => 'id')`.
 *
 * The key is the regex-matched value and the value is the name of the
 * argument. This array should be 0-indexed as RouteNode offsets it by 1 so
 * that it can use preg_match (and 0 is normally the entire match). You could
 * also write this as ('id').
 *
 * @package Route\Base
 */
class RouteNodeArguments {

    /**
     * A store for the RouteNode
     * @var Route\RouteNode
     */
    private $node;

    /**
     * A store for the arguments
     * @var string[]
     */
    private $arguments;

    /**
     * The constructor
     *
     * @param Route\RouteNode $node The RouteNode to store with arguments
     * @param string[] $args An array of arguments to store
     */
    public function __construct(Route\RouteNode $node = null, array $args = array()) {
        if ($node !== null) {
            $this->SetNode($node);
        }
        $this->SetArguments($args);
    }

    /**
     * Sets the node for later
     *
     * @param Route\RouteNode $node The RouteNode to store against arguments
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
     * Sets the arguments for later recall
     *
     * @param string[] $args An array of arguments to store
     */
    public function SetArguments(array $args) {
        $this->arguments = $args;
    }

    /**
     * Gets the arguments
     *
     * @return string[]
     */
    public function GetArguments() {
        return $this->arguments;
    }
}
