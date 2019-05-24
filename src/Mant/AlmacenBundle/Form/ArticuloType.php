<?php

namespace Mant\AlmacenBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mant\AlmacenBundle\Entity\ClasificacionRepository;
use Mant\AlmacenBundle\Entity\ArticuloRepository;
use Mant\AlmacenBundle\Entity\ArticuloMarca;
use Mant\AlmacenBundle\Form\ArticuloMarcaType;

class ArticuloType extends AbstractType
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
            ->add('cantXUnidad', 'number', array('invalid_message' => 'Valor invalido!'))                                       
            ->add('clasificacion', 'entity', array('class' => 'MantAlmacenBundle:Clasificacion',
                                                'query_builder' => function(ClasificacionRepository $er){
                                                                                                return $er->createQueryBuilder('u');
                                                                                             }))   
            ->add('articuloBase', 'entity', array('class' => 'MantAlmacenBundle:Articulo',
                                                    'required'      => false,
                                                  'query_builder' => function(ArticuloRepository $er){
                                                                                                            return $er->createQueryBuilder('u')
                                                                                                                      ->orderBy('u.descripcion');
                                                                                                         }))                                                                                                                             
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
        return 'mant_almacenbundle_articulo';
    }
}
