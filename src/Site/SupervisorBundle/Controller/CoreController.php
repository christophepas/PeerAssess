<?php

namespace Site\SupervisorBundle\Controller;

use Peerassess\CoreBundle\Controller\BaseController;
use Peerassess\CoreBundle\Entity\Status;

class CoreController extends BaseController
{
    public function dashboardAction()
    {
        $sessions = $this->get('peerassess_core.evaluation_session_manager');
        $invites = $this->get('peerassess_core.evaluation_invite_manager');

        $finished = $sessions->getFinished($this->getSupervisor());
        $unclaimed = $invites->getUnclaimed($this->getSupervisor());

        return $this->render(
            'SiteSupervisorBundle:Core:dashboard.html.twig',
            array(
                'finishedSessions' => $finished,
                'unclaimedInvites' => $unclaimed,
            )
        );
    }

    public function settingsAction()
    {
        return $this->render(
            'SiteSupervisorBundle:Core:settings.html.twig',
            array(
                'supervisor' => $this->getSupervisor()
            )
        );
    }

    public function switchAction()
    {
        $supervisors = $this->getDoctrine()
            ->getRepository('PeerassessCoreBundle:Supervisor')
            ->findAll();

        return $this->render(
            'SiteSupervisorBundle:Core:switch.html.twig',
            array(
                'supervisors' => $supervisors
            )
        );
    }

    public function subscribeAction ($newPlan){
        $supervisor = $this->get('security.context')
            ->getToken()
            ->getUser()
            ->getSupervisor();
        $em = $this->getDoctrine()->getManager();
        $company = $supervisor->getCompany();
        //Si on soumet le formulaire
        if ($this->getRequest()->isMethod('POST') ) {
            //Setup de Stripe
            require_once(__DIR__.'/../../../../vendor/stripe/stripe-php/lib/Stripe.php');
            //TODO: store the api key in a safe parameter file
            \Stripe::setApiKey("sk_test_PZVnXlhdG4kF6WjUW7hfkCGf");
            $stripeToken = $_POST['stripeToken'];
            //TODO à changer, on récupère le nouveau choix d'abonnement en premier lieu (soit standard soit business)
            $nextPlan = $company->getNextPlan();;
            $currentPlan = $company->getPlan();
            if( $newPlan == $currentPlan ){
                $this->get('session')->getFlashBag()->add('notice','Vous bénéficiez déjà de cet abonnement');
            }
            elseif($newPlan == $nextPlan){
                $this->get('session')->getFlashBag()->add('notice','Vous avez déjà demandé à changer d\'abonnement');
            }
            elseif ($currentPlan=='custom') {
                $this->get('session')->getFlashBag()->add('notice','Vous bénéficiez d\'un abonnement personnalisé. Contactez-nos équipes pour toute demande.');
            }
            elseif($currentPlan=='trial' || $currentPlan=='trialEnded' ){
                $company->setPlan($newPlan);
                $company->setNextPlan($newPlan);

                if ($newPlan == "standard"){
                    $credits = 5;
                }
                elseif ($newPlan == "business"){
                    $credits = 20;
                }
                $company->setCreditsPerMonth($credits);
                $company->setCreditsRemaining($credits);//Le plan était en trial, l'abonnement commence donc directement

                $dateNextPlan = new \DateTime();
                $dateNextPlan->add(new \DateInterval('P1M'));
                $company->setDateNextPlan($dateNextPlan);
                $stripeSupervisor = \Stripe_Supervisor::create(array(
                  "card" => $stripeToken,
                  "plan" => $newPlan,
                  "email" => $supervisor->getUser()->getEmail() )
                );
                $stripeId = $stripeSupervisor->id;
                $company->setStripeId($stripeId);
                $company->setStripeSubscriptionId($stripeSupervisor->subscriptions->data[0]->id);
                $em->persist($company);
                $em->flush();
                $this->get('session')->getFlashBag()->add('notice','Vous bénéficiez maintenant de votre nouvel abonnement');
            }
            elseif($currentPlan=='canceled'){//On doit maj le subscriptionId
                $company->setPlan($newPlan);
                $company->setNextPlan($newPlan);
                if ($newPlan == "standard"){
                    $credits = 5;
                }
                elseif ($newPlan == "business"){
                    $credits = 20;
                }
                $company->setCreditsPerMonth($credits);
                $company->setCreditsRemaining($credits);//Le plan était en trial, l'abonnement commence donc directement
                $dateNextPlan = new \DateTime();
                $dateNextPlan->add(new \DateInterval('P1M'));
                $company->setDateNextPlan($dateNextPlan);

                $stripeId = $company->getStripeId();
                $stripeSupervisor = \Stripe_Supervisor::retrieve($stripeId);
                $stripeSupervisor->card =  $stripeToken;
                $stripeSupervisor->subscriptions->create(array("plan" => $newPlan));
                $company->setStripeSubscriptionId($stripeSupervisor->subscriptions->data[0]->id);

                $em->persist($company);
                $em->flush();
            }
            else{ //Sinon le plan actuel est business ou standard
                $company->setNextPlan($newPlan);
                //On ne change pas les crédits, seront changés durant la routine de changement de plan
                $stripeId = $company->getStripeId();
                $stripeSubscriptionId = $company->getStripeSubscriptionId();
                $stripeSupervisor = \Stripe_Supervisor::retrieve($stripeId);
                $stripeSupervisor->card =  $stripeToken;
                $stripeSubscription = $stripeSupervisor->subscriptions->retrieve($stripeSubscriptionId);
                $stripeSubscription->plan =  $newPlan;
                $stripeSubscription->prorate =  false;
                $stripeSubscription->save();

                $em->persist($company);
                $em->flush();
                $this->get('session')->getFlashBag()->add('notice','Votre nouvel abonnement changera bien lors de la prochaine période de souscription.');
            }
            return $this->redirect(
                    $this->generateUrl('site_supervisor_core_settings',
                            array(
                                'supervisor' => $supervisor
                            )));
        }
        return $this->render('SiteSupervisorBundle:Core:subscribe.html.twig',
                array(
                        'supervisor' => $supervisor
                ));
    }
    public function cancelSubscriptionAction(){
        //On enregistre le plan comme "canceled" dans l'entité company
        $supervisor = $this->get('security.context')
            ->getToken()
            ->getUser()
            ->getSupervisor();
        $em = $this->getDoctrine()->getManager();
        $company = $supervisor->getCompany();
        $company->setPlan('canceled');
        $company->setNextPlan('canceled');
        $credits = 0;
        $company->setCreditsPerMonth($credits);
        $company->setCreditsRemaining($credits);
        $em->persist($company);
        $em->flush();

        //On annule l'abonnement sur Stripe
        require_once(__DIR__.'/../../../../vendor/stripe/stripe-php/lib/Stripe.php');
        \Stripe::setApiKey("sk_test_PZVnXlhdG4kF6WjUW7hfkCGf");
        $stripeId = $company->getStripeId();
        $stripeSubscriptionId = $company->getStripeSubscriptionId();
        $stripeSupervisor = \Stripe_Supervisor::retrieve($stripeId);
        $stripeSubscription = $stripeSupervisor->subscriptions->retrieve($stripeSubscriptionId);
        $stripeSubscription->cancel();
        //$stripeSubscription->at_period_end = true; permettrait de laisser l'abonnement jusuq'à la fin de la période.

        $em = $this->getDoctrine()->getManager();
        $company->setStripeSubscriptionId(null);
        $em->persist($company);
        $em->flush();

        $this->get('session')->getFlashBag()->add('notice','Votre abonnement a bien été annulé.');
        return $this->redirect(
                $this->generateUrl('site_supervisor_core_settings',
                        array(
                            'supervisor' => $supervisor
                        )));
    }
}
