<?php

namespace Site\CandidateBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ResultFileType extends AbstractType
{
    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder->add('resultFile', 'file', array(
                'required' => true,
                'attr' => array(
                    'class' => 'input-main'
                )
            ))
        	->add('save', 'submit', array(
                'label' => 'test.end.btn',
                'translation_domain' => 'SiteCandidateBundle'
            ));
    }

    /**
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions (OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Peerassess\CoreBundle\Entity\EvaluationSession'
        ));
    }

    /**
     *
     * @return string
     */
    public function getName ()
    {
        return 'site_candidatebundle_result_link';
    }
}
