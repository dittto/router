<?php

namespace Route\Base;

use Route;
use Route\Exception;

/**
 * Class RouteBuilder
 * Handles building a Route trie
 *
 * @package Route\Base
 */
class RouteBuilder {

    /**
     * The start of the Route trie
     * @var \Route\RouteNode
     */
    private $root;

    /**
     * An assoc. array of route names against their urls. These are used to
     * calculate internal paths inside the web app
     * @var string[]
     */
    private $routeNames;

    /**
     * The constructor
     *
     * @param Route\RouteNode $root An optional parameter for setting the root
     * of the Route trie to anything other than an empty Route
     */
    public function __construct(Route\RouteNode $root = null) {
        // make sure the root exists
        if ($root === null) {
            $root = new Route\RouteNode();
        }

        // store for later
        $this->root = $root;
    }

    /**
     * Returns the Route root that contains the entire route trie
     *
     * @return Route\RouteNode
     */
    public function GetRouteRoot() {
        return $this->root;
    }

    /**
     * Gets the array of routenames linked to their urls
     *
     * @return string[]
     */
    public function GetRouteNames() {
        return $this->routeNames;
    }

    /**
     * Takes a given path and creates the nodes required to complete the trie
     * for the given path
     *
     * @param string $routeName The name of the route, so this path can be
     * recreated
     * @param string $path The internal path that will be resolved. This should
     * be in the format: articles/(id)/test/article-(other_id)
     * @param array $arguments An array of arguments that contain the args in
     * the path, and the regex that should replace them. For a static route
     * make this an empty array. The args should be in the format:
     * array('id' => '\d+', 'other_id' => '\d+')
     * @param array $verbs The list of verbs that should respond with the
     * RouteLink. This is an array so that it can take multiple verbs for the
     * same RouteLink and should be in the format of: array('get', 'post')
     * @param Route\RouteLink $routeLink A link object that connects the Route
     * to the controller
     */
    public function Add($routeName, $path, array $arguments, array $verbs, Route\RouteLink $routeLink) {
        // set the current route to the root
        $current = $this->root;

        // split the path by /
        $sections = explode('/', $path);

        // loop through the sections
        foreach ($sections as $section) {
            // get the built url and relevant arguments from the section

            // replace the path with the arguments
            $url = $this->BuildUrl($section, $arguments);

            // look for an existing route that matches
            try {
                $node = $current->Find($section);
            } catch (Exception\RouteMatchException $e) {
                // if none exist then add it
                $node = new Route\RouteNode();
                if (empty($arguments)) {
                    $current->AddStatic($section, $node);
                } else {
                    $current->AddDynamic($section, $node, $sectionArguments);
                }

            }

            var_dump($section);


            // if this is the last route then add the verbs and the links

            // else update the current route to be the new / existing route
        }

        // add the route name and path to the routeNames store
    }

    /**
     * Builds a url for the Route from a given user-friendly path
     *
     * @param string $path A path in the format /article/(id)/(somethingElse)
     * @param array $arguments An assoc. array that links the variables in the
     * path (shown above) to the regex's to replace them with. If this is a
     * static path then this should be an empty array
     * @return string The complete path
     * @throws \Route\Exception\RouteAddMissingArgumentsException This is
     * thrown if either a variable used in the path doesn't exist in the
     * arguments array, or the arguments array contains arguments that aren't
     * required in the url
     */
    private function BuildUrl($path, array $arguments) {
        // change this or ditch it so that it works on sections of paths instead


        // get all of the arguments to match
        preg_match_all('#\((.+)\)#U', $path, $matches);

        // if no arguments then drop out
        if (!isset($matches[1]) && empty($arguments)) {
            return $path;
        }

        // replace all of the arguments
        if (isset($matches[1])) {
            foreach ($matches[1] as $variable) {
                // capture any arguments that don't exist
                if (!isset($arguments[$variable])) {
                    throw new Exception\RouteAddMissingArgumentsException('Argument in path `'.$variable.'` could not be matched');
                }

                // replace the argument in the path
                $path = str_replace('('.$variable.')', $arguments[$variable], $path);

                // remove the argument from the list
                unset($arguments[$variable]);
            }
        }

        // if there are still arguments left over, error
        if (sizeof($arguments) > 0) {
            throw new Exception\RouteAddMissingArgumentsException('Surplus arguments not matched in path `'.$path.'`');
        }

        return $path;
    }
}

// create a route writer which reads from a Route root and writes out the php as a cache file

// need to work out how to read that cache file though - read from given location? probably set this during site bootstrap.
