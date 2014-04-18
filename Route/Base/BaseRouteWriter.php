<?php
namespace Route\Base;

use Route\RouteLink;
use Route\RouteName;
use Route\RouteNode;

/**
 * Class BaseRouteWriter
 * Takes a route root and writes out how to build this root from scratch using
 * the base components
 *
 * @package Route\Base
 */
abstract class BaseRouteWriter {

    /**
     * An array of deconstructed route nodes
     * @var mixed[]
     */
    private $nodes;

    /**
     * An array of deconstructed route links
     * @var mixed[]
     */
    private $links;

    /**
     * An array of deconstructed route names
     * @var mixed[]
     */
    private $names;

    /**
     * An assoc. array of route names against urls with variables in them that
     * need replacing
     * @var RouteName[][]
     */
    private $routeNames;

    /**
     * The root route node to write out
     * @var RouteNode
     */
    private $root;

    /**
     * The constructor
     *
     * @param RouteNode $root The root route node to write out
     * @param Route\RouteName[][] $routeNames An assoc. array of route names
     * against urls with variables in them that need replacing
     */
    public function __construct(RouteNode $root, array $routeNames) {
        $this->SetRootNode($root);
        $this->SetRouteNames($routeNames);
    }

    /**
     * Stores the root node for later recall
     *
     * @param RouteNode $root The root route node to write out
     */
    public function SetRootNode(RouteNode $root) {
        $this->root = $root;
    }

    /**
     * Stores the route names for later recall
     *
     * @param Route\RouteName[][] $routeNames An assoc. array of route names
     * against urls with variables in them that need replacing
     */
    public function SetRouteNames(array $routeNames) {
        $this->routeNames = $routeNames;
    }

    /**
     * Explodes the root route node and the route names to generate PHP so
     * that we can recreate them
     *
     * @return array The PHP lines in an array
     */
    public function OutputCode() {
        // reset the data store
        $this->nodes = array();
        $this->links = array();

        // get the root id after splitting the data into arrays and strings
        $rootId = $this->BuildNodeData($this->root);

        // get the code for the nodes
        $nodeCode = $this->WriteNodeData($rootId);

        // split the name routing data into strings and arrays
        $this->BuildNameData();

        // get the code for the route names
        $nameCode = $this->WriteNameData();

        return array_merge($nodeCode, $nameCode);
    }

    /**
     * Explode the root route node to so we can save how to recreate the route
     * trie to a file
     *
     * @param string $filename The filename to save the file to
     * @param string[] $code An array of the code to write out
     */
    public function Write($filename, array $code = array()) {
        // get the code
        if (is_empty($code)) {
            $code = $this->OutputCode();
        }

        // init the file
        $handle = fopen($filename, 'w+');

        // save the data
        foreach ($code as $line) {
            fwrite($handle, $line."\n\r");
        }

        // close the handle to the file
        fclose($handle);
    }

    /**
     * Handles the creation of the php for the nodes and links
     *
     * @param int $rootId The id of the root route node
     * @return array An array of PHP to recreate the links and nodes
     */
    private function WriteNodeData($rootId) {
        // loop through the links and write them out
        $output = array();
        foreach ($this->links as $key => $link) {
            $args = array();
            foreach ($link['args'] as $argKey => $argValue) {
                $args[] = '"'.$argKey.'" => "'.$argValue.'"';
            }
            $output[] = '$link'.$key.' = new \Route\RouteLink("'.$link['module'].'", "'.$link['controller'].'", array('.implode(', ', $args).'));';
        }

        // loop through the nodes and write them out
        foreach ($this->nodes as $key => $node) {
            // init the node
            $output[] = '$node'.$key.' = new Route\RouteNode();';

            // add the static links
            foreach ($node['statics'] as $static) {
                $output[] = '$node'.$key.'->AddStatic("'.$static['name'].'", $node'.$static['id'].');';
            }

            // add the dynamic links
            foreach ($node['dynamics'] as $dynamic) {
                $args = array();
                foreach ($dynamic['args'] as $argKey => $argValue) {
                    $args[] = '"'.$argKey.'" => "'.$argValue.'"';
                }
                $output[] = '$node'.$key.'->AddDynamic("'.$dynamic['name'].'", $node'.$dynamic['id'].', array('.implode(', ', $args).'));';
            }

            // add the verbs
            foreach ($node['verbs'] as $verb) {
                $output[] = '$node'.$key.'->AddVerb("'.$verb['name'].'", $link'.$verb['linkId'].');';
            };
        }

        // store the root id
        $output[] = '$rootNode = $node'.$rootId.';';

        return $output;
    }

    /**
     * Builds the PHP required to rebuild the route name array
     *
     * @return array The PHP to generate the route name array
     */
    private function WriteNameData() {
        // loop through the names to retrieve the urls and route links for each
        $output = array('$routes = array();');
        foreach ($this->names as $name => $routes) {
            // make sure the name exists in the output
            $output[] = '$routes["'.$name.'"] = array();';

            // add each route to the output
            foreach ($routes as $route) {
                $output[] = '$routes["'.$name.'"][] = new Route\RouteName("'.$route['url'].'", $link'.$route['linkId'].');';
            }
        }

        return $output;
    }

    /**
     * Deconstructs a given route node and saves the data neatly in the
     * centralised arrays, complete with replacing object links with ids
     * inside the array. This makes it easy to write out to a file
     *
     * @param RouteNode $node The node to explode
     * @return int The id of the node in the $nodes store
     */
    private function BuildNodeData(RouteNode $node) {
        // init vars
        $statics = $dynamics = $verbs = array();

        // recursively loop through the static nodes and get their ids and names
        $staticNodes = $node->GetStatics();
        foreach ($staticNodes as $name => $static) {
            $nodeId = $this->BuildNodeData($static);
            $statics[] = array('id' => $nodeId, 'name' => $name);
        }

        // recursively loop through the dynamic nodes and get their ids, names, and section arguments
        $dynamicNodes = $node->GetDynamics();
        foreach ($dynamicNodes as $name => $dynamic) {
            $nodeId = $this->BuildNodeData($dynamic->GetNode());
            $dynamics[] = array('id' => $nodeId, 'name' => $name, 'args' => $dynamic->GetArguments());
        }

        // add the verbs - these need a name and route link
        $verbList = $node->GetVerbs();
        foreach ($verbList as $name => $link) {
            $verbs[] = array('name' => $name, 'linkId' => $this->GetLinkId($link));
        }

        // add the current node to the store and get the node id
        $this->nodes[] = array('statics' => $statics, 'dynamics' => $dynamics, 'verbs' => $verbs);
        $nodeId = sizeof($this->nodes) - 1;

        return $nodeId;
    }

    /**
     * Takes a route link, mines it for data, and the stores in a centralised
     * array for later, using a concatenation of the module, controller and
     * json-encoded arguments as the id so we can match duplicates easily
     *
     * @param RouteLink $link The link to store the data from
     * @return string The id of the link in the store
     */
    private function GetLinkId(RouteLink $link) {
        // init vars
        $module = $link->GetModule();
        $controller = $link->GetController();
        $args = $link->GetDefaultArgs();

        // build a simple id
        $id = md5($module.$controller.json_encode($args));

        // if this doesn't exist in the store, store it
        if (!isset($this->links[$id])) {
            $this->links[$id] = array('module' => $module, 'controller' => $controller, 'args' => $args);
        }

        return $id;
    }

    /**
     * This builds the route names array as a simple array of strings. This
     * requires BuildNodeData() to have been run first as this relies on the
     * $links built by it
     */
    private function BuildNameData() {
        // loop through for the route names
        foreach ($this->routeNames as $name => $routes) {
            // make sure the name exists in the store
            if (!isset($this->names[$name])) {
                $this->names[$name] = array();
            }

            // loop through the routes and add to the store
            foreach ($routes as $route) {
                $this->names[$name][] = array('url' => $route->GetUrl(), 'linkId' => $this->GetLinkId($route->GetRouteLink()));
            }
        }
    }
}