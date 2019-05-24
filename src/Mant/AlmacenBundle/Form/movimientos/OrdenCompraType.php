<?php

namespace Mant\AlmacenBundle\Form\movimientos;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mant\AlmacenBundle\Entity\AlmacenRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Mant\AlmacenBundle\Entity\movimientos\ProveedorRepository;

class OrdenCompraType extends AbstractType
{
    
    private $deposito;
    
    public function __construct($deposito){
                                            $this->deposito = $deposito;
    }    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $deposito = $this->deposito;
        $builder
                ->add('movimiento', new MovimientoStockType('fcp'), array(
                    'data_class' => 'Mant\AlmacenBundle\Entity\movimientos\OrdenCompra',
                ))        
                ->add('almacenDestino', 'entity', array('class' => 'MantAlmacenBundle:Almacen','choices' => array($deposito->getId() => $deposito)))                
                ->add('proveedor', 'entity', array('class' => 'MantAlmacenBundle:movimientos\Proveedor',
                                            'query_builder' => function(ProveedorRepository $er){
                                                                                               
                                                                                                return $er->createQueryBuilder('u');
                                                                                             }))  
                ->add('numero')                   
                ->add('save', 'submit', array('label'=>'Agregar Articulos al Movimiento'));               
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mant\AlmacenBundle\Entity\movimientos\OrdenCompra'
        ));
        $resolver->setRequired('user');        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mant_almacenbundle_movimientos_ordencompra';
    }
}
