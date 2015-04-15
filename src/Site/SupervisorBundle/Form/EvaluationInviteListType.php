<?php

namespace Site\SupervisorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Peerassess\CoreBundle\Entity\Evaluation;
use Peerassess\CoreBundle\Entity\EvaluationInvite;
use Peerassess\CoreBundle\Entity\Supervisor;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Peerassess\CoreBundle\Service\EvaluationInviteManager;
use Site\SupervisorBundle\Validator\Constraints\EmailList as EmailListConstraint;
use Site\SupervisorBundle\Form\DataTransformer\EmailListToArrayTransformer;
use Symfony\Component\Validator\Constraints\DateTime as DateTimeConstraint;

class EvaluationInviteListType extends AbstractType
{
    /**
     * Evaluations that can be chosen for the invites.
     *
     * @var array
     */
    private $evaluations;

    public function __construct(array $evaluations)
    {
        $this->evaluations = $evaluations;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $emails = $builder->create('emails', 'textarea', array(
            'label' => false,
            'constraints' => array(
                new EmailListConstraint()
            )
        ));
        $emails->addModelTransformer(new EmailListToArrayTransformer());

        $builder
            ->add($emails)
            ->add('evaluation', 'entity', array(
                'label' => false,
                'class' => 'PeerassessCoreBundle:Evaluation',
                'property' => 'name',
                'choices' => $this->evaluations,
            ))
            ->add('scheduledDate', 'datetime', array(
                'label' => false,
                'date_widget' => 'single_text',
                'input' => 'datetime',
                'time_widget' => 'single_text',
                'data' => new \DateTime(),
                'constraints' => array(
                    new DateTimeConstraint()
                )
            ))
            ->add('save', 'submit', array(
                'label' => 'evaluationSession.add.send',
                'translation_domain' => 'SiteSupervisorBundle'
            ))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_supervisorbundle_evaluationinvite';
    }
}
