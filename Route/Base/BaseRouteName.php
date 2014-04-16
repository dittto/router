<?php
namespace Route\Base;

use Route\RouteLink;

/**
 * Class BaseRouteName
 * A store for route names to store the url and route link together
 *
 * @package Route\Base
 */
abstract class BaseRouteName {
    /**
     * The url for the route name, containing variables in [] tags
     * @var string
     */
    private $url;

    /**
     * The route link, containing where the root should be ending up
     * @var \Route\RouteLink
     */
    private $routeLink;

    /**
     * The constructor
     *
     * @param string $url The url for the route name, containing variables in
     * [] tags
     * @param RouteLink $routeLink The route link, containing where the root
     * should be ending up
     */
    public function __construct($url, RouteLink $routeLink) {
        $this->url = $url;
        $this->routeLink = $routeLink;
    }

    /**
     * Gets the url set during construction
     *
     * @return string
     */
    public function GetUrl() {
        return $this->url;
    }

    /**
     * Gets the route link set during construction
     *
     * @return RouteLink
     */
    public function GetRouteLink() {
        return $this->routeLink;
    }
}
