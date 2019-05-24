<?php

namespace Mant\AlmacenBundle\Form\movimientos;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ItemMovimientoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cantidad')
            ->add('descripcion')
            ->add('precioUnitario')
            ->add('precioTotal')
            ->add('articulo')
            ->add('marcaArticulo')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mant_almacenbundle_movimientos_itemmovimiento';
    }
}
