<?php

namespace Mant\AlmacenBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticuloMarcaAlmacenType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sReal')
            ->add('ubicacion')
            ->add('save', 'submit', array('label'=>'Guardar Articulo'));            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mant\AlmacenBundle\Entity\ArticuloMarcaAlmacen'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mant_almacenbundle_articulomarcaalmacen';
    }
}
