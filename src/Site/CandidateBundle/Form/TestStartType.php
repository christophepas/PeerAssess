<?php

namespace Site\CandidateBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TestStartType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', 'submit', array(
                'label' => 'start.btn',
                'attr' => array(
                    'class' => 'btn btn-main-2 btn-lg'
                ),
                'translation_domain' => 'SiteCandidateBundle'
            ))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_candidatebundle_test_start';
    }
}
