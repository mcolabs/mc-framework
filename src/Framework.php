<?php

/*
 * This file is part of the mc-framework project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace McFramework;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Class Framework
 *
 * @author Michael COULLERET <michael@coulleret.pro>
 */
class Framework
{
    /**
     * @var UrlMatcherInterface
     */
    protected $urlMatcher;

    /**
     * @var ControllerResolverInterface
     */
    protected $controllerResolver;

    /**
     * @var ArgumentResolverInterface
     */
    protected $argumentResolver;

    /**
     * constructor Framework
     *
     * @param UrlMatcherInterface         $urlMatcher
     * @param ControllerResolverInterface $controllerResolver
     * @param ArgumentResolverInterface   $argumentResolver
     */
    public function __construct(UrlMatcherInterface $urlMatcher, ControllerResolverInterface $controllerResolver, ArgumentResolverInterface $argumentResolver)
    {
        $this->urlMatcher = $urlMatcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
    }

    /**
     * @param Request $request
     * @return Response|mixed
     */
    public function handle(Request $request)
    {
        $this->urlMatcher->getContext()->fromRequest($request);

        try {
            $request->attributes->add($this->urlMatcher->match($request->getPathInfo()));

            $controller = $this->controllerResolver->getController($request);
            $arguments = $this->argumentResolver->getArguments($request, $controller);

            return call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $e) {
            return new Response('Not Found', 404);
        } catch (\Exception $e) {
            return new Response('An error occurred', 500);
        }
    }
}
