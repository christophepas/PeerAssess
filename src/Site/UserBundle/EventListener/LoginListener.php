<?php

namespace Site\UserBundle\EventListener;

use Symfony\Component\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\ORM\EntityManager;
use Peerassess\CoreBundle\Entity\EvaluationSession;

class LoginListener implements EventSubscriberInterface
{

    /**
     * @var \Symfony\Component\Routing\Router
     */
    private $router;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    private $security;

    public function __construct(Router $router, EntityManager $em, SecurityContext $security)
    {
        $this->router = $router;
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN => array(
                array('updateFirstLogin', 5),
                array('redirectToDashboard', 0),
            )
        );
    }

    /**
     *
     * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
     */
    public function updateFirstLogin(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();
        $user = $token->getUser();

		if ($user->getFirstLogin()) {
			$user->setFirstLogin(false);
			$this->em->persist($user);
			$this->em->flush();
		}
    }

    /**
     *
     * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
     */
    public function redirectToDashboard(InteractiveLoginEvent $event)
    {
        if ($this->security->isGranted('ROLE_SUPERVISOR')) {
            $route = 'site_supervisor_core_homepage';
		} elseif ($this->security->isGranted('ROLE_CANDIDATE')) {
            $route = 'site_candidate_core_homepage';
		}
        $event->getRequest()->request->set('_target_path', $route);
    }


}
