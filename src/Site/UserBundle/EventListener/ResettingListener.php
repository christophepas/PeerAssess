<?php

namespace Site\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Event\FormEvent;

class ResettingListener implements EventSubscriberInterface
{

    private $router;

    public function __construct (UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents ()
    {
        return array(
                FOSUserEvents::RESETTING_RESET_SUCCESS => "onResettingSuccess"
        );
    }

    /**
     *
     * @param FormEvent $event
     */
    public function onResettingSuccess (FormEvent $event)
    {
        $url = $this->router->generate('site_vitrine_core_homepage');
        $event->setResponse(new RedirectResponse($url));
    }
}
