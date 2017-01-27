<?php

/*
 * This file is part of the mc-framework project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$request = Request::createFromGlobals();

try {
    $match = (new \McFramework\Component\Routing()->match($request));
    extract($match, EXTR_SKIP);
    ob_start();
    include sprintf(__DIR__.'/../src/Controller/%s.php', $_route);

    $response = new Response(ob_get_clean());
} catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
    $response = new Response('Not Found', 404);
} catch (Exception $e) {
    $response = new Response('An error occurred', 500);
}

$response->send();
