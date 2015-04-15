<?php
namespace Site\UserBundle\Form\Type;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Peerassess\CoreBundle\Entity\UserType;

class RegistrationFormType extends BaseType
{

    private $router;

    public function __construct ($class, Router $router)
    {
        parent::__construct($class);
        $this->router = $router;
    }

    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder->setAction(
            $this->router->generate('fos_user_registration_register')
        );
        parent::buildForm($builder, $options);

        $builder->add('type','choice',array(
                'choices' => array(
                    UserType::CANDIDATE => 'entity.type.candidate',
                    UserType::SUPERVISOR => 'entity.type.supervisor'
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'data' => '',
                'translation_domain' => 'SiteUserBundle'
            ))
            ->add('submit', 'submit', array(
                'label' => 'registration.submit',
                'translation_domain' => 'FOSUserBundle'
            ))
            ->remove('username')
        ;
    }

    public function getName ()
    {
        return 'site_user_registration';
    }
}
