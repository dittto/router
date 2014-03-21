<?php

namespace Route\Base;

class Route {

    /**
     * The start of the route trie. This represents '/'
     * @var RouteNode
     */
    private $root;

    /**
     * Finds the current route by the url passed to it. Traverses the route
     * trie until either a route is found or until it runs out of tries
     *
     * @param string $url The url to find the route for
     * @return RouteLink The found route data
     * @throws \Route\Exception\RouteMatchException Thrown if the route cannot
     * be found in the trie
     */
    public function Find($url) {

    }

    /**
     * Calculates the url based on the route provided. This matches against a
     * list of route names that store the name and the url
     *
     * @param string $routeName The name of the route to match against
     * @param array $options An assoc. array of options with the key being the
     * variable in the url to match and the value being what to replace the
     * variable with
     * @return string The url created
     * @throws \Route\Exception\RouteCreateException Thrown if the route has
     * not replaced all required variables
     * @throws \Route\Exception\RouteGetException Thrown if the route does not
     * exist
     */
    public function Get($routeName, $options) {

    }
}
