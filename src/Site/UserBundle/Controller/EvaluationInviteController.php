<?php

namespace Site\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Peerassess\CoreBundle\Entity\EvaluationInvite;

class EvaluationInviteController extends Controller
{
    /**
     * @ParamConverter("invite", class="PeerassessCoreBundle:EvaluationInvite")
     */
    public function retrieveAction(EvaluationInvite $invite)
    {
        // TODO: make the key a constant and put it somewhere with other
        // constants
        $this->getRequest()->getSession()->set('peerassess.evaluation_invite_id', $invite);
        return new RedirectResponse(
            $this->generateUrl('site_candidate_core_homepage')
        );
    }
}
