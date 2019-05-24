<?php

namespace Mant\AlmacenBundle\Form\movimientos;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mant\AlmacenBundle\Entity\AlmacenRepository;

class TransferenciaStockType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   $user = $options['user'];
        $builder
            ->add('movimiento', new MovimientoStockType('trx'), array(
                'data_class' => 'Mant\AlmacenBundle\Entity\movimientos\TransferenciaStock',
            ))        
            ->add('almacenOrigen', 'entity', array('class' => 'MantAlmacenBundle:Almacen',
                                            'query_builder' => function(AlmacenRepository $er) use ($user){
                                                                                               
                                                                                                return $er->createQueryBuilder('u')
                                                                                                          ->where('u in (:depositos)')
                                                                                                          ->setParameter('depositos', $user->getDepositos()->toArray());
                                                                                             }))       
            ->add('almacenDestino')
            ->add('save', 'submit', array('label'=>'Agregar Articulos al Movimiento'));               
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mant\AlmacenBundle\Entity\movimientos\TransferenciaStock'
        ));
        $resolver->setRequired('user');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mant_almacenbundle_movimientos_transferenciastock';
    }
}
