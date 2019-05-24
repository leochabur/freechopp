<?php

namespace Mant\AlmacenBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mant\AlmacenBundle\Entity\ClasificacionRepository;

class ArticuloBaseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo')
            ->add('descripcion')
            ->add('especificacion')
            ->add('unidad')            
            ->add('markup')              
            ->add('clasificacion', 'entity', array('class' => 'MantAlmacenBundle:Clasificacion',
                                            'query_builder' => function(ClasificacionRepository $er){
                                                                                                return $er->createQueryBuilder('u');
                                                                                             })) 
            ->add('articulosMarca', 'collection', array(
                                                    'type' => new ArticuloMarcaType(),
                                                    'options'      => array('label' => false),
                                                    'allow_add'    => true,
                                                    'by_reference' => false, 
                                                    'attr'=> array('class' => 'form-control')
                                            ))                                            
            ->add('save', 'submit', array('label'=>'Guardar Articulo'));           
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mant\AlmacenBundle\Entity\Articulo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mant_almacenbundle_articulobase';
    }
}
