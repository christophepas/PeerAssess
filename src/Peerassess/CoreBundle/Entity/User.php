<?php

namespace Peerassess\CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Peerassess\CoreBundle\Entity\Image;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
{

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var \Peerassess\CoreBundle\Entity\Candidate
     *
     * @ORM\OneToOne(targetEntity="Peerassess\CoreBundle\Entity\Candidate",
     * mappedBy="user", cascade={"persist","remove"})
     * @Assert\NotNull()
     */
    private $candidate;

    /**
     * @var \Peerassess\CoreBundle\Entity\Supervisor
     *
     * @ORM\OneToOne(targetEntity="Peerassess\CoreBundle\Entity\Supervisor",
     * mappedBy="user", cascade={"persist","remove"})
     * @Assert\NotNull()
     */
    private $supervisor;

    /**
     * @var \Peerassess\CoreBundle\Entity\Image
     *
     * @ORM\OneToOne(targetEntity="Peerassess\CoreBundle\Entity\Image")
     */
    private $image;

    /**
     * @var boolean
     *
     * @ORM\Column(name="firstLogin", type="boolean")
     */
    private $firstLogin = true;

    /**
     * @var integer
     *
     * @ORM\Column(name="githubId", type="bigint", nullable=true)
     */
    private $githubId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="confirmed", type="boolean")
     */
    private $confirmed;

    public function __construct ()
    {
        parent::__construct();

        $supervisor = new Supervisor();
        $this->setSupervisor($supervisor);

        $candidate = new Candidate();
        $this->setCandidate($candidate);

        // TODO: actually use this variable ? -_-
        $this->setConfirmed(true);
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
     * Set type
     *
     * @param integer $type
     * @return User
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set confirmed
     *
     * @param boolean $confirmed
     * @return User
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * Get confirmed
     *
     * @return boolean
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Set firstLogin
     *
     * @param boolean $firstLogin
     * @return User
     */
    public function setFirstLogin($firstLogin)
    {
        $this->firstLogin = $firstLogin;

        return $this;
    }

    /**
     * Get firstLogin
     *
     * @return boolean
     */
    public function getFirstLogin()
    {
        return $this->firstLogin;
    }

    /**
     * Set candidate
     *
     * @param \Peerassess\CoreBundle\Entity\Candidate $candidate
     * @return User
     */
    public function setCandidate(\Peerassess\CoreBundle\Entity\Candidate $candidate = null)
    {
        $this->candidate = $candidate;

        if (null !== $candidate && $candidate->getUser() !== $this) {
            $candidate->setUser($this);
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
     * Set supervisor
     *
     * @param \Peerassess\CoreBundle\Entity\Supervisor $supervisor
     * @return User
     */
    public function setSupervisor(\Peerassess\CoreBundle\Entity\Supervisor $supervisor = null)
    {
        $this->supervisor = $supervisor;

        if (null !== $supervisor && $supervisor->getUser() !== $this) {
            $supervisor->setUser($this);
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

    public function setEmail($email)
    {
        parent::setEmail($email);
        parent::setUsername($email);

        return $this;
    }

    public function setEmailCanonical($emailCanonical)
    {
        parent::setEmailCanonical($emailCanonical);
        parent::setUsernameCanonical($emailCanonical);

        return $this;
    }


    /**
     * Set image
     *
     * @param \Peerassess\CoreBundle\Entity\Image $image
     * @return User
     */
    public function setImage(\Peerassess\CoreBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Peerassess\CoreBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set githubId
     *
     * @param integer $githubId
     * @return User
     */
    public function setGithubId($githubId)
    {
        $this->githubId = $githubId;

        return $this;
    }

    /**
     * Get githubId
     *
     * @return integer
     */
    public function getGithubId()
    {
        return $this->githubId;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function giveType(){
        if ($this->hasRole('ROLE_SUPERVISOR')) {
            $this->type = UserType::SUPERVISOR;
        } else if($this->hasRole('ROLE_CANDIDATE')) {
            $this->type = UserType::CANDIDATE;
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function giveRole(){
        if ($this->type == UserType::SUPERVISOR) {
            $this->addRole('ROLE_SUPERVISOR');
        } else if ($this->type == UserType::CANDIDATE) {
            $this->addRole('ROLE_CANDIDATE');
        }
    }

}
