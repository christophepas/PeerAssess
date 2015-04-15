<?php

namespace Site\CandidateBundle\Controller;

use Peerassess\CoreBundle\Controller\BaseController;
use Peerassess\CoreBundle\Entity\EvaluationInvite;
use Peerassess\CoreBundle\Entity\EvaluationSession;

class CoreController extends BaseController
{
    public function homepageAction ()
    {
        $candidate = $this->getCandidate();

        $em = $this->getDoctrine()->getManager();

        $sessions = $em->getRepository('PeerassessCoreBundle:EvaluationSession')
            ->findByCandidate($candidate);
        $invites = $em->getRepository('PeerassessCoreBundle:EvaluationInvite')
            ->findByCandidate($candidate);

        return $this->render('SiteCandidateBundle:Core:homepage.html.twig', array(
            'sessions' => $sessions,
            'invites' => $invites
        ));
    }

    public function claimInviteAction(EvaluationInvite $invite)
    {
        $this->get('peerassess_core.evaluation_invite_manager')
            ->claim($this->getCandidate(), $invite);

        $tr = $this->get('translator');
        $message = $tr->trans('invite.claimed',array(),'SiteCandidateBundle');
        $this->addFlash('success', $message);

        return $this->redirectBack();
    }
}
