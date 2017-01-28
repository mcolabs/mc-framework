<?php

/*
 * This file is part of the mc-framework project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;

use McFramework\Framework;

$request = Request::createFromGlobals();
$routes = include __DIR__.'/../app/config/routes.php';
$urlMatcher = new UrlMatcher($routes, new RequestContext());

$response = (new Framework($urlMatcher, new ControllerResolver(), new ArgumentResolver()))->handle($request);

$response->send();
