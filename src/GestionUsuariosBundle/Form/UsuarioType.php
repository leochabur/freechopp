<?php

namespace GestionUsuariosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options  --- usr
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('nombre')
            ->add('apellido')
            ->add('email', 'email')
            ->add('clave', 'password')
            ->add('role', 'choice', array('choices'=>array('ROLE_USER' => 'Usuario', 'ROLE_ADMIN' => 'Administrador', 'ROLE_PAX' => 'Pasajero', 'ROLE_SUPER_USUARIO' => 'Super Usuario')))
            ->add('save', 'submit', array('label'=>'Guardar Usuario'));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GestionUsuariosBundle\Entity\Usuario'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gestionusuariosbundle_usuario';
    }
}
