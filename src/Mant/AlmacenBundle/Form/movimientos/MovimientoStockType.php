<?php

namespace Mant\AlmacenBundle\Form\movimientos;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mant\AlmacenBundle\Form\movimientos\ItemMovimientoType;
use Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento;
use Mant\AlmacenBundle\Entity\movimientos\ConceptoMovimientoRepository;


class MovimientoStockType extends AbstractType
{
    private $type;
    public function __construct($tipo){
       $this->type = $tipo;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $type = $this->type;
        $builder
            ->add('fecha', 'date', array('widget' => 'single_text'))
            ->add('conceptoEntrada', 'entity', array('class' => 'MantAlmacenBundle:movimientos\ConceptoMovimiento',
                                            'query_builder' => function(ConceptoMovimientoRepository $er) use ($type){
                                                                                               
                                                                                                return $er->createQueryBuilder('u')
                                                                                                          ->where('u.movAfectado = ?1')
                                                                                                          ->setParameter(1, $type);
                                                                                             }));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
       /* $resolver->setDefaults(array(
            'data_class' => 'Mant\AlmacenBundle\Entity\movimientos\MovimientoStock'
        ));
        */
        $resolver->setDefaults(array(
            'virtual' => true,
        ));        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mant_almacenbundle_movimientos_movimientostock';
    }
}
