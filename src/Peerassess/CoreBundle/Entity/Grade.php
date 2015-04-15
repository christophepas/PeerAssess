<?php

namespace Peerassess\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Peerassess\CoreBundle\Entity\MarkingSchemeSectionGrade;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Grade
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Peerassess\CoreBundle\Entity\GradeRepository")
 */
class Grade
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
     * A grade between 0 and 2 (three possible choices).
     *
     * @var integer
     *
     * @Assert\Range(min=0, max=2)
     * @ORM\Column(name="score", type="integer", nullable=true)
     */
    private $score;

    /**
     * @ORM\ManyToOne(targetEntity="Peerassess\CoreBundle\Entity\Correction",
     * inversedBy="grades", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $correction;

    /**
     * @ORM\ManyToOne(targetEntity="Peerassess\CoreBundle\Entity\MarkingSchemeSectionGrade",
     * inversedBy="grades", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $markingSchemeGrade;

    /**
     * @ORM\Column(name="comment", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $comment;

    public static function fromMarkingSchemeGrade(MarkingSchemeSectionGrade $markingSchemeGrade)
    {
        $grade = new self();
        $grade->setMarkingSchemeGrade($markingSchemeGrade);
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
     * Set score
     *
     * @param integer $score
     * @return Grade
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set correction
     *
     * @param \Peerassess\CoreBundle\Entity\Correction $correction
     * @return Grade
     */
    public function setCorrection(\Peerassess\CoreBundle\Entity\Correction $correction = null)
    {
        $this->correction = $correction;

        if (null !== $correction && !$correction->getGrades()->contains($this)) {
            $correction->addGrade($this);
        }

        return $this;
    }

    /**
     * Get correction
     *
     * @return \Peerassess\CoreBundle\Entity\Correction
     */
    public function getCorrection()
    {
        return $this->correction;
    }

    /**
     * Set markingSchemeGrade
     *
     * @param \Peerassess\CoreBundle\Entity\MarkingSchemeSectionGrade $markingSchemeGrade
     * @return Grade
     */
    public function setMarkingSchemeGrade(\Peerassess\CoreBundle\Entity\MarkingSchemeSectionGrade $markingSchemeGrade = null)
    {
        $this->markingSchemeGrade = $markingSchemeGrade;

        if (null !== $markingSchemeGrade && !$markingSchemeGrade->getGrades()->contains($this)) {
            $markingSchemeGrade->addGrade($this);
        }

        return $this;
    }

    /**
     * Get markingSchemeGrade
     *
     * @return \Peerassess\CoreBundle\Entity\MarkingSchemeSectionGrade
     */
    public function getMarkingSchemeGrade()
    {
        return $this->markingSchemeGrade;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Grade
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
