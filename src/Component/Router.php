<?php

/*
 * This file is part of the mc-framework project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace McFramework\Component;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class Router
 *
 * @author Michael COULLERET <michael@coulleret.pro>
 */
class Router
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
     * @var ControllerResolver
     */
    protected $controllerResolver;

    /**
     * @var ArgumentResolver
     */
    protected $argumentResolver;

    /**
     * constructor Routing
     *
     * @param RouteCollection $routeCollection
     */
    public function __construct(RouteCollection $routeCollection)
    {
        $this->routeCollection = $routeCollection;
        $this->context = new RequestContext();
        $this->controllerResolver = new ControllerResolver();
        $this->argumentResolver = new ArgumentResolver();
    }

    /**
     * Resolve route and return controller
     *
     * @param Request $request
     *
     * @return Response
     */
    public function resolveController(Request $request)
    {
        $this->context->fromRequest($request);
        $matcher = new UrlMatcher($this->routeCollection, $this->context);

        try {
            $request->attributes->add($matcher->match($request->getPathInfo()));

            $controller = $this->controllerResolver->getController($request);
            $arguments = $this->argumentResolver->getArguments($request, $controller);

            return call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $exception) {
            return new Response('Not found', 404);
        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
            return new Response('An error occurred', 500);
        }
    }
}
