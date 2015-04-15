<?php

namespace Peerassess\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Peerassess\CoreBundle\Entity\EvaluationInvite;

class LoadEvaluationInviteData extends BaseFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $evaluations = $manager->getRepository('PeerassessCoreBundle:Test')
            ->findAll()[0]
            ->getEvaluations();

        $emails = array_map(function($candidate) {
            return $candidate->getUser()->getEmail();
        }, $manager->getRepository('PeerassessCoreBundle:Candidate')->findAll());

        $inviteManager = $this->container->get('peerassess_core.evaluation_invite_manager');

        foreach ($evaluations as $e) {
            foreach ($emails as $emi) {
                $ie = $inviteManager->create($e, $emi);
                $inviteManager->send($ie);
            }
        }
    }

    /**
     *
     * @return number
     */
    public function getOrder()
    {
        return 251;
    }
}
