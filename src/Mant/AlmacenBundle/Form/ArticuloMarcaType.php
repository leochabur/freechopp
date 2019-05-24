<?php

namespace Mant\AlmacenBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mant\AlmacenBundle\Entity\Marca;
use Mant\AlmacenBundle\Entity\MarcaRepository;

class ArticuloMarcaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigoBarras')
            ->add('marca', 'entity', array('class' => 'MantAlmacenBundle:Marca',
                                            'query_builder' => function(MarcaRepository $er){
                                                                                                return $er->createQueryBuilder('u');
                                                                                             },
                                                'multiple' => false));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mant\AlmacenBundle\Entity\ArticuloMarca'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mant_almacenbundle_articulomarca';
    }
}
