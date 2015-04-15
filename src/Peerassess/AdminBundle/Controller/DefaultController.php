<?php

namespace Peerassess\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $sessions = $this->get('peerassess_core.evaluation_session_manager');

        return $this->render('PeerassessAdminBundle:Default:index.html.twig', array(
            'sessions' => $sessions->getAll()
        ));
    }
}
