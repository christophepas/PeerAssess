<?php

namespace Site\SupervisorBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EmailList extends Constraint
{
    // TODO: use translation here
    public $unknownErrorMessage = 'Unknown error.';
    public $emailMessage = 'The email "%email%" is invalid.';
    public $emptyMessage = 'The email list cannot be empty.';
}
