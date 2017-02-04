<?php

/*
 * This file is part of the mc-framework project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace McFramework;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Class Framework
 *
 * @author Michael COULLERET <michael@coulleret.pro>
 */
class Framework implements HttpKernelInterface
{
    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

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
     * @var string
     */
    protected $environment;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * constructor Framework
     *
     * @param EventDispatcher             $eventDispatcher
     * @param UrlMatcherInterface         $urlMatcher
     * @param ControllerResolverInterface $controllerResolver
     * @param ArgumentResolverInterface   $argumentResolver
     * @param string                      $environment
     * @param boolean                     $debug
     */
    public function __construct(
        EventDispatcher $eventDispatcher,
        UrlMatcherInterface $urlMatcher,
        ControllerResolverInterface $controllerResolver,
        ArgumentResolverInterface $argumentResolver,
        $environment = 'prod',
        $debug = false
    ) {
        $this->dispatcher = $eventDispatcher;
        $this->urlMatcher = $urlMatcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->urlMatcher->getContext()->fromRequest($request);

        try {
            $request->attributes->add($this->urlMatcher->match($request->getPathInfo()));

            $controller = $this->controllerResolver->getController($request);
            $arguments = $this->argumentResolver->getArguments($request, $controller);

            $response = call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $e) {
            $response = new Response('Not Found', 404);
        } catch (\Exception $e) {
            $response = new Response('An error occurred', 500);
        }

        $this->dispatcher->dispatch(KernelEvents::RESPONSE, new FilterResponseEvent($this, $request, $type, $response));

        return $response;
    }

    /**
     * Get environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * is Debug
     *
     * @return boolean
     */
    public function isDebug()
    {
        return $this->debug;
    }
}
