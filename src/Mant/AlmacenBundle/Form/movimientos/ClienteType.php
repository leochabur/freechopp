<?php

namespace Mant\AlmacenBundle\Form\movimientos;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClienteType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {      
        $builder->add('ente', new EnteComercialType(), array(
                    'data_class' => 'Mant\AlmacenBundle\Entity\movimientos\Cliente',
                ))      
            ->add('save', 'submit', array('label'=>'Guardar Cliente'));            
        ;
    }    

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mant\AlmacenBundle\Entity\movimientos\Cliente'
        ));
        $resolver->setRequired('user');  
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mant_almacenbundle_movimientos_cliente';
    }
}
