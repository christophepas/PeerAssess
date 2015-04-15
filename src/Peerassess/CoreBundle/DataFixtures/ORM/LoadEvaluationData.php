<?php

namespace Peerassess\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Peerassess\CoreBundle\Entity\Evaluation;

class LoadEvaluationData extends BaseFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $supervisor = $manager->getRepository('PeerassessCoreBundle:User')
            ->findOneByEmail('demo@peerassess.co');

        $tests = $manager->getRepository('PeerassessCoreBundle:Test')->findAll();

        foreach ($tests as $test) {
            $evaluation = new Evaluation();

            $evaluation->setSupervisor($supervisor->getSupervisor());
            $evaluation->setName($test->getName() . " - Evaluation");

            $test->addEvaluation($evaluation);

            $manager->persist($test);
        }

        $manager->flush();
    }

    /**
     *
     * @return number
     */
    public function getOrder()
    {
        return 201;
    }
}
