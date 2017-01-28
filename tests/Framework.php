<?php

/*
 * This file is part of the mc-framework project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests;

use McFramework\Framework;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * Class FrameworkTest
 *
 * @author Michael COULLERET <michael@coulleret.pro>
 */
class FrameworkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testNotFoundHandling()
    {
        $framework = $this->getFrameworkForException(new ResourceNotFoundException());

        $response = $framework->handle(new Request());

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testErrorHandling()
    {
        $framework = $this->getFrameworkForException(new \RuntimeException());

        $response = $framework->handle(new Request());

        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testControllerResponse()
    {
        $matcher = $this->getMockBuilder(UrlMatcherInterface::class)->getMock();
        $matcher
            ->expects($this->once())
            ->method('match')
            ->will($this->returnValue(array(
                '_route' => '/',
                '_controller' => function () {
                    return new Response('Hello ');
                },
            )))
        ;

        $matcher
            ->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($this->getMockBuilder(RequestContext::class)->getMock()))
        ;

        $controllerResolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver();

        $framework = new Framework($matcher, $controllerResolver, $argumentResolver);

        $response = $framework->handle(new Request());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Hello', $response->getContent());
    }

    /**
     * @param $exception
     *
     * @return Framework
     */
    private function getFrameworkForException($exception)
    {
        $matcher = $this->getMockBuilder(UrlMatcherInterface::class)->getMock();

        $matcher
            ->expects($this->once())
            ->method('match')
            ->will($this->throwException($exception))
        ;

        $matcher
            ->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($this->getMockBuilder(RequestContext::class)->getMock()))
        ;

        $controllerResolver = $this->getMockBuilder(ControllerResolverInterface::class)->getMock();
        $argumentResolver = $this->getMockBuilder(ArgumentResolverInterface::class)->getMock();

        return new Framework($matcher, $controllerResolver, $argumentResolver);
    }
}
