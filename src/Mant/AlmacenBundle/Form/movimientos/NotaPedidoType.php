<?php

namespace Mant\AlmacenBundle\Form\movimientos;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mant\AlmacenBundle\Entity\movimientos\ClienteRepository;
use Mant\AlmacenBundle\Entity\movimientos\Cliente;

class NotaPedidoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('movimiento', new MovimientoStockType('npc'), array(
                    'data_class' => 'Mant\AlmacenBundle\Entity\movimientos\NotaPedido',
                ))           
            ->add('almacenDestino')
            ->add('cliente', 'entity', array('class' => 'MantAlmacenBundle:movimientos\Cliente',
                                            'query_builder' => function(ClienteRepository $er){
                                                                                                return $er->createQueryBuilder('c');
                                                                                             }))  
            ->add('save', 'submit', array('label'=>'Agregar Articulos al Movimiento'));                  
        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mant\AlmacenBundle\Entity\movimientos\NotaPedido'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mant_almacenbundle_movimientos_notapedido';
    }
}
