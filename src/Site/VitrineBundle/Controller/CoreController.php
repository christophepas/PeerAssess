<?php

namespace Site\VitrineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Site\VitrineBundle\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;

class CoreController extends Controller
{

    public function homepageAction ()
    {
        if ($this->get('security.context')->isGranted('ROLE_SUPERVISOR') === TRUE) {
            return $this->render('SiteVitrineBundle:Core:company.html.twig');
        } else if ($this->get('security.context')->isGranted('ROLE_CANDIDATE') === TRUE) {
            return $this->render('SiteVitrineBundle:Core:candidate.html.twig');
        }
        return $this->render('SiteVitrineBundle:Core:company.html.twig');
    }

    public function candidateAction ()
    {
        return $this->render('SiteVitrineBundle:Core:candidate.html.twig');
    }

    public function companyAction ()
    {
        return $this->render('SiteVitrineBundle:Core:company.html.twig');
    }

    public function teamAction ()
    {
        return $this->render('SiteVitrineBundle:Core:team.html.twig');
    }

    public function faqAction ()
    {
        return $this->render('SiteVitrineBundle:Core:faq.html.twig');
    }

    public function techniqueAction ()
    {
        return $this->render('SiteVitrineBundle:Core:technique.html.twig');
    }

    public function legalAction ()
    {
        return $this->render('SiteVitrineBundle:Core:legal.html.twig');
    }

    public function testTwigAction ($code)
    {
        return $this->render('TwigBundle:Exception:error'.$code.'.html.twig');
    }

    public function landingAction ()
    {
        return $this->render('SiteVitrineBundle:Core:landing.html.twig');
    }
}
