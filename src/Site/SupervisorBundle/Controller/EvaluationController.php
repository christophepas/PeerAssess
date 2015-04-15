<?php

namespace Site\SupervisorBundle\Controller;

use Peerassess\CoreBundle\Controller\BaseController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Peerassess\CoreBundle\Entity\Candidate;
use Peerassess\CoreBundle\Entity\User;
use Peerassess\CoreBundle\Entity\EvaluationSession;
use Peerassess\CoreBundle\Entity\Evaluation;
use Site\SupervisorBundle\Form\EvaluationType;
use Site\SupervisorBundle\Form\EvaluationInviteListType;
use Peerassess\CoreBundle\Entity\Languages;

class EvaluationController extends BaseController
{

    /**
     * Creation of a new Evaluation
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction ()
    {
        $em = $this->getDoctrine()->getManager();

        $evaluation = new Evaluation();
        $evaluation->setSupervisor($this->getSupervisor());

        // Liste des tests accessibles par l'utilisateur
        $tests = $em->getRepository('PeerassessCoreBundle:Test')->findAll();
        $form = $this->createForm(new EvaluationType($tests), $evaluation);

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $em->persist($evaluation);
            $em->flush();

            $tr = $this->get('translator');
            $message = $tr->trans('evaluation.add.success',array(),'SiteSupervisorBundle');

            $this->get('session')->getFlashBag()->add('notice',$message);
            return $this->redirectRoute(
                'site_supervisor_evaluation-session_add-single',
                array(
                    'id' => $evaluation->getId()
                )
            );
        }

        return $this->render('SiteSupervisorBundle:Evaluation/Add:add.html.twig', array(
            'form' => $form->createView(),
            'languages' => Languages::getList(),
            'tests' => $tests
        ));
    }

    /**
     * Show all the Evaluation for a supervisor
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction ()
    {
        $em = $this->getDoctrine()->getManager();
        $evaluations = $em->getRepository('PeerassessCoreBundle:Evaluation')
            ->findBy(array(
                'supervisor' => $this->getSupervisor(),
                'archivedDate' => null
            ));

        return $this->render(
            'SiteSupervisorBundle:Evaluation:list.html.twig',
            array(
                'evaluations' => $evaluations
            )
        );
    }

    /**
     * Show the stats of an evaluation and all the tests taken on it.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction (Evaluation $evaluation)
    {
        return $this->render(
            'SiteSupervisorBundle:Evaluation:show.html.twig',
            array(
                'evaluation' => $evaluation
            )
        );
    }

    public function deleteAction (Evaluation $evaluation)
    {
        $this->denyAccessUnlessEqual(
            $this->getSupervisor()->getId(),
            $evaluation->getSupervisor()->getId()
        );

        $em = $this->getDoctrine()->getManager();
        $evaluation->setArchivedDate(new \DateTime());
        $em->persist($evaluation);
        $em->flush();

        return $this->redirectRoute('site_supervisor_evaluation_list');
    }

}
