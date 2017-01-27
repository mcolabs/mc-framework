<?php

/*
 * This file is part of the mc-framework project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace McFramework\Component;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class Routing
 *
 * @author Michael COULLERET <michael@coulleret.pro>
 */
class Routing
{
    /**
     * @var RouteCollection
     */
    protected $routeCollection;

    /**
     * @var RequestContext
     */
    protected $context;

    /**
     * constructor Routing
     */
    public function __construct()
    {
        $this->routeCollection = new RouteCollection();
        $this->context = new RequestContext();
    }

    /**
     * Load routes
     */
    public function load()
    {
        $this->routeCollection
            ->add('home', new Route('/'))
        ;
    }

    /**
     * Match route
     *
     * @param Request $request
     *
     * @return array
     */
    public function match(Request $request)
    {
        $this->context->fromRequest($request);

        $matcher = new UrlMatcher($this->routeCollection, $this->context);

        return $matcher->match($request->getPathInfo());
    }
}
