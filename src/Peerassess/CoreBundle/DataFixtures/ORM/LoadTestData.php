<?php

namespace Peerassess\CoreBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Peerassess\CoreBundle\Entity\Test;
use Peerassess\CoreBundle\Entity\MarkingScheme;
use Peerassess\CoreBundle\Entity\MarkingSchemeSection;
use Peerassess\CoreBundle\Entity\MarkingSchemeSectionGrade;
use Peerassess\CoreBundle\Entity\Languages;

class LoadTestData extends BaseFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // Create a few tests.
        $testShortDescription = 'Construisez un système de prise de rendez-vous pour un cabinet de vétérinaire.';
        $testReadMe = file_get_contents(__DIR__ . '/../Data/TEST.md');

        $testManager = $this->container->get('peerassess_core.test_manager');

        $tests = array(
            Languages::SYMFONY => 'Test Symfony',
            Languages::PYTHON => 'Test Python',
            Languages::JAVA => 'Test Java',
            Languages::OBJECTIVEC => 'Test ObjectiveC',
        );

        foreach ($tests as $k => $t) {
            foreach (range(1, 10) as $i) {
                $test = new Test();
                $test->setLanguage($k);
                $test->setName($t . ' ' . $i);
                $test->setShortDescription($testShortDescription);
                $test->setReadMe($testReadMe);
                $test->setDuration(5400);
                $test->setMarkingScheme($this->getMarkingScheme($test));
                $test->setBaseFile($this->getTestFile());

                $testManager->create($test);
            }
        }
    }

    /**
     * Generate a dummy marking scheme for a given test.
     *
     * @return \Peerassess\CoreBundle\Entity\MarkingScheme
     */
    private function getMarkingScheme(Test $test)
    {
        $markingScheme = new MarkingScheme();
        $markingScheme->setTitle('Marking scheme - ' . $test->getName());
        $markingScheme->setIntroduction(
            "## What's this all about?" .
            "\n\n" .
            "You will now grade another fellow candidate on the " . $test->getName() . " test." .
            "This is **just as important as the coding itself**. Your ability to" .
            "**evaluate code, do code reviews and find bugs** before they get put" .
            "in production is very valuable and needed by the companies."
        );

        $section = new MarkingSchemeSection();
        $section->setTitle('Application structure');
        $section->setIntroduction(
            "Keep in mind that not everybody thinks alike. If the structure is" .
            "sensible, it's good! It doesn't need to be like what *you* would " .
            "have done."
        );
        $section->addGrade(
            MarkingSchemeSectionGrade::fromName('Entities have a repository')
        );
        $section->addGrade(
            MarkingSchemeSectionGrade::fromName('Views in the "view/" folder')
        );
        $section->addGrade(
            MarkingSchemeSectionGrade::fromName('Controllers are simple')
        );
        $markingScheme->addSection($section);

        $section = new MarkingSchemeSection();
        $section->setTitle('Solution');
        $section->setIntroduction(
            "Ideally, **candidates need to be able to deliver**. At the end of the " .
            "day, companies want a working product. That's what we'll check now"
        );
        $section->addGrade(
            MarkingSchemeSectionGrade::fromName('Calendar')
        );
        $section->addGrade(
            MarkingSchemeSectionGrade::fromName('Social login')
        );
        $markingScheme->addSection($section);

        return $markingScheme;
    }

    /**
     *
     * @return number
     */
    public function getOrder()
    {
        return 151;
    }
}
