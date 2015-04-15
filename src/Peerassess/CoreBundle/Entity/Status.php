<?php

namespace Peerassess\CoreBundle\Entity;

/**
 * The status of an EvaluationSession instance.
 */
abstract class Status
{

    /**
     * The session has been created by the supervisor.
     * The candidate has clicked on the invitation link
     * But he has not yet started working on it.
     */
    const CREATED = 0;

    /**
     * The candidate is currently working on the test.
     */
    const RUNNING = 4;

    /**
     * The candidate has submitted the solution and is waiting to be assigned
     * enough correctees.
     */
    const WAITINGTOCORRECT = 5;

    /**
     * The candidate is correcting others.
     */
    const CORRECTING = 6;

    /**
     * The candidate has corrected others.
     */
    const CLOSED = 8;

    /**
     * Some error occured during the session.
     */
    const ERROR = 10;

    /**
     * The candidate started too late or didn't start.
     */
    const LATE_START = 12;

    public static function getList()
    {
        return array(
            self::CREATED => "En attente",
            self::RUNNING => "En cours",
            self::WAITINGTOCORRECT => "En attente pour corriger",
            self::CORRECTING => "En correction",
            self::CLOSED => "Terminée",
            self::ERROR => "Erreur",
        );
    }

    public static function getStatus($status)
    {
        if ($status === null) {
            return null;
        }

        $list = self::getList();
        return $list[$status];
    }
}
