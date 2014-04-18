<?php

namespace Route;

/**
 * Class RouteNode
 * A trie node. These link to other RouteNodes if there are deeper branches of
 * possible routes to follow, and/or link to RouteLinks if there are endpoints
 * available.
 *
 * The Route class traverses through these nodes looking for one that match
 * parts a url. If it can match all nodes then it looks at the final node for
 * a matching verb. If that verb matches then the RouteNode will return a
 * RouteLink that points to a controller
 *
 * @package Route
 */
class RouteNode extends Base\BaseRouteNode {

}
