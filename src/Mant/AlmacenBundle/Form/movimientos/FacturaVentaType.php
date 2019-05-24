<?php

namespace Mant\AlmacenBundle\Form\movimientos;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mant\AlmacenBundle\Entity\movimientos\ClienteRepository;

class FacturaVentaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
                ->add('movimiento', new MovimientoStockType('fvc'), array(
                    'data_class' => 'Mant\AlmacenBundle\Entity\movimientos\FacturaVenta',
                ))        
                ->add('almacenOrigen')                
                ->add('cliente', 'entity', array('class' => 'MantAlmacenBundle:movimientos\Cliente',
                                            'query_builder' => function(ClienteRepository $er){
                                                                                               
                                                                                                return $er->createQueryBuilder('u');
                                                                                             }))               
                ->add('save', 'submit', array('label'=>'Agregar Articulos al Movimiento'));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mant\AlmacenBundle\Entity\movimientos\FacturaVenta'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mant_almacenbundle_movimientos_facturaventa';
    }
}
