<?php

namespace Route\Base;

use Route\RouteLink;

/**
 * Class Route
 * A class that handles taking a given url and finding out where this links to
 * in the code, or takes a route name and returns wh
 *
 * @package Route\Base
 */
class Route {

    /**
     * The start of the route trie. This represents '/'
     * @var RouteNode
     */
    private $root;

    /**
     * An assoc. array of routenames against urls with variables in them that
     * need replacing
     * @var string[]
     */
    private $routeNames;

    /**
     * Finds the current route by the url passed to it. Traverses the route
     * trie until either a route is found or until it runs out of tries
     *
     * @param string $url The url to find the route for
     * @param string $verb The verb to find the route data for
     * @return RouteLink The found route data
     * @throws \Route\Exception\RouteMatchException Thrown if the route cannot
     * be found in the trie
     */
    public function Find($url, $verb) {
        // split the url on '/'

        // set the current node to the root

        // while there is still more url to search and a matching trie nodes

            // if there is more of the url to search on

                // find the next child node and set to current node

            // else check for the verb

                // if it matches store route link and break

        // if there is a valid route link return

        // otherwise throw exception
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
        // match on the route name

        // if no route name exists throw a get exception

        // otherwise retrieve all of the variables from the path

        // loop through the options, update the path, and remove from the variables list

        // if there are variables left over, throw a create exception
    }
}
