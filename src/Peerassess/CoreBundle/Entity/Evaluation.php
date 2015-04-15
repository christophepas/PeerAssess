<?php

namespace Peerassess\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Peerassess\CoreBundle\Entity\Status;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Evaluation
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Evaluation
{
    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Peerassess\CoreBundle\Entity\Supervisor",
     * inversedBy="evaluations", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    protected $supervisor;

    /**
     * @ORM\OneToMany(targetEntity="Peerassess\CoreBundle\Entity\EvaluationSession",
     * mappedBy="evaluation", cascade={"persist", "remove"})
     */
    protected $evaluationSessions;

    /**
     * @ORM\ManyToOne(targetEntity="Peerassess\CoreBundle\Entity\Test",
     * inversedBy="evaluations", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    protected $test;

    /**
     * Number of seconds available to start a test before being
     * assigned a grade of 0.
     *
     * Defaults to 1 day.
     *
     * @var integer
     *
     * @ORM\Column(name="testStartDuration", type="integer")
     */
    private $testStartDuration;

    /**
     * Number of seconds available to correct other people before being
     * assigned a grade of 0.
     *
     * Defaults to 3 days.
     *
     * @var integer
     *
     * @ORM\Column(name="correctionDuration", type="integer")
     */
    private $correctionDuration;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Peerassess\CoreBundle\Entity\EvaluationInvite",
     * mappedBy="evaluation", cascade={"persist", "remove"})
     */
    private $invites;

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
        $this->evaluationSessions = new ArrayCollection();
        $this->invites = new ArrayCollection();
        $this->testStartDuration = 60 * 60 * 24 * 1;
        $this->correctionDuration = 60 * 60 * 24 * 3;
    }


    /**
     * to string for direct display
     */
    public function __toString ()
    {
        return $this->name;
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
     * Set name
     *
     * @param string $name
     * @return Evaluation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set supervisor
     *
     * @param \Peerassess\CoreBundle\Entity\Supervisor $supervisor
     * @return Evaluation
     */
    public function setSupervisor(\Peerassess\CoreBundle\Entity\Supervisor $supervisor = null)
    {
        $this->supervisor = $supervisor;

        if (null !== $supervisor && !$supervisor->getEvaluations()->contains($this)) {
            $supervisor->addEvaluation($this);
        }

        return $this;
    }

    /**
     * Get supervisor
     *
     * @return \Peerassess\CoreBundle\Entity\Supervisor
     */
    public function getSupervisor()
    {
        return $this->supervisor;
    }

    /**
     * Add evaluationSessions
     *
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $evaluationSessions
     * @return Evaluation
     */
    public function addEvaluationSession(\Peerassess\CoreBundle\Entity\EvaluationSession $evaluationSessions)
    {
        $this->getEvaluationSessions()[] = $evaluationSessions;

        if ($evaluationSessions->getEvaluation() !== $this) {
            $evaluationSessions->setEvaluation($this);
        }

        return $this;
    }

    /**
     * Remove evaluationSessions
     *
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $evaluationSessions
     */
    public function removeEvaluationSession(\Peerassess\CoreBundle\Entity\EvaluationSession $evaluationSessions)
    {
        $this->evaluationSessions->removeElement($evaluationSessions);

        $evaluationSessions->setEvaluation(null);
    }

    /**
     * Get evaluationSessions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvaluationSessions()
    {
        return $this->evaluationSessions;
    }

    /**
     * Set test
     *
     * @param \Peerassess\CoreBundle\Entity\Test $test
     * @return Evaluation
     */
    public function setTest(\Peerassess\CoreBundle\Entity\Test $test = null)
    {
        $this->test = $test;

        if (null !== $test && !$test->getEvaluations()->contains($this)) {
            $test->addEvaluation($this);
        }

        return $this;
    }

    /**
     * Get test
     *
     * @return \Peerassess\CoreBundle\Entity\Test
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * Returns the average score of all candidates for this test.
     *
     * @return float
     */
    public function getAverage()
    {
        $total = 0;
        $nb = 0;
        foreach ($this->evaluationSessions as $evaluationSession) {
            if ($evaluationSession->getStatus() === Status::CLOSED) {
                $total += $evaluationSession->getGlobalScore();
                $nb++;
            }
        }
        if ($nb > 0) {
            return floatval($total / $nb);
        }
        return -1;
    }

    /**
     * Set testStartDuration
     *
     * @param integer $testStartDuration
     * @return Evaluation
     */
    public function setTestStartDuration($testStartDuration)
    {
        $this->testStartDuration = $testStartDuration;

        return $this;
    }

    /**
     * Get testStartDuration
     *
     * @return integer
     */
    public function getTestStartDuration()
    {
        return $this->testStartDuration;
    }

    /**
     * Set correctionDuration
     *
     * @param integer $correctionDuration
     * @return Evaluation
     */
    public function setCorrectionDuration($correctionDuration)
    {
        $this->correctionDuration = $correctionDuration;

        return $this;
    }

    /**
     * Get correctionDuration
     *
     * @return integer
     */
    public function getCorrectionDuration()
    {
        return $this->correctionDuration;
    }

    /**
     * Add invites
     *
     * @param \Peerassess\CoreBundle\Entity\EvaluationInvite $invites
     * @return Evaluation
     */
    public function addInvite(\Peerassess\CoreBundle\Entity\EvaluationInvite $invites)
    {
        $this->getInvites()[] = $invites;

        if ($invites->getEvaluation() !== $this) {
            $invites->setEvaluation($this);
        }

        return $this;
    }

    /**
     * Remove invites
     *
     * @param \Peerassess\CoreBundle\Entity\EvaluationInvite $invites
     */
    public function removeInvite(\Peerassess\CoreBundle\Entity\EvaluationInvite $invites)
    {
        $this->invites->removeElement($invites);

        $invites->setEvaluation(null);
    }

    /**
     * Get invites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvites()
    {
        return $this->invites;
    }

    /**
     * Set archivedDate
     *
     * @param \DateTime $archivedDate
     * @return Evaluation
     */
    public function setArchivedDate($archivedDate)
    {
        $this->archivedDate = $archivedDate;

        return $this;
    }

    /**
     * Get archivedDate
     *
     * @return \DateTime 
     */
    public function getArchivedDate()
    {
        return $this->archivedDate;
    }
}
