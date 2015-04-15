<?php

namespace Peerassess\CoreBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManager;
use Peerassess\CoreBundle\Entity\Status as EvaluationSessionStatus;
use Peerassess\CoreBundle\Entity\EvaluationSession;
use Peerassess\CoreBundle\Entity\EvaluationInvite;
use Peerassess\CoreBundle\Entity\Candidate;
use Peerassess\CoreBundle\Entity\Supervisor;
use Peerassess\CoreBundle\Entity\Correction;
use Peerassess\CoreBundle\Entity\Evaluation;
use Site\CandidateBundle\Services\NotificationMailer;
use Peerassess\CoreBundle\Exception\EvaluationSession as ESE;

class EvaluationSessionManager
{
    /**
     * @var FileManager
     */
    private $files;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var int
     */
    private $minCorrections;

    /**
     * @var \Site\CandidateBundle\Services\NotificationMailer
     */
    private $notifier;

    /**
     * @param int $minCorrections
     */
    public function __construct(FileManager $files, EntityManager $em,
        $minCorrections, NotificationMailer $notifier)
    {
        $this->files = $files;
        $this->em = $em;
        $this->minCorrections = $minCorrections;
        $this->notifier = $notifier;
    }

    /**
     * Get all evaluation sessions.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->em->getRepository('PeerassessCoreBundle:EvaluationSession')
            ->findAll();
    }

    /**
     * Get recently finished sessions for a supervisor.
     *
     * @return array
     */
    public function getFinished(Supervisor $supervisor)
    {
        // Get the finished sessions.
        $sessions = $this->em->getRepository('PeerassessCoreBundle:EvaluationSession')
            ->findFinishedBySupervisor($supervisor);

        return $sessions;
    }

    public function create(Evaluation $evaluation, Candidate $candidate)
    {
        $session = new EvaluationSession();
        $session->setEvaluation($evaluation);
        $session->setCandidate($candidate);
        $session->setScheduledDate(new \DateTime());
        $latestStart = clone $session->getScheduledDate();
        $session->setLatestStartDate($latestStart->add(
            // One week to start the test.
            new \DateInterval('P7D')
        ));

        return $session;
    }

    /**
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $session
     */
    public function nextStage(EvaluationSession $session)
    {
        $status = $session->getStatus();

        switch ($status) {
            case EvaluationSessionStatus::CREATED:
                return $this->nextStageFromCreated($session);
                break;
            case EvaluationSessionStatus::RUNNING:
                return $this->nextStageFromRunning($session);
                break;
            case EvaluationSessionStatus::WAITINGTOCORRECT:
                return $this->nextStageFromWaitingToCorrect($session);
                break;
            case EvaluationSessionStatus::CORRECTING:
                return $this->nextStageFromCorrecting($session);
                break;
            default:
                throw new \Exception('No next stage after stage ' . $status);
                break;
        }
    }

    /**
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $session
     */
    public function checkStage(EvaluationSession $session)
    {
        $status = $session->getStatus();

        switch ($status) {
            case EvaluationSessionStatus::CREATED:
                return $this->checkStageCreated($session);
                break;
            case EvaluationSessionStatus::RUNNING:
                return $this->checkStageRunning($session);
                break;
            case EvaluationSessionStatus::WAITINGTOCORRECT:
                return $this->checkStageWaitingToCorrect($session);
                break;
            case EvaluationSessionStatus::CORRECTING:
                return $this->checkStageCorrecting($session);
                break;
            case EvaluationSessionStatus::CLOSED:
                return $this->checkStageClosed($session);
                break;
            case EvaluationSessionStatus::LATE_START:
                // TODO: do something here ?
                break;
            default:
                throw new \Exception('o_O No evaluation session stage: ' . $status);
                break;
        }
    }

    /**
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $session
     */
    private function checkStageCreated(EvaluationSession $session)
    {
        // If the test is scheduled for later.
        if (new \DateTime() < $session->getScheduledDate()) {
            throw new ESE\EarlyStartException();
        }

        // If the test is no longer available, ie the candidate is starting late.
        if (new \DateTime() > $session->getLatestStartDate()) {
            $session->setStatus(EvaluationSessionStatus::LATE_START);

            $this->em->persist($session);
            $this->em->flush();

            // Notify the supervisor if the candidate did not start on time.
            $invite = $session->getInvite();
            if ($invite !== null) {
                $this->notifier->notifyLateStart($invite);
            }

            throw new ESE\LateStartException();
        }
    }

    /**
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $session
     */
    private function nextStageFromCreated(EvaluationSession $session)
    {
        $this->checkStageCreated($session);

        $session->setStatus(EvaluationSessionStatus::RUNNING);

        $session->setStart(new \DateTime());

        // Set the end of the test so we can disallow late submissions.
        $now = (new \DateTime())->getTimestamp();
        $duration = $session->getEvaluation()->getTest()->getDuration();
        $end = (new \DateTime())->setTimestamp($now + $duration);
        $session->setEnd($end);

        $this->em->persist($session);
        $this->em->flush();
    }

    /**
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $session
     */
    private function checkStageRunning(EvaluationSession $session)
    {
        // Check for late result file submissions.
        if (new \DateTime() > $session->getEnd()) {
            $session->setStatus(EvaluationSessionStatus::WAITINGTOCORRECT);

            $this->em->persist($session);
            $this->em->flush();

            throw new ESE\LateSubmissionException();
        }
    }

    /**
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $session
     */
    private function nextStageFromRunning(EvaluationSession $session)
    {
        $this->checkStageRunning($session);

        // Save the result file to storage.
        $this->files->save(
            $session->getResultFile(),
            $session->getResultFileKey()
        );

        $session->setStatus(EvaluationSessionStatus::WAITINGTOCORRECT);

        $session->setEnd(new \DateTime());

        $this->em->persist($session);
        $this->em->flush();
    }

    /**
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $session
     */
    private function checkStageWaitingToCorrect(EvaluationSession $session)
    {
        if (count($session->getCorrectionsGiven()) < $this->minCorrections) {
            throw new ESE\CorrectionLackException();
        }
    }

    /**
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $session
     */
    private function nextStageFromWaitingToCorrect(EvaluationSession $session)
    {
        $this->checkStageWaitingToCorrect($session);

        $session->setStatus(EvaluationSessionStatus::CORRECTING);

        $session->setCorrectionStart(new \DateTime());

        $now = (new \DateTime())->getTimestamp();
        $duration = $session->getEvaluation()->getCorrectionDuration();
        $end = (new \DateTime())->setTimestamp($now + $duration);
        $session->setCorrectionEnd($end);

        $this->em->persist($session);
        $this->em->flush();
    }

    /**
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $session
     */
    private function checkStageCorrecting(EvaluationSession $session)
    {
        // Too late to correct.
        if (new \DateTime() > $session->getCorrectionEnd()) {
            $session->setStatus(EvaluationSessionStatus::CLOSED);

            $this->em->persist($session);
            $this->em->flush();

            throw new ESE\LateCorrectionException();
        }
    }

    /**
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $session
     */
    private function nextStageFromCorrecting(EvaluationSession $session)
    {
        $this->checkStageCorrecting($session);

        $session->setStatus(EvaluationSessionStatus::CLOSED);

        $session->setCorrectionEnd(new \DateTime());

        $this->em->persist($session);
        $this->em->flush();
    }

    /**
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $session
     */
    private function checkStageClosed(EvaluationSession $session)
    {
        // TODO: Is this method useful ?
    }

    /**
     * Find correcter/correctee pairs for the given evaluation.
     *
     * This method does not handle the case where a user does the same
     * test twice at the same time. In that case, the candidate may be
     * correcting him/her-self. For this reason, it is expected that a user
     * cannot do the same test twice at the same time.
     */
    public function assignCorrections(Evaluation $eval)
    {
        $sessions = $eval->getEvaluationSessions()->toArray();

        // Find people that must correct others.
        $correctersSessions = array_filter($sessions, function($s) {
            $statusOK = $s->getStatus() === EvaluationSessionStatus::WAITINGTOCORRECT;
            $countOK = count($s->getCorrectionsGiven()) < $this->minCorrections;

            return $statusOK && $countOK;
        });

        // Find people that can still be corrected.
        $correcteesSessions = array_filter($sessions, function($s) {
            $status = $s->getStatus();
            $statusOK = in_array($status, array(
                EvaluationSessionStatus::CLOSED,
                EvaluationSessionStatus::CORRECTING,
                EvaluationSessionStatus::WAITINGTOCORRECT,
            ));
            $countOK = count($s->getCorrectionsReceived()) < $this->minCorrections;

            return $statusOK && $countOK;
        });

        // Assign as many corrections as possible.
        foreach ($correctersSessions as $scr) {
            foreach ($correcteesSessions as $sce) {
                // If both the correcter and correctee still have slots open,
                // we'll them to one to correct the other.
                if (
                    // Don't correct myself.
                    $scr->getCandidate()->getUser()->getId() !== $sce->getCandidate()->getUser()->getId() &&
                    // Make sure the correcter can still correct others.
                    count($scr->getCorrectionsGiven()) < $this->minCorrections &&
                    // Make sure the other is not yet corrected.
                    count($sce->getCorrectionsReceived()) < $this->minCorrections &&
                    // Don't correct the same candidate twice.
                    ! $scr->isCorrecting($sce)
                ) {
                    // Add a new pending correction.
                    $correction = new Correction();
                    $correction->setEvaluationSessionGiver($scr);
                    $correction->setEvaluationSessionReceiver($sce);
                    $this->em->persist($correction);

                    // If the correcter has enough assigned corrections, send
                    // a notification so the correction process can start.
                    if (count($scr->getCorrectionsGiven()) >= $this->minCorrections) {
                        $this->notifier->notifyCorrectionStart($scr);
                    }
                }
            }
        }

        // Save assigned corrections.
        $this->em->flush();
    }

    /**
     * @return UploadedFile
     */
    private function getEmptyFile()
    {
        // Original empty file path.
        $src = __DIR__ . '/../Resources/public/empty-result.zip';

        // Make a temporary filename for the empty result.
        $dest = '/tmp/result-' . microtime() . '-' . getmypid() . '.zip';
        if (copy($src, $dest) === false) {
            throw new \Exception('Could not copy empty result.');
        }

        return new UploadedFile(
            $dest,
            'base.zip',
            null, // mime-type
            null, // size
            null, // error
            true  // test
        );
    }
}
