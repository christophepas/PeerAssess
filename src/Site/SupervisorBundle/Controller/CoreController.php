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
}
