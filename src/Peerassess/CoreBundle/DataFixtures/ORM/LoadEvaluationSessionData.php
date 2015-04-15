<?php

namespace Peerassess\CoreBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Peerassess\CoreBundle\Entity\EvaluationSession;

class LoadEvaluationSessionData extends BaseFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->loadClaimedInvites($manager);
    }

    private function loadClaimedInvites(ObjectManager $manager)
    {
        $inviteManager = $this->container->get('peerassess_core.evaluation_invite_manager');
        $sessionManager = $this->container->get('peerassess_core.evaluation_session_manager');

        $invites = $manager->getRepository('PeerassessCoreBundle:EvaluationInvite')->findAll();

        // Make candidates claim their invite, except the last one.
        array_pop($invites);
        foreach ($invites as $invite) {
            $candidate = $manager->getRepository('PeerassessCoreBundle:User')
                ->findOneByEmail($invite->getEmail())->getCandidate();

            $inviteManager->claim($candidate, $invite);
        }

        // Make candidates advance in their session.
        $sessions = $manager->getRepository('PeerassessCoreBundle:EvaluationSession')->findAll();

        // Just start the first session, but dont submit anything.
        $sessionManager->nextStage($sessions[0]);

        // Start and submit a result file.
        $sessionManager->nextStage($sessions[1]);
        $sessions[1]->setResultFile($this->getTestFile());
        $sessionManager->nextStage($sessions[1]);

        // Start, submit and start correcting people.
        $sessionManager->nextStage($sessions[2]);
        $sessions[2]->setResultFile($this->getTestFile());
        $sessionManager->nextStage($sessions[2]);
        $sessionManager->assignCorrections($sessions[2]->getEvaluation());
        $sessionManager->nextStage($sessions[2]);

        // Make two candidates finish the whole stuff, including corrections.
        $sessionManager->nextStage($sessions[3]);
        $sessions[3]->setResultFile($this->getTestFile());
        $sessionManager->nextStage($sessions[3]);

        $sessionManager->nextStage($sessions[4]);
        $sessions[4]->setResultFile($this->getTestFile());
        $sessionManager->nextStage($sessions[4]);

        // Run the corrections so we can see the results panel.
        $sessionManager->assignCorrections($sessions[3]->getEvaluation());
        $sessionManager->nextStage($sessions[3]);
        $sessionManager->nextStage($sessions[4]);

        $this->runCorrections($sessions[3], 1);
        $this->runCorrections($sessions[4], 0);

        $sessionManager->nextStage($sessions[3]);
        $sessionManager->nextStage($sessions[4]);
    }

    private function runCorrections(EvaluationSession $session, $minGrade)
    {
        foreach ($session->getCorrectionsGiven() as $c) {
            foreach ($c->getGrades() as $g) {
                $g->setScore(rand($minGrade, 2));
                $g->setComment('Some useful comment about this part of the test.');
            }
            $c->finish();
        }
    }

    /**
     *
     * @return number
     */
    public function getOrder()
    {
        return 301;
    }
}
