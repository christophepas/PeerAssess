<?php

namespace Peerassess\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Peerassess\CoreBundle\Entity\Grade;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Correction
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Correction
{
    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Peerassess\CoreBundle\Entity\EvaluationSession",
     * inversedBy="correctionsGiven", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    protected $evaluationSessionGiver;

    /**
     * @ORM\ManyToOne(targetEntity="Peerassess\CoreBundle\Entity\EvaluationSession",
     * inversedBy="correctionsReceived", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    protected $evaluationSessionReceiver;

    /**
     * @ORM\OneToMany(targetEntity="Peerassess\CoreBundle\Entity\Grade",
     * mappedBy="correction", cascade={"persist"})
     */
    protected $grades;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status = CorrectionStatus::ASSIGNED;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     * @Assert\NotBlank()
     */
    private $comment = '';


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
     * Set status
     *
     * @param integer $status
     * @return Correction
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
     * Set evaluationSessionGiver
     *
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $evaluationSessionGiver
     * @return Correction
     */
    public function setEvaluationSessionGiver(\Peerassess\CoreBundle\Entity\EvaluationSession $evaluationSessionGiver = null)
    {
        $this->evaluationSessionGiver = $evaluationSessionGiver;

        if (null !== $evaluationSessionGiver && !$evaluationSessionGiver->getCorrectionsGiven()->contains($this)) {
            $evaluationSessionGiver->addCorrectionsGiven($this);
        }

        return $this;
    }

    /**
     * Get evaluationSessionGiver
     *
     * @return \Peerassess\CoreBundle\Entity\EvaluationSession
     */
    public function getEvaluationSessionGiver()
    {
        return $this->evaluationSessionGiver;
    }

    /**
     * Set evaluationSessionReceiver
     *
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $evaluationSessionReceiver
     * @return Correction
     */
    public function setEvaluationSessionReceiver(\Peerassess\CoreBundle\Entity\EvaluationSession $evaluationSessionReceiver = null)
    {
        $this->evaluationSessionReceiver = $evaluationSessionReceiver;

        if (null !== $evaluationSessionReceiver && !$evaluationSessionReceiver->getCorrectionsReceived()->contains($this)) {
            $evaluationSessionReceiver->addCorrectionsReceived($this);

            // Add one grade for each one in the marking scheme
            // TODO: put this in the evaluation session manager ?
            foreach ($evaluationSessionReceiver->getEvaluation()->getTest()->getMarkingScheme()->getSections() as $section) {
                foreach ($section->getGrades() as $g) {
                    $this->addGrade(Grade::fromMarkingSchemeGrade($g));
                }
            }
        }


        return $this;
    }

    /**
     * Get evaluationSessionReceiver
     *
     * @return \Peerassess\CoreBundle\Entity\EvaluationSession
     */
    public function getEvaluationSessionReceiver()
    {
        return $this->evaluationSessionReceiver;
    }

    public function isFinished()
    {
        return $this->status === CorrectionStatus::FINISHED;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Add grades
     *
     * @param \Peerassess\CoreBundle\Entity\Grade $grades
     * @return Correction
     */
    public function addGrade(\Peerassess\CoreBundle\Entity\Grade $grades)
    {
        $this->getGrades()[] = $grades;

        if ($grades->getCorrection() !== $this) {
            $grades->setCorrection($this);
        }

        return $this;
    }

    /**
     * Remove grades
     *
     * @param \Peerassess\CoreBundle\Entity\Grade $grades
     */
    public function removeGrade(\Peerassess\CoreBundle\Entity\Grade $grades)
    {
        $this->getGrades()->removeElement($grades);

        $grades->setCorrection(null);
    }

    /**
     * Get grades
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGrades()
    {
        if (!$this->grades) {
            $this->grades = new ArrayCollection();
        }

        return $this->grades;
    }

    public function finish()
    {
        $this->setStatus(CorrectionStatus::FINISHED);
    }

    /**
     * Returns the score based on grades (0 <= score <= 1).
     *
     * @return float
     */
    public function getScore()
    {
        $total = 0;
        $nb = 0;

        foreach ($this->getGrades() as $grade) {
            switch ($grade->getScore()) {
                case 0:
                    $total += 0;
                    break;
                case 1:
                    $total += 0.5;
                    break;
                case 2:
                    $total += 1;
                    break;
                default:
                    throw new \Exception();
            }
            $nb++;
        }

        $total = $total / $nb;

        return $total;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Correction
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }
}
