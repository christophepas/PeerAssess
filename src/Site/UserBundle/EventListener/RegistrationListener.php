<?php

namespace Site\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Peerassess\CoreBundle\Entity\UserType;
use Peerassess\CoreBundle\Entity\Supervisor;

/**
 * Listener for customer account creation
 * priority 200 for acting before FOSUserBundle EventListener
 *
 * Use FOSUserBundle and add admin validation before account confirmation
 */
class RegistrationListener implements EventSubscriberInterface
{

    private $router;
    protected $mailer;
    protected $twig;

    public function __construct (UrlGeneratorInterface $router,
        \Swift_Mailer $mailer, \Twig_Environment $twig,
        Doctrine $doctrine)
    {
        $this->router = $router;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->em = $doctrine->getManager();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents ()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => "onRegistrationSuccess",
            FOSUserEvents::REGISTRATION_INITIALIZE => "onRegistrationInitialize",
            FOSUserEvents::REGISTRATION_COMPLETED => "onRegistrationCompleted",
            FOSUserEvents::REGISTRATION_CONFIRM => "onRegistrationConfirm"
        );
    }

    /**
     * Before display Account Creation Form
     *
     * @param GetResponseUserEvent $event
     */
    public function onRegistrationInitialize (GetResponseUserEvent $event)
    {
        $user = $event->getUser();

        // Create a Customer associated to the User
        $user->setType(UserType::SUPERVISOR);

        // Set the User type to Customer
        $supervisor = new Supervisor();
        $user->setSupervisor($supervisor);
        $supervisor->setUser($user);
    }

    /**
     * Before New Account Creation
     *
     * @param FormEvent $event
     */
    public function onRegistrationSuccess (FormEvent $event)
    {
        // $user = $event->getForm()->getData();

        // // / if the User is not confirmed yet
        // if (! $user->getConfirmed())
        // {
        //     // redirect to homepage and add hash #success-alpha to display
        //     // confirm message
        //     $url = $this->router->generate('site_supervisor_core_homepage') .
        //              "#success-alpha";
        //     $event->setResponse(new RedirectResponse($url));

        //     // prevent propagation to FOSUSerBundle EventListeners (mail sending)
        //     $event->stopPropagation();
        // }
        // // Si le user a bien été confirmé (donc post enable)
        // else{
        //     //Dans tous les cas, mail de bienvenu (propre à l'user)
        //     $templateFile = "SiteUserBundle:Supervisor:welcome.html.twig";
        //     $templateContent = $this->twig->loadTemplate($templateFile);
        //     $body = $templateContent->render(array("user" => $user));
        //     $message = \Swift_Message::newInstance()->setSubject(
        //             "Votre accès à Peerassess")
        //         ->setFrom('support@peerassess.co')
        //         ->setTo($user->getEmail())
        //         ->setBody($body, 'text/html');
        //     $this->mailer->send($message);
        //     // prevent propagation to FOSUSerBundle EventListeners (mail sending)
        //     $event->stopPropagation();
        // }
        $user = $event->getForm()->getData();
        if ($user->getType() == UserType::SUPERVISOR){
            $user->addRole("ROLE_SUPERVISOR");
            $event->setResponse(
                new RedirectResponse(
                        $this->router->generate('site_supervisor_core_homepage')));
        }
        elseif ($user->getType() == UserType::CANDIDATE) {
            $user->addRole("ROLE_CANDIDATE");
            $event->setResponse(
                new RedirectResponse(
                        $this->router->generate('site_candidate_core_homepage')));
        }

    }

    /**
     * After Account Creation
     *
     * @param FilterUserResponseEvent $event
     */
    public function onRegistrationCompleted (FilterUserResponseEvent $event)
    {
        // prevent propagation to FOSUSerBundle EventLiteners (auto-login)
        // $event->stopPropagation();
    }

    /**
     * After token confirmation
     *
     * @param GetResponseUserEvent $event
     */
    public function onRegistrationConfirm (GetResponseUserEvent $event)
    {
        $event->setResponse(
                new RedirectResponse(
                        $this->router->generate('site_supervisor_core_homepage')));
    }
}
