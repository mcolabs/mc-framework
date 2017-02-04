<?php

/*
 * This file is part of the mc-framework project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace McFramework\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class HomeController
 *
 * @author Michael COULLERET <michael@coulleret.pro>
 */
class HomeController
{
    /**
     * Home page
     *
     * @return Response
     */
    public function indexAction()
    {
        return new Response('Hello ! '.rand());
    }
}
