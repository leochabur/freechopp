<?php

namespace Mant\AlmacenBundle\Form\movimientos;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mant\AlmacenBundle\Entity\AlmacenRepository;
use Mant\AlmacenBundle\Entity\gestion\EmpleadoRepository;
use Mant\AlmacenBundle\Entity\gestion\UnidadRepository;

class ConsumoStockType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $builder
            ->add('movimiento', new MovimientoStockType('cons'), array(
                'data_class' => 'Mant\AlmacenBundle\Entity\movimientos\SalidaStock',
            ))
            ->add('numeroOrdenTrabajo')            
            ->add('almacenOrigen', 'entity', array('class' => 'MantAlmacenBundle:Almacen',
                                            'query_builder' => function(AlmacenRepository $er) use ($user){
                                                                                               
                                                                                                return $er->createQueryBuilder('u')
                                                                                                          ->where('u in (:depositos)')
                                                                                                          ->setParameter('depositos', $user->getDepositos()->toArray());
                                                                                             }))    
            ->add('unidad', 'entity', array('class' => 'MantAlmacenBundle:gestion\Unidad',
                                            'required' => false,
                                            'query_builder' => function(UnidadRepository $er){
                                                                                               
                                                                                                return $er->createQueryBuilder('u')
                                                                                                          ->where('u.activo = :activo')
                                                                                                          ->orderBy('u.interno')
                                                                                                          ->setParameter('activo', true);
                                                                                             }))       
            ->add('empleado', 'entity', array('class' => 'MantAlmacenBundle:gestion\Empleado',
                                                'required' => false,
                                            'query_builder' => function(EmpleadoRepository $er){
                                                                                               
                                                                                                return $er->createQueryBuilder('u')
                                                                                                          ->where('u.activo = :activo')
                                                                                                          ->orderBy('u.apellido')
                                                                                                          ->setParameter('activo', true);
                                                                                             }))                                                                                                
            ->add('save', 'submit', array('label'=>'Agregar Articulos al Movimiento'));            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mant\AlmacenBundle\Entity\movimientos\ConsumoStock'
        ));
        $resolver->setRequired('user');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mant_almacenbundle_movimientos_consumostock';
    }
}
