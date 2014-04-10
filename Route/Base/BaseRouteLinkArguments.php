<?php

namespace Route\Base;

use Route;
use Route\Exception;

/**
 * Class BaseRouteLinkArguments
 * This takes and wrap a route link to provide a simple interface to all
 * arguments, module, and controller
 *
 * @package Route\Base
 */
abstract class BaseRouteLinkArguments {

    /**
     * The route link to wrap
     * @var \Route\RouteLink
     */
    private $routeLink;

    /**
     * An array of arguments to add to the default route link arguments
     * @var array
     */
    private $arguments;

    /**
     * The constructor
     *
     * @param Route\RouteLink $routeLink The route link to add arguments to
     * @param array $arguments An array of arguments to add to the default
     * route link arguments
     */
    public function __construct(Route\RouteLink $routeLink, array $arguments = array()) {
        // store the route link
        $this->routeLink = $routeLink;

        // store the arguments
        $this->arguments = $arguments;
    }

    /**
     * Gets the default arguments from the route link and merges them with the
     * supplied arguments
     *
     * @return array An array of the merged arguments
     */
    public function GetArguments() {
        return $this->arguments + $this->routeLink->GetDefaultArgs();
    }

    /**
     * Gets the module from the route link
     *
     * @return String
     */
    public function GetModule() {
        return $this->routeLink->GetModule();
    }

    /**
     * Gets the controller from the route link
     *
     * @return String
     */
    public function GetController() {
        return $this->routeLink->GetController();
    }
}
