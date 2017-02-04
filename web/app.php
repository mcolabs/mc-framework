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
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\Store;
use \Symfony\Component\HttpKernel\HttpCache\Esi;
use McFramework\EventListener\HttpCacheListener;

use McFramework\Framework;

$request = Request::createFromGlobals();
$routes = include __DIR__.'/../app/config/routes.php';
$urlMatcher = new UrlMatcher($routes, new RequestContext());

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new HttpCacheListener());

$framework = new Framework($dispatcher, $urlMatcher, new ControllerResolver(), new ArgumentResolver());

$framework = new HttpCache(
    $framework,
    new Store(__DIR__.'/../var/cache/'),
    new Esi(),
    array('debug' => $framework->isDebug())
);

$framework->handle($request)->send();
