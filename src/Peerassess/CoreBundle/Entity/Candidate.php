<?php

namespace Peerassess\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Candidate
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Peerassess\CoreBundle\Entity\CandidateRepository")
 */
class Candidate
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Peerassess\CoreBundle\Entity\EvaluationSession",
     * mappedBy="candidate", cascade={"persist", "remove"})
     */
    protected $evaluationSessions;

    /**
     * @ORM\OneToOne(targetEntity="Peerassess\CoreBundle\Entity\User",inversedBy="candidate",
     * cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $user;

    public function __construct ()
    {
        $this->evaluationSessions = new ArrayCollection();
    }

    /**
     * to string for direct display
     */
    public function __toString ()
    {
        if ($this->user !== null)
            return $this->user->__toString();
        else
            return 'Applicant';
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
     * Add evaluationSessions
     *
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $evaluationSessions
     * @return Candidate
     */
    public function addEvaluationSession(\Peerassess\CoreBundle\Entity\EvaluationSession $evaluationSessions)
    {
        $this->getEvaluationSessions()[] = $evaluationSessions;

        if ($evaluationSessions->getCandidate() !== $this) {
            $evaluationSessions->setCandidate($this);
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

        $evaluationSessions->setCandidate(null);
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
     * Set user
     *
     * @param \Peerassess\CoreBundle\Entity\User $user
     * @return Candidate
     */
    public function setUser(\Peerassess\CoreBundle\Entity\User $user = null)
    {
        $this->user = $user;

        if (null !== $user && $user->getCandidate() !== $this) {
            $user->setCandidate($this);
        }

        return $this;
    }

    /**
     * Get user
     *
     * @return \Peerassess\CoreBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
