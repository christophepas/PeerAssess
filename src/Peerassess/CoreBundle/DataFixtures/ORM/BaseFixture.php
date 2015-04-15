<?php

namespace Peerassess\CoreBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseFixture implements
    FixtureInterface, OrderedFixtureInterface,
    ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        $this->container->enterScope('request');
        $this->container->set('request', new Request(), 'request');
    }

    /**
     * {@inheritDoc}
     */
    abstract public function load(ObjectManager $manager);

    /**
     *
     * @return number
     */
    abstract public function getOrder();

    /**
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    protected function getTestFile()
    {
        $dir = __DIR__ . '/../Data';

        // Make a copy before moving it so we can do this several times.
        if (!copy($dir . '/base.zip', $dir . '/base-copy.zip')) {
            throw new Exception('Could not copy test file.');
        }

        return new UploadedFile(
            $dir . '/base-copy.zip',
            'base.zip',
            null, // mime-type
            null, // size
            null, // error
            true  // test
        );
    }
}
