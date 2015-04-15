<?php
namespace Site\SupervisorBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityRepository;

class EvaluationType extends AbstractType
{

    private $tests;

    public function __construct ($tests)
    {
        $this->tests = $tests;
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text',
                array(
                    'label' => false,
                    'attr' => array(
                        'class' => 'evaluationTitle'
                    )
                ))
            ->add('test', 'entity',
                array(
                    'label' => false,
                    'class' => 'PeerassessCoreBundle:Test',
                    'property' => 'name',
                    'choices' => $this->tests,
                    'expanded' => true
                ))
            ->add('save', 'submit',
                array(
                ));
    }

    /**
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions (OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
                array(
                        'data_class' => 'Peerassess\CoreBundle\Entity\Evaluation'
                ));
    }

    /**
     *
     * @return string
     */
    public function getName ()
    {
        return 'site_supervisorbundle_evaluation';
    }
}
