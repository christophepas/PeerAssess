<?php

namespace Peerassess\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MarkingSchemeSectionGrade
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Peerassess\CoreBundle\Entity\MarkingSchemeSectionGradeRepository")
 */
class MarkingSchemeSectionGrade
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var \Peerassess\CoreBundle\Entity\MarkingSchemeSection
     *
     * @ORM\ManyToOne(targetEntity="Peerassess\CoreBundle\Entity\MarkingSchemeSection",
     * inversedBy="grades", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $markingSchemeSection;

    /**
     * Actual grades that have been given by assessers.
     *
     * The class of this file describes what this grade is. But the "Grade"
     * class actually gives a concrete score.
     *
     * @ORM\OneToMany(targetEntity="Peerassess\CoreBundle\Entity\Grade",
     * mappedBy="markingSchemeGrade", cascade={"persist", "remove"})
     */
    private $grades;

    public static function fromName($name)
    {
        $grade = new self();
        $grade->setName($name);
        return $grade;
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
     * @return MarkingSchemeSectionGrade
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
     * Set markingSchemeSection
     *
     * @param \Peerassess\CoreBundle\Entity\MarkingSchemeSection $markingSchemeSection
     * @return MarkingSchemeSectionGrade
     */
    public function setMarkingSchemeSection(\Peerassess\CoreBundle\Entity\MarkingSchemeSection $markingSchemeSection = null)
    {
        $this->markingSchemeSection = $markingSchemeSection;

        if (null !== $markingSchemeSection && !$markingSchemeSection->getGrades()->contains($this)) {
            $markingSchemeSection->addGrade($this);
        }

        return $this;
    }

    /**
     * Get markingSchemeSection
     *
     * @return \Peerassess\CoreBundle\Entity\MarkingSchemeSection
     */
    public function getMarkingSchemeSection()
    {
        return $this->markingSchemeSection;
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
     * @param \Peerassess\CoreBundle\Entity\Grade $grades
     * @return MarkingSchemeSectionGrade
     */
    public function addGrade(\Peerassess\CoreBundle\Entity\Grade $grades)
    {
        $this->getGrades()[] = $grades;

        if ($grades->getMarkingSchemeGrade() !== $this) {
            $grades->setMarkingSchemeGrade($this);
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
        $this->grades->removeElement($grades);

        $grades->setMarkingSchemeGrade(null);
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
}
