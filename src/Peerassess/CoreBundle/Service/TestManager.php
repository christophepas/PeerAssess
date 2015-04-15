<?php

namespace Peerassess\CoreBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Peerassess\CoreBundle\Entity\Test;
use Peerassess\CoreBundle\Entity\MarkingScheme;
use Doctrine\ORM\EntityManager;

class TestManager
{
    /**
     * @var FileManager
     */
    private $files;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param string $uploadDir
     */
    public function __construct(FileManager $files, EntityManager $em)
    {
        $this->files = $files;
        $this->em = $em;
    }

    public function create(Test $test)
    {
        // Save the test entity.
        $this->em->persist($test);
        $this->em->flush();

        // Save the ZIP file with the test ID as the name.
        try {
            $this->files->save(
                $test->getBaseFile(),
                $test->getBaseFileKey()
            );
        // If we can't save the file, we'll remove the database entry too.
        } catch (\Exception $e) {
            $this->em->remove($test);
            $this->em->flush();

            // Still throw the exception so we can be made aware of it.
            throw $e;
        }
    }
}
