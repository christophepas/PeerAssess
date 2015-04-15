<?php

namespace Peerassess\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Peerassess\CoreBundle\Entity\Supervisor;
use Peerassess\CoreBundle\Entity\EvaluationSession;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EvaluationInvite
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Peerassess\CoreBundle\Entity\EvaluationInviteRepository")
 */
class EvaluationInvite
{
    /**
     * Default number of bytes used to generate the token.
     *
     * @const integer
     */
    const DEFAULT_TOKEN_LENGTH = 32;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="scheduledDate", type="datetime")
     */
    private $scheduledDate;

    /**
     * @var \Peerassess\CoreBundle\Entity\Evaluation
     *
     * @ORM\ManyToOne(targetEntity="Peerassess\CoreBundle\Entity\Evaluation",
     * inversedBy="invites", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $evaluation;

    /**
     * A random number generator.
     *
     * Used to generate the evaluation token.
     *
     * @var \Symfony\Component\Security\Core\Util\SecureRandom
     */
    private $rng;

    /**
     * The evaluation session that resulted from this invite, if any.
     *
     * @var \Peerassess\CoreBundle\Entity\EvaluationSession
     *
     * @ORM\OneToOne(targetEntity="Peerassess\CoreBundle\Entity\EvaluationSession",
     * mappedBy="invite", cascade={"persist"})
     **/
    private $evaluationSession;

    /**
     * The number of random bytes used to generate the token.
     *
     * @var integer
     */
    private $tokenLength = self::DEFAULT_TOKEN_LENGTH;

    /**
     * The date at which this was archived, or NULL.
     *
     * @var \DateTime
     *
     * @ORM\Column(name="archivedDate", type="datetime", nullable=true)
     */
    private $archivedDate;

    /**
     * The date at which this was created.
     *
     * @var \DateTime
     *
     * @ORM\Column(name="createdDate", type="datetime")
     */
    private $createdDate;

    public function __construct()
    {
        $this->rng = new SecureRandom();
        $this->generateToken();
    }

    /**
     * Sets a new random token.
     *
     */
    public function generateToken()
    {
        $this->setToken(bin2hex($this->rng->nextBytes($this->tokenLength)));
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
     * Set token
     *
     * @param string $token
     * @return EvaluationInvite
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
     * Set email
     *
     * @param string $email
     * @return EvaluationInvite
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set scheduledDate
     *
     * @param \DateTime $scheduledDate
     * @return EvaluationInvite
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
     * Set evaluation
     *
     * @param \Peerassess\CoreBundle\Entity\Evaluation $evaluation
     * @return EvaluationInvite
     */
    public function setEvaluation(\Peerassess\CoreBundle\Entity\Evaluation $evaluation = null)
    {
        $this->evaluation = $evaluation;

        if (null !== $evaluation && !$evaluation->getInvites()->contains($this)) {
            $evaluation->addInvite($this);
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
     * Set evaluationSession
     *
     * @param \Peerassess\CoreBundle\Entity\EvaluationSession $evaluationSession
     * @return EvaluationInvite
     */
    public function setEvaluationSession(\Peerassess\CoreBundle\Entity\EvaluationSession $evaluationSession = null)
    {
        $this->evaluationSession = $evaluationSession;

        if (null !== $evaluationSession && $evaluationSession->getInvite() !== $this) {
            $evaluationSession->setInvite($this);
        }

        return $this;
    }

    /**
     * Get evaluationSession
     *
     * @return \Peerassess\CoreBundle\Entity\EvaluationSession
     */
    public function getEvaluationSession()
    {
        return $this->evaluationSession;
    }

    /**
     * @return boolean
     */
    public function isClaimed()
    {
        return null !== $this->getEvaluationSession();
    }

    /**
     * Set archivedDate
     *
     * @param \DateTime $archivedDate
     * @return EvaluationInvite
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

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return EvaluationInvite
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime 
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }
}
