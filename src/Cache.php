<?php

/*
 * This file is part of the mc-framework project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace McFramework;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpCache\Esi;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cache provides HTTP caching.
 *
 * @author Michael COULLERET <michael@coulleret.pro>
 */
class Cache
{
    /**
     * @var HttpCache
     */
    protected $httpCache;

    /**
     * constructor Cache
     *
     * @param Kernel      $kernel
     * @param string|null $cacheDir
     */
    public function __construct(Kernel $kernel, $cacheDir = null)
    {
        if (null === $cacheDir) {
            $cacheDir = __DIR__.'/../var/cache/';
        }

        $this->httpCache = new HttpCache(
            $kernel,
            new Store($cacheDir),
            new Esi(),
            array('debug' => $kernel->isDebug())
        );
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        return $this->httpCache->handle($request);
    }
}
