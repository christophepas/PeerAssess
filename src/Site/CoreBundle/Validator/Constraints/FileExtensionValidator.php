<?php

namespace Site\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class FileExtensionValidator extends ConstraintValidator
{
	public function validate($file, Constraint $constraint)
	{
		if ($file->getClientOriginalExtension() !== $constraint->extension) {
			$this->context->buildViolation($constraint->message)
				->setParameter('%extension%', $constraint->extension)
				->addViolation();
		}
	}
}
