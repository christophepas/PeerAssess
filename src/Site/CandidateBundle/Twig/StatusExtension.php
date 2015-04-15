<?php

namespace Site\CandidateBundle\Twig;

use Peerassess\CoreBundle\Entity\Status;

/**
 * Extension Twig for check the status of an object Session (EvaluationSession, TestCaseSession, TestSession)
 * {% if session is <test> %}
 * <test> in [created, running, closed, error]
 * @author MOULIN
 *
 */
class StatusExtension extends \Twig_Extension
{

    public function getTests ()
    {
        return array(
                new \Twig_SimpleTest('created',
                        array(
                                $this,
                                'isStatusCreated'
                        )),
                new \Twig_SimpleTest('running',
                        array(
                                $this,
                                'isStatusRunning'
                        )),
                new \Twig_SimpleTest('waitingToCorrect',
                        array(
                                $this,
                                'isStatusWaitingToCorrect'
                        )),
                new \Twig_SimpleTest('correcting',
                        array(
                                $this,
                                'isStatusCorrecting'
                        )),
                new \Twig_SimpleTest('closed',
                        array(
                                $this,
                                'isStatusClosed'
                        )),
                new \Twig_SimpleTest('error',
                        array(
                                $this,
                                'isStatusError'
                        ))
        );
    }

    public function isStatusCreated ($session)
    {
        if (method_exists($session, 'getStatus') &&
                 $session->getStatus() === Status::CREATED)
        {
            return true;
        }
        return false;
    }

    public function isStatusRunning ($session)
    {
        if (method_exists($session, 'getStatus') &&
                 $session->getStatus() === Status::RUNNING)
        {
            return true;
        }
        return false;
    }

    public function isStatusWaitingToCorrect ($session)
    {
        if (method_exists($session, 'getStatus') &&
                 $session->getStatus() === Status::WAITINGTOCORRECT)
        {
            return true;
        }
        return false;
    }

    public function isStatusCorrecting ($session)
    {
        if (method_exists($session, 'getStatus') &&
                 $session->getStatus() === Status::CORRECTING)
        {
            return true;
        }
        return false;
    }

    public function isStatusClosed ($session)
    {
        if (method_exists($session, 'getStatus') &&
                 $session->getStatus() === Status::CLOSED)
        {
            return true;
        }
        return false;
    }

    public function isStatusError ($session)
    {
        if (method_exists($session, 'getStatus') &&
                 $session->getStatus() === Status::ERROR)
        {
            return true;
        }
        return false;
    }

    public function getName ()
    {
        return 'status';
    }
}
