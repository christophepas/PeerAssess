<?php

namespace Site\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class FileExtension extends Constraint
{
	// TODO: use translation here
	public $message = 'The file must have the "%extension%" extension.';

	/**
	 * The default extension to validate.
	 *
	 * @var string
	 */
	public $extension = 'txt';
}
