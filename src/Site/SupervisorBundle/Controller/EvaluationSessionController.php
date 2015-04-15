<?php

namespace Site\SupervisorBundle\Controller;

use Peerassess\CoreBundle\Controller\BaseController;
use Peerassess\CoreBundle\Entity\EvaluationSession;
use Peerassess\CoreBundle\Entity\Status;
use Peerassess\CoreBundle\Entity\Candidate;
use Peerassess\CoreBundle\Entity\Supervisor;
use Peerassess\CoreBundle\Entity\User;
use Peerassess\CoreBundle\Entity\UserType;
use Peerassess\CoreBundle\Entity\Evaluation;
use Peerassess\CoreBundle\Entity\EvaluationInvite;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\Common\Collections\Collection;
use Site\SupervisorBundle\Form\EvaluationInviteListType;
use Site\SupervisorBundle\Form\EvaluationInviteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class EvaluationSessionController extends BaseController
{
	/**
     * @ParamConverter("evaluation", class="PeerassessCoreBundle:Evaluation")
     */
	public function addSingleAction(Evaluation $evaluation)
	{
		return $this->add(array($evaluation), true);
	}

	public function addMultipleAction()
	{
		$em = $this->getDoctrine()->getManager();

		$evaluations = $em->getRepository(
			'PeerassessCoreBundle:Evaluation'
		)->findBySupervisor($this->getSupervisor());

		return $this->add($evaluations, false);
	}

	private function add(array $evaluations, $single)
	{
		$inviteManager = $this->get('peerassess_core.evaluation_invite_manager');

		// The form that gathers evaluation invitation with the email.
		$form = $this->createForm(
			new EvaluationInviteListType($evaluations)
		);

		$form->handleRequest($this->get('request'));
		if ($form->isValid()) {
			// Save invites to new candidates.
			$emails = $form->get('emails')->getData();
			$evaluation = $form->get('evaluation')->getData();
			$scheduledDate = $form->get('scheduledDate')->getData();
			foreach ($emails as $email) {
				$invite = $inviteManager->create($evaluation, $email);
				$invite->setScheduledDate($scheduledDate);
				$inviteManager->send($invite);
			}

			// Get right confirmation message.
			// TODO: use the i18n module with automatic pluralization instead
			$this->flashTrans(
				'notice',
				count($emails) > 1 ? 'evaluationSession.add.successes' : 'evaluationSession.add.success',
				array(),
				'SiteSupervisorBundle'
			);
			return $this->redirectRoute(
				'site_supervisor_evaluation-session_list'
			);
		}

		return $this->render(
			'SiteSupervisorBundle:EvaluationSession:add_form.html.twig',
			array(
				'form' => $form->createView(),
				'evaluations' => $evaluations,
				'evaluation' => $single ? $evaluations[0] : null
			)
		);
	}

	/**
	* @ParamConverter("session", class="PeerassessCoreBundle:EvaluationSession")
	*/
	public function fileAction(EvaluationSession $session)
	{
		return $this->sendFile(
            $session->getResultFileKey(),
            'application/zip',
            'session.zip'
        );
	}

	/**
	 * Show an EvaluationSession
	 *
	 * @param EvaluationSessionId $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function showAction ($id)
	{
		// TODO: use ParamConverter here instead
		$em = $this->getDoctrine()->getManager();
		$evaluationSession = $em->getRepository(
			'PeerassessCoreBundle:EvaluationSession'
		)->loadEvaluation($id);

		switch ($evaluationSession->getStatus()) {
			case Status::CLOSED:
				$view = 'SiteSupervisorBundle:EvaluationSession:show_closed.html.twig';
				break;
			case Status::RUNNING:
				$view = 'SiteSupervisorBundle:EvaluationSession:show_running.html.twig';
				break;
			default:
				$view = 'SiteSupervisorBundle:EvaluationSession:show_started.html.twig';
				break;
		}

		return $this->render($view, array(
			'evaluationSession' => $evaluationSession,
		));
	}

	/**
	 * Show all the EvaluationSession for a supervisor
	 * optionnal specific status
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function listAction ($status)
	{
		switch ($status) {
			case "all":
				$status = null;
				break;
			case "created":
				$status = Status::CREATED;
				break;
			case "running":
				$status = Status::RUNNING;
				break;
			case "closed":
				$status = Status::CLOSED;
				break;
			default:
				throw new \LogicException('Unexpected session evaluation status: ' . $status);
				break;
		}

		$invites = $this->getDoctrine()->getManager()->getRepository(
			'PeerassessCoreBundle:EvaluationInvite'
		)->findBySupervisorAndStatus($this->getSupervisor(), $status);

		return $this->render(
			'SiteSupervisorBundle:EvaluationSession:list.html.twig',
			array(
				'invites' => $invites,
				'type' => Status::getStatus($status)
			)
		);
	}

	public function deleteAction (EvaluationInvite $invite)
	{
		$em = $this->getDoctrine()->getManager();

		$invite->setArchivedDate(new \DateTime());

		$em->persist($invite);
		$em->flush();

		$this->flashTrans(
			'success',
			'evaluationSession.archive.success',
			array('%email%' => $invite->getEmail()),
			'SiteSupervisorBundle'
		);

		return $this->redirectRoute('site_supervisor_evaluation-session_list');

	}
}
