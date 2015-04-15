<?php
namespace Site\UserBundle\Form\Type;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class ProfileFormType extends BaseType
{
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->add('submit', 'submit', array(
                'label' => 'form.edit.submit',
                'translation_domain' => 'SiteUserBundle',
                'attr' => array(
                    'class' => 'btn btn-info btn-main'
                )
            ))
            ->remove('username');
    }

    public function getName ()
    {
        return 'site_user_profile';
    }

}
