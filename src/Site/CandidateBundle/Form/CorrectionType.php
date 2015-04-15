<?php

namespace Site\CandidateBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Site\CoreBundle\Form\GradeType;

class CorrectionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('grades', 'collection', array(
                'type' => new GradeType(),
            ))
            ->add('comment', 'textarea')
            ->add('save', 'submit', array(
                'label' => 'correction.send',
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
            'data_class' => 'Peerassess\CoreBundle\Entity\Correction'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_candidatebundle_correction';
    }
}
