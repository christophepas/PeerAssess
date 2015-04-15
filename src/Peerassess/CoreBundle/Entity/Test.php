<?php

namespace Peerassess\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Site\CoreBundle\Validator\Constraints as CustomAssert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Test
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Test
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Peerassess\CoreBundle\Entity\Evaluation",
     * mappedBy="test", cascade={"persist","remove"})
     */
    protected $evaluations;

    /**
     * @ORM\Column(name="language", type="integer")
     */
    protected $language;

    /**
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $shortDescription;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $readMe;

    /**
     * The time available to complete the test, in seconds.
     *
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer")
     * @Assert\Range(min=3600)
     */
    private $duration = 0;

    /**
     * @ORM\OneToOne(targetEntity="Peerassess\CoreBundle\Entity\MarkingScheme",
     * mappedBy="test", cascade={"persist","remove"})
     * @Assert\NotNull()
     **/
    private $markingScheme;

    /**
     * @Assert\File()
     * @CustomAssert\FileExtension(extension="zip")
     */
    private $baseFile;

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
     * Constructor
     */
    public function __construct()
    {
        $this->evaluations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setMarkingScheme(new MarkingScheme());
    }

    /**
     * Set language
     *
     * @param integer $language
     * @return Test
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return integer
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Test
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Add evaluation
     *
     * @param \Peerassess\CoreBundle\Entity\Evaluation $evaluation
     * @return Test
     */
    public function addEvaluation(\Peerassess\CoreBundle\Entity\Evaluation $evaluations)
    {
        $this->getEvaluations()[] = $evaluations;

        if ($evaluations->getTest() !== $this) {
            $evaluations->setTest($this);
        }

        return $this;
    }

    /**
     * Remove evaluation
     *
     * @param \Peerassess\CoreBundle\Entity\Evaluation $evaluation
     */
    public function removeEvaluation(\Peerassess\CoreBundle\Entity\Evaluation $evaluations)
    {
        $evaluations->setTest(null);

        $this->evaluations->removeElement($evaluations);
    }

    /**
     * Get evaluations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvaluations()
    {
        if (!$this->evaluations) {
            $this->evaluations = new ArrayCollection();
        }

        return $this->evaluations;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Test
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
     * Set shortDescription
     *
     * @param string $shortDescription
     * @return Test
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set readMe
     *
     * @param string $readMe
     * @return Test
     */
    public function setReadMe($readMe)
    {
        $this->readMe = $readMe;

        return $this;
    }

    /**
     * Get readMe
     *
     * @return string
     */
    public function getReadMe()
    {
        return $this->readMe;
    }

    /**
     * Set markingScheme
     *
     * @param \Peerassess\CoreBundle\Entity\MarkingScheme $markingScheme
     * @return Test
     */
    public function setMarkingScheme(\Peerassess\CoreBundle\Entity\MarkingScheme $markingScheme = null)
    {
        $this->markingScheme = $markingScheme;

        if (null !== $markingScheme && $markingScheme->getTest() !== $this) {
            $markingScheme->setTest($this);
        }

        return $this;
    }

    /**
     * Get markingScheme
     *
     * @return \Peerassess\CoreBundle\Entity\MarkingScheme
     */
    public function getMarkingScheme()
    {
        return $this->markingScheme;
    }

    /**
     * Set baseFile
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile
     * @return Test
     */
    public function setBaseFile(UploadedFile $baseFile)
    {
        $this->baseFile = $baseFile;

        return $this;
    }

    /**
     * Get baseFile
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getBaseFile()
    {
        return $this->baseFile;
    }

    /**
     * Get baseFile key
     *
     * @return string
     */
    public function getBaseFileKey()
    {
        return 'tests/base/test-' . $this->getId() . '-base.zip';
    }
}
