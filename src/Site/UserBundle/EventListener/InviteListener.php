<?php

namespace Site\UserBundle\EventListener;

use Symfony\Component\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Doctrine\ORM\EntityManager;
use Peerassess\CoreBundle\Entity\EvaluationSession;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Peerassess\CoreBundle\Service\EvaluationInviteManager;

use Symfony\Component\Translation\Translator;

class InviteListener implements EventSubscriberInterface
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
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    private $security;

    /**
     * @var \Peerassess\CoreBundle\Service\EvaluationInviteManager
     */
    private $invites;

    public function __construct(Router $router, EntityManager $em, SecurityContext $security, EvaluationInviteManager $invites,Translator $translator)
    {
        $this->router = $router;
        $this->em = $em;
        $this->translator = $translator;
        $this->security = $security;
        $this->invites = $invites;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(
                array('checkInvite', 0),
            )
        );
    }

    /**
     *
     * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
     */
    public function checkInvite(FilterControllerEvent $event)
    {
        // Check if an invite token was sent, if so, assign a new eval session
        // to the logged in user.
        if ($this->security->isGranted('ROLE_CANDIDATE')) {
            $session = $event->getRequest()->getSession();

            // If the user has claimed the invite via a link provided in the invite
            // email, a session variable with the invite ID is set.
            if ($session->has('peerassess.evaluation_invite_id')) {

                $invite = $this->getInviteFromSession($session);

                // Check if the invite exists. This can happen when an invite is
                // removed by the supervisor but the candidat has already
                // clicked on it and is trying to connect with it.
                if (null === $invite) {
                    $message = $this->translator->trans('invite.removed',array(),'SiteCandidateBundle');
                    $session->getFlashBag()->add('notice',$message);
                    return;
                }

                // Check if the session exists, don't attempt to re-create it.
                // This can happen if a user clicks on the invite link more
                // than once.
                if ($invite->getEvaluationSession()) {
                    return;
                }

                $candidate = $this->security->getToken()->getUser()->getCandidate();
                $this->invites->claim($candidate, $invite);
                $message = $this->translator->trans('invite.success',array(),'SiteCandidateBundle');
                $session->getFlashBag()->add('notice', $message);
            }
        }
    }

    private function getInviteFromSession(SessionInterface $session)
    {
        $repo = $this->em->getRepository('PeerassessCoreBundle:EvaluationInvite');

        $invite = $repo->findOneById(
            $session->get('peerassess.evaluation_invite_id')
        );

        $session->remove('peerassess.evaluation_invite_id');

        return $invite;
    }

}
