<?php

namespace Peerassess\CoreBundle\Service;

use Peerassess\CoreBundle\Entity\Candidate;
use Peerassess\CoreBundle\Entity\Supervisor;
use Peerassess\CoreBundle\Entity\EvaluationSession;
use Peerassess\CoreBundle\Entity\EvaluationInvite;
use Peerassess\CoreBundle\Entity\Evaluation;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;
use Site\CandidateBundle\Services\NotificationMailer;

class EvaluationInviteManager
{
    private $em;
    private $security;
    private $notifier;
    private $sessions;

    public function __construct(EntityManager $em, SecurityContext $security, NotificationMailer $notifier, EvaluationSessionManager $sessions)
    {
        $this->em = $em;
        $this->security = $security;
        $this->notifier = $notifier;
        $this->sessions = $sessions;
    }

    public function create(Evaluation $evaluation, $email)
    {
        $invite = new EvaluationInvite();

        $invite->setEmail($email);
        $invite->setCreatedDate(new \DateTime());
        $invite->setEvaluation($evaluation);
        $invite->setScheduledDate(new \DateTime());

        return $invite;
    }

    public function getUnclaimed(Supervisor $supervisor)
    {
        $invites = $this->em->getRepository('PeerassessCoreBundle:EvaluationInvite');

        // Older than 1 day.
        $interval = new \DateInterval('P1D');

        return $invites->findUnclaimed($supervisor, $interval);
    }

    public function claim(Candidate $candidate, EvaluationInvite $invite)
    {
        if ($invite->isClaimed()) {
            throw new \LogicException('Can\'t claim an invite more than once.');
        }

        $session = $this->sessions->create($invite->getEvaluation(), $candidate);

        $session->setInvite($invite);
        $session->setScheduledDate($invite->getScheduledDate());
        $latestStart = clone $session->getScheduledDate();
        $session->setLatestStartDate($latestStart->add(
            // One week to start the test.
            new \DateInterval('P7D')
        ));

        $this->em->persist($session);
        $this->em->flush();
    }

    public function send(EvaluationInvite $invite)
    {
        $this->notifier->notifyTestInvite($invite);
        $this->em->persist($invite);
        $this->em->flush();
    }
}
