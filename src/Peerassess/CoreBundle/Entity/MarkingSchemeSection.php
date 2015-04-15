<?php

namespace Peerassess\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MarkingSchemeSection
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Peerassess\CoreBundle\Entity\MarkingSchemeSectionRepository")
 */
class MarkingSchemeSection
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
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="introduction", type="text")
     * @Assert\NotBlank()
     */
    private $introduction;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Peerassess\CoreBundle\Entity\MarkingSchemeSectionGrade",
     * mappedBy="markingSchemeSection", cascade={"persist","remove"})
     */
    private $grades;

    /**
     * @var \Peerassess\CoreBundle\Entity\MarkingScheme
     *
     * @ORM\ManyToOne(targetEntity="Peerassess\CoreBundle\Entity\MarkingScheme",
     * inversedBy="sections", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $markingScheme;


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
     * @return MarkingSchemeSection
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
     * @return MarkingSchemeSection
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
        $this->grades = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add grades
     *
     * @param \Peerassess\CoreBundle\Entity\MarkingSchemeSectionGrade $grades
     * @return MarkingSchemeSection
     */
    public function addGrade(\Peerassess\CoreBundle\Entity\MarkingSchemeSectionGrade $grades)
    {
        $this->getGrades()[] = $grades;

        if ($grades->getMarkingSchemeSection() !== $this) {
            $grades->setMarkingSchemeSection($this);
        }

        return $this;
    }

    /**
     * Remove grades
     *
     * @param \Peerassess\CoreBundle\Entity\MarkingSchemeSectionGrade $grades
     */
    public function removeGrade(\Peerassess\CoreBundle\Entity\MarkingSchemeSectionGrade $grades)
    {
        $this->grades->removeElement($grades);

        $grades->setMarkingSchemeSection(null);
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

    /**
     * Set markingScheme
     *
     * @param \Peerassess\CoreBundle\Entity\MarkingScheme $markingScheme
     * @return MarkingSchemeSection
     */
    public function setMarkingScheme(\Peerassess\CoreBundle\Entity\MarkingScheme $markingScheme = null)
    {
        $this->markingScheme = $markingScheme;

        if (null !== $markingScheme && !$markingScheme->getSections()->contains($this)) {
            $markingScheme->addSection($this);
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
}
