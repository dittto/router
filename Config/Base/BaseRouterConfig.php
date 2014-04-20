<?php
namespace Config\Base;

use Route\RouteBuilder;
use Route\Router;
use Route\RouteWriter;

/**
 * Class BaseRouterConfig
 * Inits the router from the cache file
 *
 * @package Config\Base
 */
abstract class BaseRouterConfig {

    /**
     * The path to look for initing the router
     * @var string
     */
    protected $path;

    /**
     * The name of the routes cache file
     * @var string
     */
    protected $cacheFile;

    /**
     * The constructor
     *
     * @param string $path The path to the cache file
     * @param string $cacheFile The name of the routes cache file
     */
    public function __construct($path, $cacheFile = 'routes.php') {
        $this->path = $path;
        $this->cacheFile = $cacheFile;
    }

    /**
     * Gets an instance of the router with the routes
     *
     * @param bool $failedAttempt A simple flag that's used for the second
     * attempt of getting the router cache. If this is true and the cache file
     * cannot be retrieved then this method will throw an exception
     * @return Router The new router class
     * @throws \Exception Thrown if the routing cache file could not be loaded
     * or was empty
     */
    public function GetRouter($failedAttempt = false) {
        // if no file exists
        if (!is_file($this->path.$this->cacheFile)) {
            if (!$failedAttempt) {
                $this->SaveCacheFile();
                return $this->GetRouter(true);
            } else {
                throw new \Exception('Routing file could not be loaded: '.$this->path.$this->cacheFile);
            }
        }

        // include cache file
        $rootNode = $routes = null;
        include_once $this->path.$this->cacheFile;

        // throw an error if the cache file is empty
        if ($rootNode === null || $routes === null) {
            throw new \Exception('The routing file was empty: '.$this->path.$this->cacheFile);
        }

        return new Router($rootNode, $routes);
    }

    /**
     * This saves the cache file of the routes found so far
     */
    protected function SaveCacheFile() {
        // get the built route trie
        $routes = $this->DefineRoutes();

        // write the routes to the cache file
        $writer = new RouteWriter($routes->GetRouteRoot(), $routes->GetRouteNames());
        $writer->Write($this->path.$this->cacheFile);
    }

    /**
     * Override this to define the routes
     *
     * @return RouteBuilder
     */
    protected function DefineRoutes() {
        // init the route links

        // init the routes
        $builder = new RouteBuilder();

        return $builder;
    }
}