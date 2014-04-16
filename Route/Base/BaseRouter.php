<?php

namespace Route\Base;

use Route;

/**
 * Class BaseRouter
 * A class that handles taking a given url and finding out where this links to
 * in the code, or takes a route name and returns wh
 *
 * @package Route\Base
 */
abstract class BaseRouter {

    /**
     * The start of the route trie. This represents '/'
     * @var \Route\RouteNode
     */
    private $root;

    /**
     * An assoc. array of route names against urls with variables in them that
     * need replacing and route links
     * @var Route\RouteName[]
     */
    private $routeNames;

    /**
     * The constructor
     *
     * @param Route\RouteNode $root The start of the route trie
     * @param Route\RouteName[] $routeNames An assoc. array of route names
     * against urls with variables in them that need replacing
     */
    public function __construct(Route\RouteNode $root, array $routeNames) {
        $this->root = $root;
        $this->routeNames = $routeNames;
    }

    /**
     * Finds the current route by the url passed to it. Traverses the route
     * trie until either a route is found or until it runs out of tries
     *
     * @param string $url The url to find the route for
     * @param string $verb The verb to find the route data for
     * @return Route\RouteLinkArguments The found route data together with the arguments
     * @throws Route\Exception\RouteMatchException Thrown if the route cannot
     * be found in the trie
     */
    public function Find($url, $verb) {
        // init vars
        $link = null;
        $args = array();

        // split the url on '/'
        $sections = explode('/', $url);
        $sectionPos = 0;
        $sectionsLength = sizeof($sections);

        // set the current node to the root
        $current = $this->root;

        // while there is still more url to search and a matching trie nodes
        while ($current !== null) {
            // if there is more of the url to search on
            if ($sectionPos < $sectionsLength) {

                // skip this level if there's no entry because there's either
                // a / at the start or there is // or an / at the end
                if ($sections[$sectionPos] === '') {
                    $sectionPos ++;
                    continue;
                }

                // find the next child node and set to current node
                $currentContainer = $current->Find($sections[$sectionPos]);
                if ($currentContainer instanceof Route\RouteNodeMatches) {
                    $current = $currentContainer->GetNode();
                    $args += $currentContainer->GetMatches();
                } else {
                    $current = $currentContainer;
                }
                $sectionPos ++;
            }
            // else check for the verb
            else {
                // if it matches store route link and break
                $link = $current->FindVerb($verb);
                break;
            }
        }

        // if there is a valid route link return
        if ($link !== null) {
            return new Route\RouteLinkArguments($link, $args);
        }
        // otherwise throw exception
        else {
            throw new Route\Exception\RouteMatchException('The `'.$url.'` could not match a route');
        }
    }

    /**
     * Calculates the url based on the route provided. This matches against a
     * list of route names that store the name and the url
     *
     * @param string $routeName The name of the route to match against
     * @param array $options An assoc. array of options with the key being the
     * variable in the url to match and the value being what to replace the
     * variable with
     * @param bool $startWithSlash Set this to false to not start the url with
     * a /
     * @return string The url created
     * @throws Route\Exception\RouteCreateException Thrown if the route has
     * not replaced all required variables
     * @throws Route\Exception\RouteGetException Thrown if the route does not
     * exist
     */
    public function Get($routeName, array $options = array(), $startWithSlash = true) {
        // match on the route name
        $route = isset($this->routeNames[$routeName]) ? $this->routeNames[$routeName] : null;

        // if no route name exists throw a get exception
        if ($route === null) {
            throw new Route\Exception\RouteGetException('Cannot match `'.$routeName.'` route');
        }

        // otherwise retrieve all of the variables from the path
        $url = $route->GetUrl();
        preg_match_all('/\[([a-zA-Z0-9\-\_]+)\]/Usi', $url, $matches);
        $variables = isset($matches[1]) ? $matches[1] : array();
        $variables = array_combine($variables, array_pad(array(), sizeof($variables), true));

        // loop through the options, update the path, and remove from the variables list
        $mergedOptions = array_merge($route->GetRouteLink()->GetDefaultArgs(), $options);
        foreach ($mergedOptions as $option => $value) {
            $url = str_replace('['.$option.']', $value, $url);
            if (isset($variables[$option])) {
                unset($variables[$option]);
            }
        }

        // if there are variables left over, throw a create exception
        if (sizeof($variables) > 0) {
            throw new Route\Exception\RouteCreateException('The route `'.$routeName.'` needs the variable(s) `'.implode('`, `', array_keys($variables)).'`');
        }

        return ($startWithSlash ? '/' : '').$url;
    }
}
