<?php

/*
 * This file is part of the mc-framework project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace McFramework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class HttpCacheListener
 *
 * @author Michael COULLERET <michael@coulleret.pro>
 */
class HttpCacheListener implements EventSubscriberInterface
{
    /**
     * On response event
     *
     * @param FilterResponseEvent $event
     */
    public function onResponse(FilterResponseEvent $event)
    {
        if (false === $event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();

        $response->setCache([
            'public'        => true,
            'etag'          => md5($response),
            'last_modified' => new \DateTime(),
            'max_age'       => 10,
            's_maxage'      => 10,
        ]);

        $event->setResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::RESPONSE => 'onResponse'];
    }
}
