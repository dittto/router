<?php

namespace Route\Base;

/**
 * Class RouteNode
 * A trie node. These link to other RouteNodes if there are deeper branches of
 * possible routes to follow, and/or link to RouteLinks if there are endpoints
 * available
 *
 * @package Route\Base
 */
class RouteNode {

    private $statics;

    private $dynamics;

    private $verbs;

    public function Find($path) {

    }

    public function FindStatic($path) {

    }

    public function FindDynamic($regex) {

    }

    public function FindVerb($verb) {

    }

    public function AddStatic($path, RouteNode $node) {

    }

    public function AddDynamic($path, RouteNode $node) {

    }

    public function SetVerb($verb, RouteLink $link) {

    }
}
