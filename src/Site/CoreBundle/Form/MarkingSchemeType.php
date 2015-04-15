<?php

namespace Site\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MarkingSchemeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('introduction', 'textarea')
            ->add('sections', 'collection', array(
                'type' => new MarkingSchemeSectionType(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__section_name__',
                'by_reference' => false,
            ))
            ->add('save', 'submit', array('label' => 'Save marking scheme'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Peerassess\CoreBundle\Entity\MarkingScheme'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_corebundle_markingscheme';
    }
}
