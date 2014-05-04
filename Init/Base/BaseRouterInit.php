<?php
namespace Init\Base;

use Route\Base\RouterInterface;
use Route\Router;

/**
 * Class BaseRouterInit
 * Implements the router interface to tie in with the Init functions
 *
 * @package Init\Base
 */
abstract class BaseRouterInit implements RouterInterface {

    /**
     * The router to find urls for
     * @var Router
     */
    protected $router;

    /**
     * The namespace of the controller
     * @var string
     */
    protected $module;

    /**
     * The name of the controller
     * @var string
     */
    protected $controller;

    /**
     * An array of arguments to be passed to the controller
     * @var array
     */
    protected $args = array();

    /**
     * The constructor
     *
     * @param Router $router the router to find urls for
     */
    public function __construct(Router $router) {
        $this->SetRouter($router);
    }

    /**
     * Stores the router for later use
     *
     * @param Router $router The router to find urls for
     */
    public function SetRouter(Router $router) {
        $this->router = $router;
    }

    /**
     * Stores the uri and calculates the controller to access
     *
     * @param string $uri The uri of the page
     * @param string $verb The http verb to use
     * @return void
     */
    public function FindRoute($uri, $verb = 'get') {
        $found = $this->router->Find($uri, $verb);
        $this->module = $found->GetModule();
        $this->controller = $found->GetController();
        $this->args = $found->GetArguments();
    }

    /**
     * Gets a uri from a route name and options
     *
     * @param string $name The name of the route to retrieve
     * @param string[] $options The options to use to build the uri
     * @return string
     */
    public function Get($name, array $options = array()) {
        return $this->router->Get($name, $options);
    }

    /**
     * The namespace of the controller to use
     *
     * @return string
     */
    public function GetModule() {
        return $this->module;
    }

    /**
     * The name of the controller
     *
     * @return string
     */
    public function GetController() {
        return $this->controller;
    }

    /**
     * An array of the arguments generated by this route
     *
     * @return mixed[]
     */
    public function GetArguments() {
        return $this->args;
    }
}