<?php

namespace Peerassess\CoreBundle\Entity;

abstract class CorrectionStatus
{

    const ASSIGNED = 0;

    const FINISHED = 1;

    const ERROR = 10;

    public static function getList ()
    {
        return array(
            self::ASSIGNED => "Assignée",
            self::FINISHED => "Faite",
            self::ERROR => "Erreur",
        );
    }

    public static function getStatus ($status)
    {
        if ($status === null) {
            return null;
        }

        $list = self::getList();
        return $list[$status];
    }
}
