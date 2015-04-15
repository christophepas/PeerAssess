<?php

namespace Peerassess\MarketingWebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('PeerassessMarketingWebsiteBundle:Default:index.html.twig', array('name' => $name));
    }
}
