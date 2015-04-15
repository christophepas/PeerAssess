<?php

namespace Site\CandidateBundle\Controller;

use Peerassess\CoreBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Peerassess\CoreBundle\Entity\EvaluationSession;
use Peerassess\CoreBundle\Entity\Status;
use Peerassess\CoreBundle\Entity\Correction;
use Peerassess\CoreBundle\Entity\CorrectionStatus;
use Site\CandidateBundle\Form\ResultFileType;
use Site\CandidateBundle\Form\TestStartType;
use Site\CandidateBundle\Form\CorrectionType;
use Peerassess\CoreBundle\Entity\Evaluation;
use Peerassess\CoreBundle\Entity\Grade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Peerassess\CoreBundle\Service\EvaluationSessionManager;
use Site\CandidateBundle\Services\NotificationMailer;
use Peerassess\CoreBundle\Exception\EvaluationSession as ESE;

class EvaluationSessionController extends BaseController
{
    /**
     * @var \Peerassess\CoreBundle\Service\EvaluationSessionManager
     */
    private $sessions;

    /**
     * @var \Site\CandidateBundle\Services\NotificationMailer
     */
    private $notifier;

    /**
     * @var int
     */
    private $minCorrections;

    public function __construct(EvaluationSessionManager $sessions,
        NotificationMailer $notifier, $minCorrections)
    {
        $this->sessions = $sessions;
        $this->notifier = $notifier;
        $this->minCorrections = $minCorrections;
    }

    /**
     * Handle the submission of the "Start test" form.
     *
     * @ParamConverter("session", class="PeerassessCoreBundle:EvaluationSession")
     */
    public function startAction (EvaluationSession $session)
    {
        // Authorization.
        $this->denyAccessUnlessEqual(
            $this->getCandidate()->getId(),
            $session->getCandidate()->getId()
        );

        // Check if we are on the right page.
        if ($session->getStatus() !== Status::CREATED) {
            return $this->redirectToRightPage($session);
        }

        // Assign corrections if possible.
        $this->sessions->assignCorrections($session->getEvaluation());


        try {
            $this->sessions->checkStage($session);

            // Create the form with the "START" button.
            $form = $this->createForm(new TestStartType());

            // If the user clicked "START", move on to the next stage.
            $form->handleRequest($this->getRequest());
            if ($form->isValid()) {
                $this->sessions->nextStage($session);
                return $this->redirectToRightPage($session);
            }

            // The user has not clicked "START", so we show the form.
            return $this->render(
                'SiteCandidateBundle:EvaluationSession:start.html.twig',
                array(
                    'form' => $form->createView(),
                    'evaluationSession' => $session
                )
            );
        } catch (ESE\EarlyStartException $e) {
            return $this->render(
                'SiteCandidateBundle:EvaluationSession:wait.html.twig',
                array('evaluationSession' => $session)
            );
        }
    }

    /**
     * Handle the submission of the result file for a test session.
     *
     * @ParamConverter("session", class="PeerassessCoreBundle:EvaluationSession")
     */
    public function testAction (EvaluationSession $session)
    {
        // Authorization.
        $this->denyAccessUnlessEqual(
            $this->getCandidate()->getId(),
            $session->getCandidate()->getId()
        );

        // Check if we are on the right page.
        if ($session->getStatus() !== Status::RUNNING) {
            return $this->redirectToRightPage($session);
        }

        // Assign corrections if possible.
        $this->sessions->assignCorrections($session->getEvaluation());

        try {
            $this->sessions->checkStage($session);

            // Create the form that gets the result file.
            $form = $this->createForm(new ResultFileType(), $session);

            // If a file was submitted, save it and move on.
            $form->handleRequest($this->getRequest());
            if ($form->isValid()) {
                $this->sessions->nextStage($session);
                return $this->redirectToRightPage($session);
            }

            // No file was submitted, we'll show the form.
            return $this->render(
                'SiteCandidateBundle:EvaluationSession:test.html.twig',
                array(
                    'form' => $form->createView(),
                    'evaluationSession' => $session,
                )
            );
        } catch (ESE\LateSubmissionException $e) {
            $this->flashTrans(
                'notice',
                'test.lateSubmission',
                array(),
                'SiteCandidateBundle'
            );
            return $this->redirectToRightPage($session);
        }
    }

    /**
     * Check if the candidate is ready to correct others.
     *
     * @ParamConverter("session", class="PeerassessCoreBundle:EvaluationSession")
     */
    public function startCorrectionAction(EvaluationSession $session)
    {
        // Authorization.
        $this->denyAccessUnlessEqual(
            $this->getCandidate()->getId(),
            $session->getCandidate()->getId()
        );

        // Check if we are on the right page.
        if ($session->getStatus() !== Status::WAITINGTOCORRECT) {
            return $this->redirectToRightPage($session);
        }

        // Assign corrections if possible.
        $this->sessions->assignCorrections($session->getEvaluation());

        try {
            $this->sessions->checkStage($session);

            $this->sessions->nextStage($session);
            return $this->redirectToRightPage($session);
        } catch (ESE\CorrectionLackException $e) {
            return $this->render('SiteCandidateBundle:EvaluationSession:await_correction.html.twig');
        }
    }

    /**
     * Show available corrections.
     *
     * @ParamConverter("session", class="PeerassessCoreBundle:EvaluationSession")
     */
    public function correctionAction(EvaluationSession $session)
    {
        // Authorization.
        $this->denyAccessUnlessEqual(
            $this->getCandidate()->getId(),
            $session->getCandidate()->getId()
        );

        // Check if we are on the right page.
        if ($session->getStatus() !== Status::CORRECTING) {
            return $this->redirectToRightPage($session);
        }

        // Assign corrections if possible.
        $this->sessions->assignCorrections($session->getEvaluation());

        try {
            $this->sessions->checkStage($session);

            return $this->render(
                'SiteCandidateBundle:EvaluationSession:correction.html.twig',
                array('session' => $session)
            );
        } catch (ESE\LateCorrectionException $e) {
            $this->flashTrans(
                'notice',
                'test.lateCorrection',
                array(),
                'SiteCandidateBundle'
            );
            return $this->redirectToRightPage($session);
        }
    }

    /**
     * Submit a correction and check if more corrections are expected after.
     *
     * @ParamConverter("correction", class="PeerassessCoreBundle:Correction",
     * options={"id" = "correctionId"})
     * @ParamConverter("session", class="PeerassessCoreBundle:EvaluationSession")
     */
    public function correctionOneAction(Request $request, EvaluationSession $session, Correction $correction)
    {
        // Session ID and correction ID don't match.
        if ($correction->getEvaluationSessionGiver()->getId() !== $session->getId()) {
            throw $this->createNotFoundException();
        }

        // Authorization.
        $this->denyAccessUnlessEqual(
            $this->getCandidate()->getId(),
            $session->getCandidate()->getId()
        );

        // Check if we are on the right page.
        if ($session->getStatus() !== Status::CORRECTING) {
            return $this->redirectToRightPage($session);
        }

        // Assign corrections if possible.
        $this->sessions->assignCorrections($session->getEvaluation());

        try {
            $this->sessions->checkStage($session);

            // Create the form with the marking scheme for this correction.
            $form = $this->createForm(new CorrectionType(), $correction);

            // If the correction was submitted, save it.
            $form->handleRequest($request);
            if ($form->isValid()) {
                // TODO: move this to the manager
                $em = $this->getDoctrine()->getManager();
                $correction->finish();
                $em->persist($correction);
                $em->flush();

                // Move on. If this is impossible, it'll be handled down below.
                if ($session->allCorrectionsDone()) {
                    $this->sessions->nextStage($session);
                }
                return $this->redirectToRightPage($session);
            }

            return $this->render(
                'SiteCandidateBundle:EvaluationSession:correction_one.html.twig',
                array(
                    'session' => $session,
                    'correction' => $correction,
                    'form' => $form->createView(),
                )
            );
        } catch (ESE\LateCorrectionException $e) {
            $this->flashTrans(
                'notice',
                'test.lateCorrection',
                array(),
                'SiteCandidateBundle'
            );
            return $this->redirectToRightPage($session);
        }
    }

    /**
     * Get the result file for a given correction.
     *
     * @ParamConverter("correction", class="PeerassessCoreBundle:Correction",
     * options={"id" = "correctionId"})
     * @ParamConverter("session", class="PeerassessCoreBundle:EvaluationSession")
     */
    public function correctionOneFileAction(Request $request, EvaluationSession $session, Correction $correction)
    {
        // Session ID and correction ID don't match.
        if ($correction->getEvaluationSessionGiver()->getId() !== $session->getId()) {
            throw $this->createNotFoundException();
        }

        // Authorization.
        $this->denyAccessUnlessEqual(
            $this->getCandidate()->getId(),
            $session->getCandidate()->getId()
        );

        // Check if we are on the right page.
        if ($session->getStatus() !== Status::CORRECTING) {
            return $this->redirectToRightPage($session);
        }

        // Assign corrections if possible.
        $this->sessions->assignCorrections($session->getEvaluation());

        try {
            $this->sessions->checkStage($session);
        } catch (ESE\LateCorrectionException $e) {
            $this->flashTrans(
                'notice',
                'test.lateCorrection',
                array(),
                'SiteCandidateBundle'
            );
            return $this->redirectToRightPage($session);
        }

        return $this->sendFile(
            $session->getResultFileKey(),
            'application/zip',
            'result.zip'
        );
    }

    /**
     * See the "The end" message for a test session.
     *
     * @ParamConverter("session", class="PeerassessCoreBundle:EvaluationSession")
     */
    public function endAction (EvaluationSession $session)
    {
        // Authorization.
        $this->denyAccessUnlessEqual(
            $this->getCandidate()->getId(),
            $session->getCandidate()->getId()
        );

        // Check if we are on the right page.
        if ($session->getStatus() !== Status::CLOSED) {
            return $this->redirectToRightPage($session);
        }

        // Assign corrections if possible.
        $this->sessions->assignCorrections($session->getEvaluation());

        // Send a notification to the supervisor.
        // TODO: make sure this only happens once.
        $this->notifier->notifyEvaluationSessionEnd($session);

        return $this->render('SiteCandidateBundle:EvaluationSession:end.html.twig');
    }

    /**
     * Redirect to the right page depending on the current status of the
     * session.
     */
    private function redirectToRightPage($session)
    {
        switch ($session->getStatus()) {
            // Session has been created but not yet launched. Go to the
            // introduction page.
            case Status::CREATED:
                $route = 'site_candidate_evaluation-session_start';
                break;
            // The candidate is currently doing the test.
            case Status::RUNNING:
                $route = 'site_candidate_evaluation-session_test';
                break;
            // The candidate has finished the test and is waiting to be assigned
            // people to correct.
            case Status::WAITINGTOCORRECT:
                $route = 'site_candidate_evaluation-session_start_correction';
                break;

            case Status::CORRECTING:
                $route = 'site_candidate_evaluation-session_correction';
                break;
            // The test and correction phases are over.
            case Status::CLOSED:
                $route = 'site_candidate_evaluation-session_end';
                break;
        }

        return $this->redirectRoute($route, array(
            'token' => $session->getToken()
        ));
    }
}
