<?php

namespace Site\CandidateBundle\Services;

use Peerassess\CoreBundle\Entity\EvaluationSession;
use Peerassess\CoreBundle\Entity\EvaluationInvite;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Translation\Translator;

class NotificationMailer
{
    private $mailer;

    private $template;

    private $translator;

    public function __construct(\Swift_Mailer $mailer, TwigEngine $template,Translator $translator)
    {
        $this->mailer = $mailer;
        $this->template = $template;
        $this->translator = $translator;
    }

    /**
     * Notifies the supervisor when an evaluation session ends.
     *
     * @param $session \Peerassess\CoreBundle\Entity\EvaluationSession
     */
    public function notifyEvaluationSessionEnd(EvaluationSession $session)
    {
        $message = \Swift_Message::newInstance();

        $titre1 = $this->translator->trans('mail.end.title1',array(),'SiteCandidateBundle');
        $titre2 = $this->translator->trans('mail.end.title2',array(),'SiteCandidateBundle');

        $message->setSubject( $titre1.' '.$session->getCandidate().' '.$titre2);
        $message->setFrom('support@peerassess.co');
        $message->setTo($session->getEvaluation()->getSupervisor()->getUser()->getEmail());
        $message->setBody(
            $this->template->render(
                'SiteCandidateBundle:EvaluationSession:mail_end.html.twig',
                array('evaluationSession' => $session)
            ),
            'text/html'
        );

        $this->mailer->send($message);
    }

    /**
     * Notifies a candidate to start correcting others.
     *
     * @param $session \Peerassess\CoreBundle\Entity\EvaluationSession
     */
    public function notifyCorrectionStart(EvaluationSession $session)
    {
        $message = \Swift_Message::newInstance();

        $titre = $this->translator->trans('mail.correctionReady.title',array(),'SiteCandidateBundle');

        $message->setSubject($titre);
        $message->setFrom('support@peerassess.co');
        $message->setTo($session->getCandidate()->getUser()->getEmail());
        $message->setBody(
            $this->template->render(
                'SiteCandidateBundle:EvaluationSession:mail_correction_start.html.twig',
                array('evaluationSession' => $session)
            ),
            'text/html'
        );

        $this->mailer->send($message);
    }

    /**
     * Notifies a candidate that a supervisor sends an invitation for a test.
     *
     * @param $session \Peerassess\CoreBundle\Entity\EvaluationInvite
     */
    public function notifyTestInvite(EvaluationInvite $invite)
    {
        $message = \Swift_Message::newInstance();

        $titre = $this->translator->trans('mail.invite.title', array(), 'SiteCandidateBundle');

        $message->setSubject($titre);
        $message->setFrom('support@peerassess.co');
        $message->setTo($invite->getEmail());
        $message->setBody(
            $this->template->render('SiteCandidateBundle:EvaluationSession:mail_invite.html.twig', array(
                'invite' => $invite
            )),
            'text/html'
        );

        $this->mailer->send($message);
    }

    /**
     * Notify a supervisor that a candidate started late or did not start the
     * test.
     *
     * @param $session \Peerassess\CoreBundle\Entity\EvaluationInvite
     */
    public function notifyLateStart(EvaluationInvite $invite)
    {
        $message = \Swift_Message::newInstance();

        $titre = $this->translator->trans('mail.late_start.title', array(
            'email' => $invite->getEmail()
        ), 'SiteCandidateBundle');

        $message->setSubject($titre);
        $message->setFrom('support@peerassess.co');
        $message->setTo($invite->getSupervisor()->getUser()->getEmail());
        $message->setBody(
            $this->template->render('SiteCandidateBundle:EvaluationSession:mail_late_start.html.twig', array(
                'invite' => $invite
            )),
            'text/html'
        );

        $this->mailer->send($message);
    }
}
