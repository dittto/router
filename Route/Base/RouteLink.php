<?php

namespace Route\Base;

use Route\Exception;

/**
 * Class RouteLink
 * This stores information about a given route. These are the endpoints in the
 * RouteNode trie structure
 *
 * @package Route\Base
 */
class RouteLink {

    /**
     * The name of the module to use for this route
     * @var String
     */
    private $module;

    /**
     * The name of the controller to use for this route
     * @var String
     */
    private $controller;

    /**
     * An assoc. array of the default arguments for the route. These are passed
     * to the controller if not overridden by the Route::Find() method
     * @var array
     */
    private $defaultArgs = array();

    /**
     * The constructor
     *
     * @param String $module The name of the module to use for this route
     * @param String $controller The name of the controller to use for this route
     * @param array $defaultArgs The default args to use for this route
     */
    public function __construct($module = '', $controller = '', array $defaultArgs = array()) {
        if ($module !== '') {
            $this->SetModule($module);
        }
        if ($controller !== '') {
            $this->SetController($controller);
        }
        if (is_array($defaultArgs)) {
            $this->SetDefaultArgs($defaultArgs);
        }
    }

    /**
     * Sets the module name for this route
     *
     * @param String $module The name of the module to use
     * @throws Exception\RouteLinkSetException Thrown if the module name isn't a string
     */
    public function SetModule($module) {
        if (is_string($module)) {
            $this->module = $module;
        } else {
            throw new Exception\RouteLinkSetException('Module name `'.print_r($module, true).'` is not a string');
        }
    }

    /**
     * Sets the controller name for this route
     *
     * @param String $controller The name of the controller to use
     * @throws Exception\RouteLinkSetException Thrown if the controller name isn't a string
     */
    public function SetController($controller) {
        if (is_string($controller)) {
            $this->controller = $controller;
        } else {
            throw new Exception\RouteLinkSetException('Controller name `'.print_r($controller, true).'` is not a string');
        }
    }

    /**
     * Sets the default args to send to the controller
     *
     * @param array $args An assoc. array of the default args for this route
     */
    public function SetDefaultArgs(array $args) {
        $this->defaultArgs = $args;
    }

    /**
     * Returns the module name for this route
     *
     * @return String
     */
    public function GetModule() {
        return $this->module;
    }

    /**
     * Returns the controller name for this route
     *
     * @return String
     */
    public function GetController() {
        return $this->controller;
    }

    /**
     * Returns the default args for this route
     *
     * @return array
     */
    public function GetDefaultArgs() {
        return $this->defaultArgs;
    }
}
