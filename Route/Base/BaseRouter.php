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
        $routes = isset($this->routeNames[$routeName]) ? $this->routeNames[$routeName] : null;

        // if no route name exists throw a get exception
        if ($routes === null) {
            throw new Route\Exception\RouteGetException('Cannot match `'.$routeName.'` route');
        }

        // find a route that matches those options supplied
        $foundUrl = null;
        foreach ($routes as $route) {
            // get the variables from the route
            $url = $route->GetUrl();
            $variables = $this->GetRouteVariables($url);

            // get the url by replacing the variables with values
            $matchedUrl = $this->MatchVariables($url, $variables, $options);
            if ($matchedUrl !== null) {
                $foundUrl = $matchedUrl;
                break;
            }
        }

        // if no route can be found, search for a route that matches the
        // options and the default options
        if ($foundUrl === null) {
            foreach ($routes as $route) {
                // get the variables from the route
                $url = $route->GetUrl();
                $variables = $this->GetRouteVariables($url);

                // get the url by replacing the variables with values
                $matchedUrl = $this->MatchVariables($url, $variables, array_merge($route->GetRouteLink()->GetDefaultArgs(), $options));
                if ($matchedUrl !== null) {
                    $foundUrl = $matchedUrl;
                    break;
                }
            }
        }

        // if no url was matched then throw an exception
        if ($foundUrl === null) {
            throw new Route\Exception\RouteCreateException('The route `'.$routeName.'` could not be matched with the supplied variables `'.implode('`, `', array_keys($options)).'`');
        }

        return ($startWithSlash ? '/' : '').$foundUrl;
    }

    /**
     * Gets all the variables in a path and adds them to the keys of an array
     * to make them easier to use
     *
     * @param string $url The url to get the variables out of, such as
     * article-[articleId]
     * @return array The found variables
     */
    private function GetRouteVariables($url) {
        // retrieve all the variables from the path
        preg_match_all('/\[([a-zA-Z0-9\-\_]+)\]/Usi', $url, $matches);
        $variables = isset($matches[1]) ? $matches[1] : array();
        if (sizeof($variables) > 0) {
            $variables = array_combine($variables, array_pad(array(), sizeof($variables), true));
        }

        return $variables;
    }

    /**
     * Loops through the variables and tries to use the options to complete the
     * url. If the url is completed (by all variables being matched) then it
     * will be returned, otherwise null will be
     *
     * @param string $url The url to replace the variables in
     * @param array $variables The variables to attempt to replace all of
     * @param array $options The options to try and replace all variables with
     * @return string|null The completed url or null on failure
     */
    private function MatchVariables($url, array $variables, array $options) {
        // init vars
        $optionsList = $options;

        // loop through the options to replace the variables in the url
        foreach ($options as $option => $value) {
            $url = str_replace('['.$option.']', $value, $url);
            if (isset($variables[$option])) {
                unset($variables[$option]);
                unset($optionsList[$option]);
            }
        }

        // if there are variables or options left over return null as it failed
        if (sizeof($variables) > 0 || sizeof($optionsList) > 0) {
            return null;
        }

        return $url;
    }
}
