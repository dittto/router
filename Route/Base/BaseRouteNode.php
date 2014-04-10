<?php

namespace Route\Base;

use Route\Exception;
use Route;

/**
 * Class BaseRouteNode
 * A trie node. These link to other RouteNodes if there are deeper branches of
 * possible routes to follow, and/or link to RouteLinks if there are endpoints
 * available.
 *
 * The Route class traverses through these nodes looking for one that match
 * parts a url. If it can match all nodes then it looks at the final node for
 * a matching verb. If that verb matches then the RouteNode will return a
 * RouteLink that points to a controller
 *
 * @package Route\Base
 */
abstract class BaseRouteNode {
    /**
     * An assoc. array of all static routeNodes belonging to this node and the
     * name to match them on
     * @var Route\RouteNode[]
     */
    private $statics = array();

    /**
     * An assoc. array of all dynamic routeNodes attached to this node and the
     * regex to match them on
     * @var Route\RouteNodeArguments[]
     */
    private $dynamics = array();

    /**
     * An assoc. array of HTTP verbs (such as GET, POST, PUT, etc) attached to
     * a RouteLink() that will link the routes to controllers
     * @var Route\RouteLink[]
     */
    private $verbs = array();

    /**
     * Checks both the static and dynamic stores for a section of a url path,
     * e.g. if the url is /articles/show/12 then this takes either 'article',
     * 'show', or '12'
     *
     * @param string $path The section of the url the node is meant to match
     * against
     * @param bool $alwaysStaticMatch Set this to try so that dynamic matches
     * don't preg match but instead static match. Use this mode for building
     * the tries
     * @return Route\RouteNode|Route\RouteNodeMatches A child node if the url
     * path section is matched, or a route node matches container if the route
     * was dynamic
     * @throws Exception\RouteMatchException If no node is matched
     */
    public function Find($path, $alwaysStaticMatch = false) {
        // try and match it statically
        try {
            return $this->FindStatic($path);
        } catch (Exception\RouteMatchException $e) {
        }

        // try and match it dynamically
        try {
            return $this->FindDynamic($path, $alwaysStaticMatch);
        } catch (Exception\RouteMatchException $e) {
        }

        // throw an exception if not matched
        throw new Exception\RouteMatchException('The path `'.$path.'` could not be matched');
    }

    /**
     * Checks for static paths in this node. Static paths are those that don't
     * require regex to match the route name - good for site sections or static
     * pages
     *
     * @param string $path The section of the url the node is meant to match
     * against
     * @return Route\RouteNode A child node if the url path section is matched
     * @throws Exception\RouteMatchException If no node is matched
     */
    public function FindStatic($path) {
        if (isset($this->statics[$path])) {
            return $this->statics[$path];
        } else {
            throw new Exception\RouteMatchException('The path `'.$path.'` could not be matched');
        }
    }

    /**
     * Checks for dynamic paths in this node. Dynamic paths are those that
     * require regex to match the route name - good for any part of the route
     * that is controlled by a database, such as article id's or subsections
     *
     * @param string $path The section of the url the node is meant to match
     * against
     * @param bool $alwaysStaticMatch Set this to try so that dynamic matches
     * don't preg match but instead static match. Use this mode for building
     * the tries
     * @return Route\RouteNodeMatches A child node if the url path section is matched
     * @throws Exception\RouteMatchException If no node is matched
     */
    public function FindDynamic($path, $alwaysStaticMatch = false) {
        // init vars
        $result = null;
        
        // loop through the dynamics and find a match
        foreach ($this->dynamics as $regex => $nodeArgs) {
            if (($alwaysStaticMatch && $regex === $path) || (!$alwaysStaticMatch && preg_match('/^'.$regex.'$/Ui', $path, $matches))) {
                
                // loop through the arguments and store matches. There is a +1
                // here so we can offset the args array by 1 and start it on 0
                // whereas the matches start on 1
                $found = array();
                $args = $nodeArgs->GetArguments();
                foreach ($args as $key => $arg) {
                    if (!$alwaysStaticMatch && isset($matches[$key + 1]) && $arg != '') {
                        $found[$arg] = $matches[$key + 1];
                    }
                }
                
                // build the RouteNodeMatches response
                $node = new Route\RouteNodeMatches();
                $node->SetNode($nodeArgs->GetNode());
                $node->SetMatches($found);
                $result = $node;

                // break out of the loop as our goal is complete
                break;
            }
        }

        // throw an exception if result is still null
        if ($result === null) {
            throw new Exception\RouteMatchException('The path `'.$path.'` could not be matched');
        }

        return $result;
    }

    /**
     * Checks against verbs stored against the node. These can be any string
     * but it is likely they will be either 'get', 'post', 'put', or 'delete'.
     * If a verb is found then this will return a RouteLink that will link
     * to a controller
     *
     * @param string $verb The verb to match against
     * @return Route\RouteLink The object containing a link the controller the route
     * points to
     * @throws Exception\RouteMatchVerbException If no node is matched
     */
    public function FindVerb($verb) {
        if (isset($this->verbs[strtolower($verb)])) {
            return $this->verbs[strtolower($verb)];
        } else {
            throw new Exception\RouteMatchVerbException('The verb `'.$verb.'` could not be matched');
        }
    }

    /**
     * Adds a new static node to the store in this object. This will be a
     * child of this node in the trie
     *
     * @param string $path The section of the url the node is meant to match
     * against
     * @param Route\RouteNode $node The child node to return if this path is
     * matched
     * @throws Exception\RouteCreateException Thrown if the path is empty or
     * not a string
     */
    public function AddStatic($path, Route\RouteNode $node) {
        // check path is string and not empty
        if (is_string($path) && $path !== '') {
            $this->statics[$path] = $node;
        } else {
            throw new Exception\RouteCreateException('A static path cannot be empty and must be a string');
        }
    }

    /**
     * Adds a new dynamic node to the store in this object. This will be a
     * child of this node in the trie. The regex should not contain a forward
     * slash as these are used to split the route urls into sections. This
     * isn't enforced though for speed reasons
     *
     * @param string $regex The regex that is meant to match a section of
     * url
     * @param Route\RouteNode $node The child node to return if the regex can
     * match a path
     * @param string[] $args The arguments to store with a dynamic route node
     * @throws Exception\RouteCreateException Thrown if the path is empty or
     * not a string
     */
    public function AddDynamic($regex, Route\RouteNode $node, array $args = array()) {
        // check regex is string and is not empty
        if (is_string($regex) && $regex !== '') {
            $routeNode = new Route\RouteNodeArguments($node, $args);
            $this->dynamics[$regex] = $routeNode;
        } else {
            throw new Exception\RouteCreateException('A dynamic path cannot be empty and must be a string');
        }
    }

    /**
     * Adds a verb to the verb store in this object. This are used when this
     * is the final node in the trie and we now need to match which controller
     * to route to
     *
     * @param string $verb The verb to match against. All verbs get made
     * lowercase to make them easier to match
     * @param Route\RouteLink $link The object containing a link the controller
     * the route points to
     * @throws Exception\RouteCreateVerbException Thrown if the verb is empty or
     * not a string
     */
    public function AddVerb($verb, Route\RouteLink $link) {
        // check verb is string and is not empty
        if (is_string($verb) && $verb !== '') {
            $this->verbs[strtolower($verb)] = $link;
        } else {
            throw new Exception\RouteCreateVerbException('A verb cannot be empty and must be a string');
        }
    }
}
