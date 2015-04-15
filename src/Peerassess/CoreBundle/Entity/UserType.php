<?php

namespace Peerassess\CoreBundle\Entity;

abstract class UserType
{

    const CANDIDATE = 0;

    const SUPERVISOR = 1;

    const EDITOR = 2;

    public static function getType ($type)
    {
        return self::getList()[$type];
    }

    public static function getList ()
    {
        return array(
            self::CANDIDATE       => "Candidate",
            self::SUPERVISOR => "Supervisor",
            self::EDITOR    => "Editor",
        );
    }
}
