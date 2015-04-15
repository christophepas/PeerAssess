<?php

namespace Site\SupervisorBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EmailListToArrayTransformer implements DataTransformerInterface
{
    /**
     * @param string $emails
     * @return array
     * @throws TransformationFailedException
     */
    public function reverseTransform($emails)
    {
        try {
			$emails = imap_rfc822_parse_adrlist($emails, null);
		} catch (\Exception $e) {
            throw new TransformationFailedException('Invalid email list.');
        }

        $emails = array_map(function($one) {
            return $one->mailbox . '@' . $one->host;
        }, $emails);

        return $emails;
    }

    /**
     * @param array $emails
     * @return string
     * @throws TransformationFailedException
     */
    public function transform($emails)
    {
        return join(', ', $emails ?: array());
    }
}
