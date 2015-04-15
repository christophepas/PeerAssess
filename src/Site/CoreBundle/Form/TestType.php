<?php

namespace Site\CoreBundle\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Peerassess\CoreBundle\Entity\Languages;

class TestType extends AbstractType
{
    const DURATION_1_HOUR  = 3600;
    const DURATION_2_HOURS = 7200;
    const DURATION_3_HOURS = 10800;
    const DURATION_1_DAY   = 86400;
    const DURATION_3_DAYS  = 259200;
    const DURATION_1_WEEK  = 604800;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('shortDescription', 'textarea')
            ->add('readMe', 'textarea')
            ->add('duration', 'choice', array(
                'choices' => array(
                    self::DURATION_1_HOUR  => '1 hour',
                    self::DURATION_2_HOURS => '2 hours',
                    self::DURATION_3_HOURS => '3 hours',
                    self::DURATION_1_DAY   => '1 day',
                    self::DURATION_3_DAYS  => '3 days',
                    self::DURATION_1_WEEK  => '1 week',
                )
            ))
            ->add('language', 'choice', array(
                'choices' => Languages::getList()
            ))
            ->add('baseFile', 'file')
            ->add('save', 'submit')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Peerassess\CoreBundle\Entity\Test',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_corebundle_test';
    }
}
