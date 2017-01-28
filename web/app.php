<?php

/*
 * This file is part of the mc-framework project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use McFramework\Component\Router;

$request = Request::createFromGlobals();
$routes = include __DIR__.'/../app/config/routes.php';

$response = (new Router($routes))->resolveController($request);

$response->send();
