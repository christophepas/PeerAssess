<?php

namespace Peerassess\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Peerassess\CoreBundle\Entity\Evaluation;
use Peerassess\CoreBundle\Entity\Applicant;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Site\CoreBundle\Validator\Constraints as CustomAssert;


/**
 * EvaluationSession
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Peerassess\CoreBundle\Entity\EvaluationSessionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class EvaluationSession
{

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetime", nullable=true)
     */
    private $start;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime", nullable=true)
     */
    private $end;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column( type="datetime", nullable=true)
     */
    private $correctionStart;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $correctionEnd;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="scheduledDate", type="datetime", nullable=true)
     */
    private $scheduledDate;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="latestStartDate", type="datetime", nullable=true)
     */
    private $latestStartDate;

    /**
     * @ORM\ManyToOne(targetEntity="Peerassess\CoreBundle\Entity\Candidate",
     * inversedBy="evaluationSessions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    protected $candidate;

    /**
     * @ORM\ManyToOne(targetEntity="Peerassess\CoreBundle\Entity\Evaluation",
     * inversedBy="evaluationSessions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    protected $evaluation;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $token;

    /**
     * @ORM\OneToMany(targetEntity="Peerassess\CoreBundle\Entity\Correction",
     * mappedBy="evaluationSessionGiver", cascade={"persist", "remove"})
     */
    protected $correctionsGiven;

    /**
     * @ORM\OneToMany(targetEntity="Peerassess\CoreBundle\Entity\Correction",
     * mappedBy="evaluationSessionReceiver", cascade={"persist", "remove"})
     */
    protected $correctionsReceived;

    /**
     * The invite which led to this session, if any.
     *
     * @var \Peerassess\CoreBundle\Entity\EvaluationInvite
     *
     * @ORM\OneToOne(targetEntity="Peerassess\CoreBundle\Entity\EvaluationInvite",
     * inversedBy="evaluationSession", cascade={"persist"})
     **/
    private $invite;

    /**
     * @Assert\File()
     * @CustomAssert\FileExtension(extension="zip")
     */
    private $resultFile;

    /**
     * The date at which this was archived, or NULL.
     *
     * @var \DateTime
     *
     * @ORM\Column(name="archivedDate", type="datetime", nullable=true)
     */
    private $archivedDate;


    public function __construct ()
    {
        $this->setScheduledDate(new \DateTime());
        $this->setStatus(Status::CREATED);
        $this->setToken(md5(uniqid(rand(), true)));
    }

    public function getStringStatus()
    {
        return Status::getStatus($this->status);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     * @return EvaluationSession
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return EvaluationSession
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set scheduledDate
     *
     * @param \DateTime $scheduledDate
     * @return EvaluationSession
     */
    public function setScheduledDate($scheduledDate)
    {
        $this->scheduledDate = $scheduledDate;

        return $this;
    }

    /**
     * Get scheduledDate
     *
     * @return \DateTime
     */
    public function getScheduledDate()
    {
        return $this->scheduledDate;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return EvaluationSession
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return EvaluationSession
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set candidate
     *
     * @param \Peerassess\CoreBundle\Entity\Candidate $candidate
     * @return EvaluationSession
     */
    public function setCandidate(\Peerassess\CoreBundle\Entity\Candidate $candidate = null)
    {
        $this->candidate = $candidate;

        if (null !== $candidate && !$candidate->getEvaluationSessions()->contains($this)) {
            $candidate->addEvaluationSession($this);
        }

        return $this;
    }

    /**
     * Get candidate
     *
     * @return \Peerassess\CoreBundle\Entity\Candidate
     */
    public function getCandidate()
    {
        return $this->candidate;
    }

    /**
     * Set evaluation
     *
     * @param \Peerassess\CoreBundle\Entity\Evaluation $evaluation
     * @return EvaluationSession
     */
    public function setEvaluation(\Peerassess\CoreBundle\Entity\Evaluation $evaluation = null)
    {
        $this->evaluation = $evaluation;

        if (null !== $evaluation && !$evaluation->getEvaluationSessions()->contains($this)) {
            $evaluation->addEvaluationSession($this);
        }

        return $this;
    }

    /**
     * Get evaluation
     *
     * @return \Peerassess\CoreBundle\Entity\Evaluation
     */
    public function getEvaluation()
    {
        return $this->evaluation;
    }

    /**
     * Add correctionsGiven
     *
     * @param \Peerassess\CoreBundle\Entity\Correction $correctionsGiven
     * @return EvaluationSession
     */
    public function addCorrectionsGiven(\Peerassess\CoreBundle\Entity\Correction $correctionsGiven)
    {
        $this->getCorrectionsGiven()[] = $correctionsGiven;

        if ($correctionsGiven->getEvaluationSessionGiver() !== $this) {
            $correctionsGiven->setEvaluationSessionGiver($this);
        }

        return $this;
    }

    /**
     * Get the assigned pending corrections.
     *
     * @return array<Correction>
     */
    public function getPendingCorrectionsGiven()
    {
        $corrections = $this->getCorrectionsGiven()->filter(function($c) {
            return false === $c->isFinished();
        })->getValues();

        usort($corrections, function($a, $b) {
            return $a->getId() - $b->getId();
        });

        return $corrections;
    }

    /**
     * Get the next assigned pending correction.
     *
     * @return Correction
     * @throws LogicException
     */
    public function getNextPendingCorrectionGiven()
    {
        $corrections = $this->getPendingCorrectionsGiven();

        if (count($corrections) > 0) {
            return $corrections[0];
        }

        throw new \LogicException('No pending corrections left.');
    }

    /**
     * Remove correctionsGiven
     *
     * @param \Peerassess\CoreBundle\Entity\Correction $correctionsGiven
     */
    public function removeCorrectionsGiven(\Peerassess\CoreBundle\Entity\Correction $correctionsGiven)
    {
        $this->getCorrectionsGiven()->removeElement($correctionsGiven);

        $correctionsGiven->setEvaluationSessionGiver(null);
    }

    /**
     * Get correctionsGiven
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCorrectionsGiven()
    {
        if (!$this->correctionsGiven) {
            $this->correctionsGiven = new ArrayCollection();
        }

        return $this->correctionsGiven;
    }

    /**
     * Add correctionsReceived
     *
     * @param \Peerassess\CoreBundle\Entity\Correction $correctionsReceived
     * @return EvaluationSession
     */
    public function addCorrectionsReceived(\Peerassess\CoreBundle\Entity\Correction $correctionsReceived)
    {
        $this->getCorrectionsReceived()[] = $correctionsReceived;

        if ($correctionsReceived->getEvaluationSessionReceiver() !== $this) {
            $correctionsReceived->setEvaluationSessionReceiver($this);
        }

        return $this;
    }

    /**
     * Remove correctionsReceived
     *
     * @param \Peerassess\CoreBundle\Entity\Correction $correctionsReceived
     */
    public function removeCorrectionsReceived(\Peerassess\CoreBundle\Entity\Correction $correctionsReceived)
    {
        $this->getCorrectionsReceived()->removeElement($correctionsReceived);

        $correctionsReceived->setEvaluationSessionReceiver(null);
    }

    /**
     * Get correctionsReceived
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCorrectionsReceived()
    {
        if (!$this->correctionsReceived) {
            $this->correctionsReceived = new ArrayCollection();
        }

        return $this->correctionsReceived;
    }

    /**
     * Set correctionStart
     *
     * @param \DateTime $correctionStart
     * @return EvaluationSession
     */
    public function setCorrectionStart($correctionStart)
    {
        $this->correctionStart = $correctionStart;

        return $this;
    }

    /**
     * Get correctionStart
     *
     * @return \DateTime
     */
    public function getCorrectionStart()
    {
        return $this->correctionStart;
    }

    /**
     * Set correctionEnd
     *
     * @param \DateTime $correctionEnd
     * @return EvaluationSession
     */
    public function setCorrectionEnd($correctionEnd)
    {
        $this->correctionEnd = $correctionEnd;

        return $this;
    }

    /**
     * Get correctionEnd
     *
     * @return \DateTime
     */
    public function getCorrectionEnd()
    {
        return $this->correctionEnd;
    }

    /**
     * Returns whether the candidate for this session is correcting the other session.
     *
     * @param $session EvaluationSession
     * @return bool
     */
    public function isCorrecting(EvaluationSession $session)
    {
        $given = $this->getCorrectionsGiven();
        if (!$given) {
            return false;
        }
        foreach ($given as $corr) {
            $persistedReceiverSession = $corr->getEvaluationSessionReceiver();
            if ($persistedReceiverSession->getId() === $session->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if all corrections given have been done.
     *
     * @return bool
     */
    public function allCorrectionsDone()
    {
        foreach ($this->getCorrectionsGiven() as $corr) {
            if (!$corr->isFinished()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns the score based on corrections given for this session.
     *
     * Returns -1 if not all corrections have been received.
     *
     * @return float
     */
    public function getGlobalScore()
    {
        $total = 0;
        $nb = 0;

        foreach ($this->getCorrectionsReceived() as $corr) {
            if ($corr->isFinished()) {
                $total += $corr->getScore();
                $nb++;
            } else {
                return -1;
            }
        }

        $total = $total / $nb;

        return floatval($total / $nb);
    }

    /**
     * Returns the score based on the amount of time used to complete this test.
     *
     * @return float
     */
    public function getTimeScore()
    {
        $testDuration = $this->getEvaluation()->getTest()->getDuration();

        $startTS = $this->getStart()->getTimestamp();
        $endTS = $this->getEnd()->getTimestamp();

        return 1 - (($endTS - $startTS) / $testDuration);
    }

    /**
     * Returns the score based on the amount of time used to complete this test.
     *
     * @return float
     */
    public function getLength()
    {
        $length = $this->getEvaluation()->getTest()->getDuration();

        $startTS = $this->getStart()->getTimestamp();
        $endTS = $this->getEnd()->getTimestamp();

        return ($endTS - $startTS);
    }


    /**
     * Set invite
     *
     * @param \Peerassess\CoreBundle\Entity\EvaluationInvite $invite
     * @return EvaluationSession
     */
    public function setInvite(\Peerassess\CoreBundle\Entity\EvaluationInvite $invite = null)
    {
        $this->invite = $invite;

        if (null !== $invite && $invite->getEvaluationSession() !== $this) {
            $invite->setEvaluationSession($this);
        }

        if (null !== $invite) {
            $this->setEvaluation($invite->getEvaluation());
        }

        return $this;
    }

    /**
     * Get invite
     *
     * @return \Peerassess\CoreBundle\Entity\EvaluationInvite
     */
    public function getInvite()
    {
        return $this->invite;
    }

    /**
     * Set resultFile
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile
     * @return EvaluationSession
     */
    public function setResultFile(UploadedFile $resultFile)
    {
        $this->resultFile = $resultFile;

        return $this;
    }

    /**
     * Get resultFile
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getResultFile()
    {
        return $this->resultFile;
    }

    /**
     * Get resultFile key
     *
     * @return string
     */
    public function getResultFileKey()
    {
        return 'tests/result/test-session-' . $this->getId() . '-result.zip';
    }

    /**
     * Set latestStartDate
     *
     * @param \DateTime $latestStartDate
     * @return EvaluationSession
     */
    public function setLatestStartDate($latestStartDate)
    {
        $this->latestStartDate = $latestStartDate;

        return $this;
    }

    /**
     * Get latestStartDate
     *
     * @return \DateTime
     */
    public function getLatestStartDate()
    {
        return $this->latestStartDate;
    }
}
