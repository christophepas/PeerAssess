<?php

namespace Peerassess\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Supervisor
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Peerassess\CoreBundle\Entity\SupervisorRepository")
 */
class Supervisor
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Peerassess\CoreBundle\Entity\Evaluation",
     * mappedBy="supervisor", cascade={"persist", "remove"})
     */
    protected $evaluations;

    /**
     * @ORM\OneToOne(targetEntity="Peerassess\CoreBundle\Entity\User",inversedBy="supervisor",
     * cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $user;

    public function __construct ()
    {
        $this->evaluations = new ArrayCollection();
    }

    /**
     * to string for direct display
     */
    public function __toString ()
    {
        if ($this->user !== null)
            return $this->user->__toString();
        else
            return 'Supervisor';
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
     * Add evaluations
     *
     * @param \Peerassess\CoreBundle\Entity\Evaluation $evaluations
     * @return Supervisor
     */
    public function addEvaluation(\Peerassess\CoreBundle\Entity\Evaluation $evaluations)
    {
        $this->getEvaluations()[] = $evaluations;

        if ($evaluations->getSupervisor() !== $this) {
            $evaluations->setSupervisor($this);
        }

        return $this;
    }

    /**
     * Remove evaluations
     *
     * @param \Peerassess\CoreBundle\Entity\Evaluation $evaluations
     */
    public function removeEvaluation(\Peerassess\CoreBundle\Entity\Evaluation $evaluations)
    {
        $this->evaluations->removeElement($evaluations);

        $evaluations->setSupervisor(null);
    }

    /**
     * Get evaluations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvaluations()
    {
        return $this->evaluations;
    }

    /**
     * Set user
     *
     * @param \Peerassess\CoreBundle\Entity\User $user
     * @return Supervisor
     */
    public function setUser(\Peerassess\CoreBundle\Entity\User $user = null)
    {
        $this->user = $user;

        if (null !== $user && $user->getSupervisor() !== $this) {
            $user->setSupervisor($this);
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
