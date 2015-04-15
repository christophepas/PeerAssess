<?php

namespace Site\SupervisorBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class EmailListValidator extends ConstraintValidator
{
	public function validate($emails, Constraint $constraint)
	{
		$validator = new EmailValidator();
		$validator->initialize($this->context);

		$constraint = new EmailConstraint();
		// TODO: use translation here
		$constraint->message = '{{ value }} is not a valid email address.';

		foreach ($emails as $e) {
			$validator->validate($e, $constraint);
		}

		if (count($emails) === 0) {
			$this->context->buildViolation($constraint->emptyMessage)
				->addViolation();
		}
	}
}
