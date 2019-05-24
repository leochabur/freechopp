<?php

namespace Mant\AlmacenBundle\Form\finanzas;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Mant\AlmacenBundle\Entity\movimientos\ProveedorRepository;

class FacturaProveedorType extends AbstractType
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
            ->add('fechaFactura', 'date', array('widget' => 'single_text'))
            ->add('puntoVenta')
            ->add('almacen', 'entity', array('class' => 'MantAlmacenBundle:Almacen','choices' => array($deposito->getId() => $deposito)))
            ->add('proveedor', 'entity', 
                    array('class' => 'MantAlmacenBundle:movimientos\Proveedor',
                          'query_builder' => function(ProveedorRepository $er) use ($deposito){
                                                                                return $er->createQueryBuilder('p')
                                                                                          ->where('p.deposito = :deposito')
                                                                                          ->setParameter('deposito', $deposito);                                                                                            
                                                                              })
                          )              
            ->add('numeroFactura')
            ->add('letraFactura', 'choice', array('choices'  => array('A' => 'A','B' => 'B','C' => 'C',),'choices_as_values' => true))
            ->add('importeNeto')
            ->add('importeIva')
            ->add('importeTotal')
            ->add('back', 'submit', array('label'=>'<< Retroceder'))                
            ->add('next', 'submit', array('label'=>'Siguiente >>'));            
        ;
        
       /* $builder->addEventListener(
                                    FormEvents::POST_SET_DATA,
                                    function (FormEvent $event) {
                                        $form = $event->getForm();
                                        $data = $event->getData();
                                      //  $accessor = PropertyAccess::createPropertyAccessor();
                                
                                       // $almacen = $accessor->getValue($form, 'almacen');                                        
                                        $alma = $form;//->get('almacen');
                                        $form->add('proveedor', 'text', array(
                                            'data' =>  " ".get_class($alma->getData('almacen'))
                                        ));
                                    }
                                    );*/
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mant\AlmacenBundle\Entity\finanzas\FacturaProveedor'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mant_almacenbundle_finanzas_facturaproveedor';
    }
}
