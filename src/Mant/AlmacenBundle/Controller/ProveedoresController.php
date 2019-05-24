<?php

namespace Mant\AlmacenBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Mant\AlmacenBundle\Entity\movimientos\Proveedor;
use Mant\AlmacenBundle\Form\movimientos\ProveedorType;
use Mant\AlmacenBundle\Entity\AlmacenRepository;
use Mant\AlmacenBundle\Entity\finanzas\FacturaProveedor;
use Mant\AlmacenBundle\Form\finanzas\FacturaProveedorType;
use Mant\AlmacenBundle\Entity\movimientos\ProveedorRepository;
use GestionUsuariosBundle\Entity\VerificaClave;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProveedoresController extends Controller
{
    public function formAddProveedorAction(){
        $proveedor = new Proveedor();
        $formProveedor = $this->createFormAltaProveedor($proveedor);
        return $this->render('MantAlmacenBundle:proveedores:addProveedor.html.twig', array('form'=>$formProveedor->createView()));  
    }
    
    private function createFormAltaProveedor($proveedor)
    {
        return $this->createForm(new ProveedorType(), $proveedor, array('action'=>$this->generateUrl('proveedores_add_procesar'), 'method'=>'POST', 'user' => $this->getUser()));
    }

    
    public function formAddProveedorProcesarAction(Request $request)
    {
        $proveedor = new Proveedor();
        $form = $this->createFormAltaProveedor($proveedor);
        $form->handleRequest($request);
        if ($form->isValid()){
            $em = $this->getDoctrine()->getManager();
        //    $user = $this->getUser();
        //    $movimiento->setUserAlta($user);
            $em->persist($proveedor);
            $em->flush();
            return $this->redirectToRoute('proveedores_add');
        }
        return $this->render('MantAlmacenBundle:proveedores:addProveedor.html.twig', array('form'=>$form->createView()));  
    }
    
    public function autorizarOrdenCompraAction(Request $request)
    {
        $form = $this->createFormAutorizarOC();
        if ( $request->isMethod('POST'))
        {
            $form->handleRequest($request);
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $movimientos = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock')->movimientosPendientesDeAutorizar($data['deposito']);
            $firma = array();
            foreach ($movimientos as $mov)
            {
                $firma[$mov->getId()] = $this->createFormFirmaFormObs('proveedores_firmar_oc_observada', $mov->getId(), 'GET')->createView();    
            }
            return $this->render('MantAlmacenBundle:proveedores:autorizarOC.html.twig', array('form'=>$form->createView(), 'movimientos'=> $movimientos, 'firmas' => $firma));  
        }
        return $this->render('MantAlmacenBundle:proveedores:autorizarOC.html.twig', array('form'=>$form->createView()));  
    }
    
    public function autorizarOCAction(Request $request, $mov)
    {
        try{
            if ($request->isXmlHttpRequest()) ////solo acepta peticiones ajax
            {
                $var = $request->request->get("data");
            }
            else
            {
                return new JsonResponse(array('error'=>true, 'message' => "Peticion Incorrecta"));
            } 
            $verifica = new VerificaClave();
            $verifica->setClave($var);
        
            $validator = $this->get('validator');
            $errors = $validator->validate($verifica);
        
            if (count($errors) > 0) 
            {
                $this->addFlash('signerror',"Contraseña incorrecta");
                return new JsonResponse(array('error'=>true, 'message' => "Contraseña incorrecta"));
            }

            $em = $this->getDoctrine()->getManager();            
            $movimiento = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock')->find($mov);
            $concepto = $movimiento->getConceptoEntrada();   
            $user = $this->getUser();            
            if ($concepto->userAutorizaConcepto($user))
            {
                $movimiento->setAutorizado(true);
                $movimiento->setFirmaAutorizante($user);
                $movimiento->setFechaAutorizacion(new \DateTime());
                $em->flush();
                $this->addFlash('signok','Formulario autorizado exitosamente!');                
                return new JsonResponse(array('error'=>false, 'message' => "Formulario autorizado exitosamente!"));
            }
            else{
                $msge = 'El usuario no tiene privilegios para autorizar el formulario!';
                $this->addFlash('signerror',$msge);                
                return new JsonResponse(array('error'=>true, 'message' => $msge));
            }
        }
        catch (\Exception $e) 
                            {
                                return new JsonResponse(array('error' => true, 'message' => $e->getMessage()));
                            }
                            
    }
    
    private function createFormFirmaFormObs($url, $idMov, $method)
    {
        return $this->createFormBuilder()
                    ->add('save', 'submit', array('label'=>'Firmar'))
                    ->setMethod($method)
                    ->setAction($this->generateUrl($url, array('mov' => $idMov))) 
                    ->getForm();        
    //                    ->add('link', 'hidden', array('data' => $this->generateUrl('mant_almacen_view_det_forms_observados', array('mov'=>$idMov))))                     
    }    
    

    
    private function createFormAutorizarOC($label = 'Cargar Ordenes Compra', $method = 'POST')
    {
        return $this->createFormBuilder()
                    ->add('deposito', 'entity', array('class' => 'MantAlmacenBundle:Almacen',
                                            'query_builder' => function(AlmacenRepository $er){
                                                                                                return $er->createQueryBuilder('u')
                                                                                                          ->where('u in (:depositos)')
                                                                                                          ->setParameter('depositos', $this->getUser()->getDepositos()->toArray());
                                                                                             }))  
                    ->add('save', 'submit', array('label'=>$label))
                    ->setMethod($method)
                    ->getForm();
    }
    
    public function altaFacturaProveedorAction()
    {
        $factura = new FacturaProveedor();
        $form = $this->createFormAltaFacturaProveedor($factura);
        return $this->render('MantAlmacenBundle:proveedores:addFacturaProveedor.html.twig', array('form'=>$form->createView()));          
    }
    
    public function procesarFacturaProveedorAction(Request $request, $depo, $factu)
    {
        $em = $this->getDoctrine()->getManager();
        $deposito = $em->find('MantAlmacenBundle:Almacen', $depo);
        $factura = new FacturaProveedor();
        $form = $this->createFormAltaFacturaProveedor($factura, $deposito);        
        $form->handleRequest($request);
        if ($form->get('back')->isClicked())
        {
            return $this->redirectToRoute('proveedores_iniciar_alta_factura_proveedor');            
        }
        
        if ($factu) ///la factura ya ha sido grabada, solo debe verificar si esta finalizada sino cargar las OC para asociarlas a la misma
        {
            $factura = $em->find('MantAlmacenBundle:finanzas\FacturaProveedor', $factu);
            if ($factura->getProcesada())
            {
                $this->addFlash('error',"La factura ya ha sido procesada!");
                return $this->redirectToRoute('proveedores_iniciar_alta_factura_proveedor');
            }
            else{
                return $this->getFormAddOCFactProov($em, $factura);
            }            
        }        
        
        if ($form->isValid())
        {
            $factura->setUserAlta($this->getUser());
            $em->persist($factura);
            $em->flush();                
            return $this->getFormAddOCFactProov($em, $factura);             
        }
        return $this->render('MantAlmacenBundle:proveedores:altaFacturaProveedor.html.twig', array('form'=>$form->createView()));          
    }
    
    private function getFormAddOCFactProov($em, $factura)
    {
        $movimiento = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');
        $movimientos = $movimiento->findDocumentosAFacturar($factura->getProveedor());
        $forms = array();
        $delete = array();
        foreach ($movimientos as $mov)
        {
            $forms[$mov->getId()] = $this->createFormAddOCFact('proveedores_add_oc_factura_proveedor', $mov->getId(), 'POST', $factura->getId(), '+', 'add')->createView();
        }
        
        foreach ($factura->getDocumentos() as $doc)
        {
            $delete[$doc->getId()] = $this->createFormAddOCFact('proveedores_add_oc_factura_proveedor', $doc->getId(), 'POST', $factura->getId(), '-', 'del')->createView();
        }
        
        $formFin = $this->createFormActionFact('proveedores_procesar_factura_proveedor','POST', $factura->getId())->createView();
        return $this->render('MantAlmacenBundle:proveedores:addOCFacturaProveedor.html.twig', array('movimientos' => $movimientos, 'formFact' => $formFin, 'forms'=>$forms, 'factura' => $factura, 'delete' => $delete));          
    }
    
    public function addOCFacturaProveedorAction(Request $request, $fact, $mov)
    {
      //  $request = $this->getRequest();
        $data = $request->request->get('form');   
        $em = $this->getDoctrine()->getManager();
        $docEntrada = $em->find('MantAlmacenBundle:movimientos\DocumentoEntrada', $mov);
        $factura = $em->find('MantAlmacenBundle:finanzas\FacturaProveedor', $fact);        
        if ($data['action'] == 'add')
        {
            $docEntrada->setAfectadoAFactura(true); //indica que ya se ha asociado el movimiento a la factura
            $factura->addDocumento($docEntrada);
            $em->flush();
        }
        else 
        {
           // return new Response("accion $data[action]");
            $factura->removeDocumento($docEntrada);            
            $docEntrada->setAfectadoAFactura(false);
            $em->flush();
        }

        return $this->redirectToRoute('proveedores_alta_factura_proveedor_procesar', array('depo'=>$factura->getAlmacen()->getId(), 'factu'=>$factura->getId())); //new JsonResponse(array('error'=>true, 'factura' => $factura->getNumeroFactura()));
    }
    
    private function createFormActionFact($url, $method, $fac) //Formulario para Guardar y/o Cancelar Factura Proveedor
    {
        return $this->createFormBuilder()
                    ->add('save', 'submit', array('label'=>'Guardar Factura'))
                    ->add('susp', 'submit', array('label'=>'Suspender Factura'))
                    ->add('cancel', 'submit', array('label'=>'Cancelar Factura'))
                    ->setAction($this->generateUrl($url, array('fact' => $fac)))
                    ->setMethod($method)
                    ->getForm();        
    }
    
    public function finalizarProcesarFacturaProveedorAction(Request $request, $fact)
    {
        $em = $this->getDoctrine()->getManager();
        $factura = $em->find('MantAlmacenBundle:finanzas\FacturaProveedor', $fact);          
        $form = $this->createFormActionFact('proveedores_procesar_factura_proveedor','POST', $factura->getId());
        $form->handleRequest($request);
        if ($form->get('cancel')->isClicked()) ///se ha cancelado la carga de la factura
        {
            $em->remove($factura);
            $em->flush();
            $this->addFlash('error',"Carga de Factura cancelada Exitosamente!");               
            return $this->redirectToRoute('proveedores_iniciar_alta_factura_proveedor');            
        }
        elseif ($form->get('susp')->isClicked()) //se ha suspendido la carga de la fcatura
        {
            $this->addFlash('warning',"Carga de Factura Suspendida Exitosamente!");               
            return $this->redirectToRoute('proveedores_iniciar_alta_factura_proveedor');             
        }
        elseif ($form->get('save')->isClicked()) 
        {
            $difAplicacion = abs($factura->getMontoAplicado() - $factura->getImporteNeto()); ///calula cual es la diferencia entre el monto facturado y el ingresado
            $observado = ($difAplicacion > 100);
            $factura->finalizarCargaFactura($observado);
            $factura->setProcesada(true);
            $em->flush();
            $this->addFlash('success',"Carga de Factura Fianlizada Exitosamente!");               
            return $this->redirectToRoute('proveedores_iniciar_alta_factura_proveedor');               
        }
    }
    
    public function inicioFacturasPendientesAction(Request $request)
    {
        $form = $this->createFormAutorizarOC('Siguiente >>', 'POST');
        $form->handleRequest($request);
        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('MantAlmacenBundle:finanzas\FacturaProveedor');
            $facturas = $repository->findFacturasProveedorPendientes($form->getData()['deposito']);
            $forms = array();
            foreach ($facturas as $factura)
            {
                $forms[$factura->getId()] = $this->createFormViewFactProveedor('proveedores_alta_factura_proveedor_procesar', 'POST', $factura->getId(), $factura->getAlmacen()->getId())->createView();
            }
            return $this->render('MantAlmacenBundle:proveedores:factProvPendiente.html.twig', array('facturas' => $facturas, 'forms' => $forms));
        }
        return $this->render('MantAlmacenBundle:proveedores:addFacturaProveedor.html.twig', array('label'=>'Deposito', 'form'=>$form->createView(), 'titulo' => 'Facturas Proveedor suspendida')); 
    }
    
    private function createFormViewFactProveedor($url, $method, $fac, $deposito) ///crea los forms para redirigir a la opcion de cargar documentos a la factura proveedor
    {
        return $this->createFormBuilder()
                    ->add('add', 'submit', array('label'=>'Finalizar...'))
                    ->setAction($this->generateUrl($url, array('depo' => $deposito, 'factu' => $fac)))
                    ->setMethod($method)
                    ->getForm();        
    }    
    
    private function createFormAddOCFact($url, $mov, $method, $fac, $label, $data)
    {
        return $this->createFormBuilder()
                    ->add('add', 'submit', array('label'=>$label))
                    ->add('action', 'hidden', array('data' => $data))
                    ->setAction($this->generateUrl($url, array('mov' => $mov, 'fact' => $fac)))
                    ->setMethod($method)
                    ->getForm();        
    }
    
    ///////inicia la carga de la fcatura del proveedor
    public function inicioAltaFacturaProveedorAction($depo)
    {
        $form = $this->createFormAutorizarOC('Siguiente >>', 'POST');
        $request = $this->getRequest();
        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            $data = $form->getData();
            return $this->redirectToRoute('proveedores_select_proveedor_alta_factura_proveedor', array('depo'=>$data['deposito']->getId()));
        }
        else{
            return $this->render('MantAlmacenBundle:proveedores:addFacturaProveedor.html.twig', array('label'=>'Deposito', 'form'=>$form->createView()));    
        }
    }
    
    public function selectProveedorFacturaAction($depo)
    {
        $em = $this->getDoctrine()->getManager();
        $deposito = $em->find('MantAlmacenBundle:Almacen', $depo);
        $factura = new FacturaProveedor();
        $form = $this->createFormAltaFacturaProveedor($factura, $deposito);               
        return $this->render('MantAlmacenBundle:proveedores:altaFacturaProveedor.html.twig', array('label'=>'Deposito', 'form'=>$form->createView()));          
    }
    
    private function createFormAltaFacturaProveedor($factura, $deposito)
    {
        return $this->createForm(new FacturaProveedorType($deposito), $factura,array('action'=>$this->generateUrl('proveedores_alta_factura_proveedor_procesar', array('depo'=>$deposito->getId(), 'factu' => 0))));
    }    
    
}
