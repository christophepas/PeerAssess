<?php

namespace Peerassess\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MarkingScheme
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Peerassess\CoreBundle\Entity\MarkingSchemeRepository")
 */
class MarkingScheme
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotNull()
     */
    private $title = '';

    /**
     * @var string
     *
     * @ORM\Column(name="introduction", type="text")
     * @Assert\NotNull()
     */
    private $introduction = '';

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Peerassess\CoreBundle\Entity\MarkingSchemeSection",
     * mappedBy="markingScheme", cascade={"persist","remove"})
     */
    private $sections;

    /**
     * @ORM\OneToOne(targetEntity="Peerassess\CoreBundle\Entity\Test",
     * inversedBy="markingScheme", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     **/
    private $test;


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
     * Set title
     *
     * @param string $title
     * @return MarkingScheme
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set introduction
     *
     * @param string $introduction
     * @return MarkingScheme
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * Get introduction
     *
     * @return string
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sections = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add sections
     *
     * @param \Peerassess\CoreBundle\Entity\MarkingSchemeSection $sections
     * @return MarkingScheme
     */
    public function addSection(\Peerassess\CoreBundle\Entity\MarkingSchemeSection $sections)
    {
        $this->getSections()[] = $sections;

        if ($sections->getMarkingScheme() !== $this) {
            $sections->setMarkingScheme($this);
        }

        return $this;
    }

    /**
     * Remove sections
     *
     * @param \Peerassess\CoreBundle\Entity\MarkingSchemeSection $sections
     */
    public function removeSection(\Peerassess\CoreBundle\Entity\MarkingSchemeSection $sections)
    {
        $this->sections->removeElement($sections);

        $sections->setMarkingScheme(null);
    }

    /**
     * Get sections
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSections()
    {
        if (!$this->sections) {
            $this->sections = new ArrayCollection();
        }

        return $this->sections;
    }

    /**
     * Set test
     *
     * @param \Peerassess\CoreBundle\Entity\Test $test
     * @return MarkingScheme
     */
    public function setTest(\Peerassess\CoreBundle\Entity\Test $test = null)
    {
        $this->test = $test;

        if (null !== $test && $test->getMarkingScheme() !== $this) {
            $test->setMarkingScheme($this);
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
}
