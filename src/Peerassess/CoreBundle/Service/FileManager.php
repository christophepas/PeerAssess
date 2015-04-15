<?php

namespace Peerassess\CoreBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager
{
    /**
     * @var string
     */
    private $uploadDir;

    /**
     * @param string $uploadDir
     */
    public function __construct($uploadDir)
    {
        $this->uploadDir = rtrim($uploadDir, '/');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param string $key
     */
    public function save(UploadedFile $file, $key)
    {
        $parts = $this->getParts($key);

        $file->move(
            $this->uploadDir . '/' . ltrim($parts['dirname'], '/'),
            $parts['filename']
        );
    }

    /**
     * @param string $key
     */
    public function retrieve($key)
    {
        $contents = file_get_contents($this->uploadDir . '/' . ltrim($key, '/'));

        if ($contents === false) {
            throw new \Exception('Could not read file ' . $key . '.');
        }

        return $contents;
    }

    /**
     * @param string $key
     */
    private function getParts($key)
    {
        return array(
            'filename' => basename($key),
            'dirname'  => dirname($key)
        );
    }
}
