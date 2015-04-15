<?php

namespace Site\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GradeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('score', 'choice', array(
                'label' => false,
                'choices' => array(
                    '0' => 'correction.grade.bad',
                    '1' => 'correction.grade.ok',
                    '2' => 'correction.grade.good'
                ),
                'expanded' => true,
                'translation_domain' => 'SiteCandidateBundle'
            ))
            ->add('comment', 'textarea', array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'correction.grade.explain'
                ),
                'translation_domain' => 'SiteCandidateBundle'
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Peerassess\CoreBundle\Entity\Grade'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_corebundle_grade';
    }
}
