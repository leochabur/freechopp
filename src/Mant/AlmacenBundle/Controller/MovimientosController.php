<?php

namespace Mant\AlmacenBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Mant\AlmacenBundle\Entity\movimientos\EntradaStock;
use Mant\AlmacenBundle\Entity\movimientos\SalidaStock;
use Mant\AlmacenBundle\Entity\movimientos\TransferenciaStock;
use Mant\AlmacenBundle\Entity\movimientos\ConsumoStock;
use Mant\AlmacenBundle\Form\movimientos\EntradaStockType;
use Mant\AlmacenBundle\Form\movimientos\SalidaStockType;
use Mant\AlmacenBundle\Form\movimientos\ConsumoStockType;
use Mant\AlmacenBundle\Form\movimientos\TransferenciaStockType;
use Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento;
use Symfony\Component\HttpFoundation\JsonResponse;
use Mant\AlmacenBundle\Entity\AlmacenRepository;
use Mant\AlmacenBundle\Entity\opciones\NumeracionFormulario;
use Mant\AlmacenBundle\Entity\movimientos\DocumentoEntrada;
use Mant\AlmacenBundle\Entity\movimientos\NotaPedido;
use Mant\AlmacenBundle\Entity\finanzas\CuentaCorriente;
use Mant\AlmacenBundle\Entity\finanzas\MovimientoDebito;
use Mant\AlmacenBundle\Entity\movimientos\OrdenCompra;
use Mant\AlmacenBundle\Form\movimientos\OrdenCompraType;
use Mant\AlmacenBundle\Form\movimientos\NotaPedidoType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use GestionUsuariosBundle\Entity\VerificaClave;

//use Symfony\Component\Validator\Validation;
class MovimientosController extends Controller
{
    public function notaPedidoAction()
    {
        $nota = new NotaPedido();
        $form = $this->getFormNotaPedido($nota);
        return $this->render('MantAlmacenBundle:movimientos:notaPedido.html.twig', array('form'=>$form->createView())); 
    }

    private function getFormNotaPedido($movimiento)
    {
        return $this->createForm(new NotaPedidoType(), $movimiento, array('action'=>$this->generateUrl('mant_almacen_nota_pedido_procesar'), 'method'=>'POST'));
    }

    public function procesarNotaPedidoAction(Request $request)
    {
        $nota = new NotaPedido();
        $form = $this->getFormNotaPedido($nota);
        $form->handleRequest($request);
        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $nota->setUserAlta($user);            
            $em->persist($nota);
            $em->flush();
            return $this->redirectToRoute('mant_almacen_entrada_stock_add_items', array('id' => $nota->getId()));            
        }
        return $this->render('MantAlmacenBundle:movimientos:notaPedido.html.twig', array('form'=>$form->createView()));         
    }

    public function entradaStockAction(){
        $movimiento = new EntradaStock();
        $form = $this->createFormEntradaStock($movimiento);
        return $this->render('MantAlmacenBundle:movimientos:entradaStock.html.twig', array('form'=>$form->createView()));  
    }
    
    public function salidaStockAction()
    {
        $movimiento = new SalidaStock();
        $form = $this->createFormSalidaStock($movimiento);
        return $this->render('MantAlmacenBundle:movimientos:salidaStock.html.twig', array('form'=>$form->createView()));          
        
    }
    
    public function transferenciaStockAction()
    {
        $movimiento = new TransferenciaStock();
        $form = $this->createFormTransferenciaStock($movimiento);
        return $this->render('MantAlmacenBundle:movimientos:transferenciaStock.html.twig', array('form'=>$form->createView()));                  
    }
    
    public function consumoStockAction()
    {
        $movimiento = new ConsumoStock();
        $form = $this->createFormConsumoStock($movimiento);
        return $this->render('MantAlmacenBundle:movimientos:consumoStock.html.twig', array('form'=>$form->createView()));             
    }
    
    private function createFormEntradaStock($movimiento)
    {
        return $this->createForm(new EntradaStockType(), $movimiento, array('action'=>$this->generateUrl('mant_almacen_entrada_stock_procesar'), 'method'=>'POST', 'user' => $this->getUser()));
    }
    
    private function createFormSalidaStock($movimiento)
    {
        return $this->createForm(new SalidaStockType(), $movimiento, array('action'=>$this->generateUrl('mant_almacen_salida_stock_procesar'), 'method'=>'POST', 'user' => $this->getUser()));
    }
    
    private function createFormTransferenciaStock($movimiento)
    {
        return $this->createForm(new TransferenciaStockType(), $movimiento, array('action'=>$this->generateUrl('mant_almacen_transferencia_stock_procesar'), 'method'=>'POST', 'user' => $this->getUser()));
    }
    
    private function createFormConsumoStock($movimiento)
    {
        return $this->createForm(new ConsumoStockType(), $movimiento, array('action'=>$this->generateUrl('mant_almacen_consumo_stock_procesar'), 'method'=>'POST', 'user' => $this->getUser()));
    }    
    
    public function procesarFormularioConsumoAction(Request $request)
    {
        $movimiento = new ConsumoStock();
        $form = $this->createFormConsumoStock($movimiento);
        $form->handleRequest($request);
        if ($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $movimiento->setUserAlta($user);
            $em->persist($movimiento);
            $em->flush();
            return $this->redirectToRoute('mant_almacen_entrada_stock_add_items', array('id' => $movimiento->getId()));
        }
        return $this->render('MantAlmacenBundle:movimientos:consumoStock.html.twig', array('form'=>$form->createView()));  
    }    
    
    public function ordenCompraAction(Request $request)
    {
        if ( $request->isMethod('POST'))
        {   
            $data = $request->request->get('form');
            $em = $this->getDoctrine()->getManager();
            $deposito = $em->find('MantAlmacenBundle:Almacen', $data['almacenDestino']);

            $movimiento = new OrdenCompra();
            $form = $this->createFormOrdenCompra($movimiento, $deposito);
            $form->handleRequest($request);            
            if ($form->isValid())
            {
            }
            return $this->render('MantAlmacenBundle:movimientos:ordenCompra.html.twig', array('form'=>$form->createView(), 'complete' => true));              
        }
        else
        {
            $form = $this->createFormSelectAlmacen('almacenDestino', 'Siguiente >>');
            $form->handleRequest($request);           
            return $this->render('MantAlmacenBundle:movimientos:ordenCompra.html.twig', array('form'=>$form->createView()));                 
        }
    }    
    
    private function createFormOrdenCompra($movimiento, $almacen)
    {
        return $this->createForm(new OrdenCompraType($almacen), $movimiento, array('action'=>$this->generateUrl('mant_almacen_orden_compra_procesar', array('depo' => $almacen->getId())), 'method'=>'POST', 'user' => $this->getUser()));
    }
    
    
    //////////////procesa el encabezado del formulario de Orden de Compra
    public function procesarFormularioOrdenCompraAction(Request $request, $depo)
    {
        $em = $this->getDoctrine()->getManager();
        $deposito = $em->find('MantAlmacenBundle:Almacen', $depo);
        $movimiento = new OrdenCompra();
        $form = $this->createFormOrdenCompra($movimiento, $deposito);
        $form->handleRequest($request);
        if ($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $movimiento->setUserAlta($user);
            $em->persist($movimiento);
            $em->flush();
            return $this->redirectToRoute('mant_almacen_entrada_stock_add_items', array('id' => $movimiento->getId()));
        }
        return $this->render('MantAlmacenBundle:movimientos:ordenCompra.html.twig', array('form'=>$form->createView(), 'complete' => true));  
    }
    
    public function procesarFormularioEntradaAction(Request $request)
    {
        $movimiento = new EntradaStock();
        $form = $this->createFormEntradaStock($movimiento);
        $form->handleRequest($request);
        if ($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $movimiento->setUserAlta($user);
            $em->persist($movimiento);
            $em->flush();
            return $this->redirectToRoute('mant_almacen_entrada_stock_add_items', array('id' => $movimiento->getId()));
        }
        return $this->render('MantAlmacenBundle:movimientos:entradaStock.html.twig', array('form'=>$form->createView()));  
    }
    
    public function procesarFormularioSalidaAction(Request $request)
    {
        $movimiento = new SalidaStock();
        $form = $this->createFormSalidaStock($movimiento);
        $form->handleRequest($request);
        if ($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $movimiento->setUserAlta($user);
            $em->persist($movimiento);
            $em->flush();
            return $this->redirectToRoute('mant_almacen_entrada_stock_add_items', array('id' => $movimiento->getId()));
        }
        return $this->render('MantAlmacenBundle:movimientos:salidaStock.html.twig', array('form'=>$form->createView()));  
    }
    
    public function procesarFormularioTransferenciaAction(Request $request)
    {
        $movimiento = new TransferenciaStock();
        $movimiento->setProcesado(false);
        $form = $this->createFormTransferenciaStock($movimiento);
        $form->handleRequest($request);
        if ($form->isValid()){
            if ($movimiento->getAlmacenOrigen() == $movimiento->getAlmacenDestino()){
                $this->addFlash(
                                'error',
                                'No puede realizar transferencias en el mismo Deposito!!'
                );  
                return $this->render('MantAlmacenBundle:movimientos:transferenciaStock.html.twig', array('form'=>$form->createView()));  
            }
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $movimiento->setUserAlta($user);
            $em->persist($movimiento);
            $em->flush();
            return $this->redirectToRoute('mant_almacen_entrada_stock_add_items', array('id' => $movimiento->getId()));
        }
        return $this->render('MantAlmacenBundle:movimientos:transferenciaStock.html.twig', array('form'=>$form->createView()));  
    }    
    
    public function addItemEntradaStockAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');   
        $movAbierto = $repository->existeMovimientoAbierto($id);
        if (!$movAbierto){
            return $this->redirectToRoute('gestion_usuarios_homepage');
        }
        $form = $this->createFormAddArticulo($id, 'mant_almacen_entrada_stock_add_items_procesar', ":ID_ART", "POST");
        $movimiento = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock')->find($id);
        $articulos = $em->getRepository('MantAlmacenBundle:ArticuloDeposito')->articulosPorDeposito($movimiento->getAlmacenDestinoData(), $movimiento->getAlmacenOrigenData());
        $formClose = $this->createFomrCierreCargaArticulosMovimiento($id, 'mant_almacen_entrada_stock_add_items_close', 'POST');
        $formCancel = $this->createFormCancelCargaArticulosMovimiento($id, 'mant_almacen_entrada_stock_add_items_cancel', 'POST');
        $formPausa = $this->createFormPausarCargaArticulosMovimiento($id, 'mant_almacen_entrada_stock_add_items_pausa', 'POST');        
        return $this->render('MantAlmacenBundle:movimientos:addArticuloEntradaStock.html.twig', array('descripcion'=> $movimiento->getDescripcionFormulario(), 'articulos'=> $articulos, 'movimiento' => $movimiento, 'form' => $form->createView(), 'formcierre' => $formClose->createView(), 'formcancel' => $formCancel->createView(), 'formpausa'=> $formPausa->createView(), 'type' => $movimiento->getInstance())); 
    }
    
    private function createFormAddArticulo($id_movimiento, $url, $id_articulo, $method){
           $form = $this->createFormBuilder()
                        ->add('codigo', 'text', array('attr' => array('readonly' => true)))    
                        ->add('descripcion', 'text')
                        ->add('marca', 'text', array('attr' => array('readonly' => true)))
                        ->add('cantidad', 'text')
                        ->add('unitario', 'text')
                        ->add('movimiento', 'hidden',  array('data' => $id_movimiento))
                        ->add('articulo', 'hidden')                        
                        ->setAction($this->generateUrl($url, array('id_art' => $id_articulo)))
                        ->setMethod($method)                        
                        ->add('save', 'submit', array('label'=>'Agregar Articulo'))
                        ->getForm();        
            return $form;
    }
    
    ///////confirma un movimiento de stock
    public function addItemMovimientoStockCloseAction($mov, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');
        $repoCtaCte = $em->getRepository('MantAlmacenBundle:finanzas\CuentaCorriente');        
        $movimiento = $repository->find($mov);
        if (!$movimiento){
            $this->addFlash(
                            'error',
                            'Movimiento Inexistente!'
                            );         
            return $this->redirectToRoute('mant_almacen_entrada_stock');                                
        }
        elseif($movimiento->getConfirmado()){
            $route = $this->getRoute($movimiento);
            $this->addFlash(
                            'error',
                            'El movimiento ya ha sido procesado!'
                            );              
            return $this->redirectToRoute($route);            
        }
        if (count($movimiento->getItems()) < 1){ //// no puede guardar un formulario sino se ha cargado ningun articulo al formulario
            $this->addFlash(
                            'error',
                            'No ha asignado un articulo al formulario!'
                            );  
            return $this->addItemEntradaStockAction($mov);
        }
        else{
            $obs = true;
            if (!$movimiento->getConceptoEntrada()->getObservaFormulario()) ////el concepto que tiene cargado indica que se debe dejar el formulario como observado
            {            
                foreach($movimiento->getItems() as $item)
                {
                    $movimiento->updateArticleItem($item);
                }
                $obs = false;
            }
            $movimiento->setObservado($obs);
            /////////recupera el proximo numero de comprobante////////////////
            $options = $repository->getNumeroComprobante($movimiento->getTipoFormulario(), $movimiento->getDepositoAAfectar());
            $numero = 1;
            if (!$options){
                $options = new NumeracionFormulario();
                $options->setFormulario($movimiento->getTipoFormulario());
                $options->setDeposito($movimiento->getDepositoAAfectar());
                $em->persist($options);
            }
            else{
                $numero = $options->getProxNumero();
            }
            $movimiento->setNumeroComprobante($numero);
            $numero++;
            $options->setProxNumero($numero);
            ///////////fin recupero numero comprobante///////////////////////
            $movimiento->setConfirmado(true); /////el formulario se termina de procesar de manera normal
            $movimiento->setCerrado($movimiento->movimientoConfirmado());//para indicar si el formulario ya ha sido controlado en el caso que se requiera, para las transferencias, se necesita que las cofnirme el receptor
            $movimiento->setAutorizacionFormulario();
            //////////Actualizacion de cuenta corriente
            if ($movimiento->generaMovimientoCtaCte())
            {
                $ctaCte = $repoCtaCte->ctaCteEnteComercial($movimiento->getEnteComercial());
                if (!$ctaCte){
                    $ctaCte = new CuentaCorriente();
                    $ctaCte->setEnte($movimiento->getEnteComercial());
                }
                $debito = new MovimientoDebito();
                $debito->setMonto($movimiento->getImporteTotal());
                $debito->setDetalle($movimiento->getDescripcionFormulario());
                $debito->setMovimientoStock($movimiento);
                $debito->setActiva(true);
                $debito->setCtaCte($ctaCte);
                $ctaCte->addMovimiento($debito);
                $em->persist($ctaCte);
                
            }

            //////////fin catualizacion cta cte            
            $em->flush();
            $this->addFlash(
                            'movok',
                            'Movimiento procesado exitosamente!'
                            );              
            return $this->redirectToRoute($this->getRoute($movimiento));
        }
    }
    
    public function addItemMovimientoStockCancelAction($mov)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');
        $movimiento = $repository->find($mov);
        $route = $this->getRoute($movimiento);        
        $em->remove($movimiento);
        $em->flush();
        return $this->redirectToRoute($route);
    }
    
    public function addItemMovimientoStockCancelPausa($mov)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');
        $movimiento = $repository->find($mov);


            return $this->redirectToRoute($route);
    }
    
    private function getRoute($movimiento) ///para un movimiento dado, devuelve la ruta inicial de carga del mismo
    {
            switch ($movimiento->getInstance()) {
                case 2:
                    $route = "mant_almacen_entrada_stock";
                    break;
                case 3:
                    $route = "mant_almacen_salida_stock";
                    break;
                case 4:
                    $route = "mant_almacen_view_change_precios";
                    break;
                case 5:
                    $route = "mant_almacen_autorizar_ingreso_stock";
                    break;                    
                case 6:
                    $route = "mant_almacen_orden_compra";
                    break;
                case 7:
                    $route = "mant_almacen_consumo_stock";
                    break;                     
            }   
            return $route;
    }
    
    private function createFomrCierreCargaArticulosMovimiento($id, $url, $method)
    {
        return $this->createFormBuilder()
                    ->add('save', 'submit', array('label' => 'Guardar Formulario'))
                    ->setMethod($method)
                    ->setAction($this->generateUrl($url, array('mov' => $id)))
                    ->getForm();
    }
    
    private function createFormCancelCargaArticulosMovimiento($id, $url, $method)
    {
        return $this->createFormBuilder()
                    ->add('cancel', 'submit', array('label' => 'Cancelar Formulario'))
                    ->setMethod($method)
                    ->setAction($this->generateUrl($url, array('mov' => $id)))
                    ->getForm();
    }    
    
    private function createFormPausarCargaArticulosMovimiento($id, $url, $method)
    {
        return $this->createFormBuilder()
                    ->add('pausa', 'submit', array('label' => 'Pausar Formulario'))
                    ->setMethod($method)
                    ->setAction($this->generateUrl($url, array('mov' => $id)))
                    ->getForm();
    }       
    
    public function addItemMovimientoStockAction($id_art){

            $request = $this->getRequest();
            if ( $request->isMethod('POST'))
            {
                $em = $this->getDoctrine()->getManager();
                $data = $request->request->get('form');        
                if (!$id_art){
                    $response = new JsonResponse();
                    $response->setData(array('ok' => false, 'msge' => "Articulo Invalido!"));
                    return $response;                    
                }                
                if ((!$data['cantidad']) || (!is_numeric($data['cantidad']))){
                    $response = new JsonResponse();
                    $response->setData(array('ok' => false, 'msge' => "Cantidad Invalida!"));
                    return $response;                    
                }

                if ((!$data['unitario']) || (!is_numeric($data['unitario']))){
                    $response = new JsonResponse();
                    $response->setData(array('ok' => false, 'msge' => "Importe Unitario Invalido!"));
                    return $response;                    
                }    
                $ok = true;          
                try{
                        
                    $repoArticulo = $em->getRepository('MantAlmacenBundle:ArticuloDeposito');
                    $movimiento = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock')->find($data['movimiento']);
                    if ($movimiento->getConfirmado())
                    {
                        $response = new JsonResponse();
                        $response->setData(array('ok' => false, 'msge' => "El formulario ya ha sido procesado!"));
                        return $response;                             
                    }
                    $articulo = $repoArticulo->find($data['articulo']); 
                    $item = new ItemMovimiento();

                    $item->setArticulo($articulo);
                    $item->setCantidad($data['cantidad']);
                    $item->setDescripcion($data['descripcion']);
                    $item->setPrecioUnitario($data['unitario']);
                    $item->setPrecioTotal($data['cantidad']*$data['unitario']);
                    $item->setMovimiento($movimiento);
                    $item->setCostoCompra($articulo->getPrecioCompra());
               
                    $item->setConfirmado($movimiento->getItemConfirmado());
                  /*  $stockEnTransito = $repoArticulo->getStockArticuloPendienteDeConfirmar($articulo); //devuelve la cantidad de articulos en los movimientos pendientes de confirmar
                    if (!$movimiento->getControlaStockPorMarca())////la orden de compra evalua Articulos Abstractos, el resto de los movimientos Articulos Concretos con Marca
                    {
                        $stockRealOrigen = $repoArticulo->getStockMaxMinArticuloAlmacen($articulo->getArticuloMarca()->getArticulo(), $movimiento->getAlmacenOrigenData());
                        $stockRealDestino = $repoArticulo->getStockMaxMinArticuloAlmacen($articulo->getArticuloMarca()->getArticulo(), $movimiento->getAlmacenDestinoData());                    
                        $result = $movimiento->verificarItem($item, $stockRealOrigen['stock'],$stockRealDestino['stock'], $stockEnTransito['stock']);  //debe obtener el stock real para el producto seleccionado, indistintamente de la marca que sea
                    }
                    else{
                        $stockRealDestino = $repoArticulo->getStockMaxMinArticuloAlmacen($articulo->getArticuloMarca()->getArticulo(), $movimiento->getAlmacenDestinoData());   
                        $result = $movimiento->verificarItem($item, $articulo->getSReal(),$stockRealDestino['stock'], $stockEnTransito['stock']);  //debe obtener el stock real para el producto seleccionado, indistintamente de la marca que sea
                                            
                    }
                    if (!$result['ok'])
                    {
                        $response = new JsonResponse();
                        $response->setData(array('ok' => false, 'msge' => $result['msge']));
                        return $response;                              
                    }*/
                    $item->setObservado(false);
                    $em->persist($item);
                    $movimiento->addItem($item);                          
                    $em->flush();   
                } catch(\Exception $e) {               
                                        $response = new JsonResponse();
                                        $response->setData(array('ok' => false, 'msge' => $e->getMessage()));
                                        return $response;
                                        }
                $data = array();
                $total = 0;
                foreach($movimiento->getItems() as $it)
                {
                    $data[] = array('codigo' => strtoupper($it->getArticulo()->getArticulo()->getCodigo()),
                                    'descripcion' => $it->getDescripcion(), 
                                    'cantidad' => $it->getCantidad(), 
                                    'unitario' => number_format($it->getPrecioUnitario(), 2,'.',''), 
                                    'total' => number_format($it->getPrecioTotal(),2,'.',''),
                                    'id' => $it->getId(),
                                    'url' => $this->generateUrl('mant_almacen_remove_item_movimiento', array('item'=>$it->getId())));
                    $total+= $it->getPrecioTotal();
                }
                $response = new JsonResponse();
                $response->setData(array('ok' => true, 'items' => $data, 'warning' => false, 'msge' => 'Exito', 'total'=>number_format($total,2,'.','')));
                return $response;
            }
    }
    
    public function listarFormulariosAction(Request $request){
        $form = $this->createFormViewDocument();
        $form->handleRequest($request);        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $movimientos = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock')->getFormulariosAlmacenPorFecha($data['deposito'], $data['desde'], $data['hasta']);
            return $this->render('MantAlmacenBundle:movimientos:viewFormularios.html.twig', array('form'=>$form->createView(), 'movimientos'=>$movimientos));             
        }
        elseif ($form->isSubmitted() && (!$form->isValid())){
            return $this->render('MantAlmacenBundle:movimientos:viewFormularios.html.twig', array('form'=>$form->createView())); 
        }        
        return $this->render('MantAlmacenBundle:movimientos:viewFormularios.html.twig', array('form'=>$form->createView())); 
    }
    
    private function createFormViewDocument()
    {
        return $this->createFormBuilder()
                    ->add('deposito', 'entity', array('class' => 'MantAlmacenBundle:Almacen',
                                            'query_builder' => function(AlmacenRepository $er){
                                                                                                return $er->createQueryBuilder('u')
                                                                                                          ->where('u in (:depositos)')
                                                                                                          ->setParameter('depositos', $this->getUser()->getDepositos()->toArray());
                                                                                             }))  
                    ->add('desde', 'date', array('widget' => 'single_text', 'constraints' => array(new NotBlank())))
                    ->add('hasta', 'date', array('widget' => 'single_text', 'constraints' => array(new NotBlank())))
                    ->add('save', 'submit', array('label'=>'Cargar Formularios'))
                    ->getForm();
    }        

    
    public function autorizarIngresoStockAction(Request $request)
    {
            $form = $this->createFormSelectAlmacen();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $movimientos = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock')->findMovimientosPendientesIngresar($data['almacenes']);
                $forms = [];
                foreach ($movimientos as $movimiento){
                    $forms[$movimiento->getId()] = $this->createFormAutorizacionIngreso('mant_almacen_cargar_fomr_autorizar_ingreso_stock', $movimiento->getId(),'POST')->createView();
                }
                return $this->render('MantAlmacenBundle:movimientos:autorizarIngreso.html.twig', 
                                      array('form'=>$form->createView(), 'movimientos' => $movimientos, 'almacen' => $data['almacenes'], 'forms' => $forms));   
            }
            else{
                return $this->render('MantAlmacenBundle:movimientos:autorizarIngreso.html.twig', array('form'=>$form->createView())); 
            }
    }
    
    private function createFormAutorizacionIngreso($url, $id, $method) ////crea un formulario para dar ingreso a un formulario de entrada
    {
       return $this->createFormBuilder()
                    ->add('save', 'submit', array('label'=>'Autorizar Ingreso'))
                    ->setMethod($method)
                    ->setAction($this->generateUrl($url, array('id' => $id)))                    
                    ->getForm();
    } 

    public function formulariosObservadosAction(Request $request)
    {
            $form = $this->createFormSelectAlmacen();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $movimientos = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock')->findMovimientosObservados($data['almacenes']);
                $forms = [];
                foreach ($movimientos as $movimiento){
                    $forms[$movimiento->getId()] = $this->createFormFirmaFormObs('mant_almacen_firmar_formularios_observados', $movimiento->getId(),'POST')->createView();
                }
                return $this->render('MantAlmacenBundle:movimientos:formObservados.html.twig', 
                                      array('form'=>$form->createView(), 'movimientos' => $movimientos, 'almacen' => $data['almacenes'], 'forms' => $forms));   
            }
            else{
                return $this->render('MantAlmacenBundle:movimientos:formObservados.html.twig', array('form'=>$form->createView())); 
            }
    }
    
    private function createFormFirmaFormObs($url, $idMov, $method)
    {
        return $this->createFormBuilder()
                    ->add('save', 'submit', array('label'=>'Firmar'))
                    ->add('link', 'hidden', array('data' => $this->generateUrl('mant_almacen_view_det_forms_observados', array('mov'=>$idMov))))                
                    ->setMethod($method)
                    ->setAction($this->generateUrl($url, array('mov' => $idMov))) 
                    ->getForm();        
    }
    
    public function firmaFomrularioAction($mov, Request $request)
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
            $em = $this->getDoctrine()->getManager();
            $verifica = new VerificaClave();
            $verifica->setClave($var);
        
            $validator = $this->get('validator');
            $errors = $validator->validate($verifica);
        
            if (count($errors) > 0) 
            {
                $this->addFlash('signerror',"Contraseña incorrecta");
                return new JsonResponse(array('error'=>true, 'message' => "Contraseña incorrecta"));
            }
            
            $movimiento = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock')->find($mov);
            $concepto = $movimiento->getConceptoEntrada();
            $user = $this->getUser();
            if ($concepto->userFirmaConcepto($user))
            {
                try
                {
                    $movimiento->firmarMovimientoObservado($user);
                    if ($movimiento->esquemaFirmaCompleto())
                    {
                        foreach ($movimiento->getItems() as $item)
                        {
                            $movimiento->updateArticleItem($item);
                        }
                    }
                    if ($user->getPermisos()->contains($movimiento->getConceptoEntrada()->getRoleCargaObservacion()))///el role del usuario debe cargar la observacion antes de firmar 
                    {
                        if (!$movimiento->getComentario())
                        {
                            $msge = 'Debe cargar la observacion antes de realizar la firma!';
                            $this->addFlash('signerror',$msge);                        
                            return new JsonResponse(array('error' => true)); 
                        }
                    }
                }
                catch (\Exception $a) {
                                        $this->addFlash('signerror',$a->getMessage());
                                        return new JsonResponse(array('error' => true, 'message' => $a->getMessage()));
                                
                }
            }
            else
            {   
                $msge = 'El usuario no tiene privilegios para firmar la observacion!';
                $this->addFlash('signerror',$msge);
                return new JsonResponse(array('error' => true, 'message' => $msge));
            }
            
            $em->flush();
            $this->addFlash('signok','Firma realizada exitosamente!');
            return new JsonResponse(array('error' => false));
        }
        catch (\Exception $e) 
                            {
                                return new JsonResponse(array('error' => true, 'message' => $e->getMessage()));
                            }
    }
    
    public function viewDetalleFormObservadoAction($mov, $flag, Request $request)
    {
       // return new Response($flag);  
        $flag = ($flag?true:false);
        $em = $this->getDoctrine()->getManager();
        $movimiento = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock')->find($mov);        
        return $this->render('MantAlmacenBundle:movimientos:detalleFormObservado.html.twig', array('movimiento'=>$movimiento, 'type'=>2, 'flag' => $flag)); 
    }
    
    public function loadObservacionFormObservadoAction($mov, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $movimiento = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock')->find($mov);   
        $user = $this->getUser();
        $comentario = $request->query->get('data');
        if ($movimiento->getConceptoEntrada()->getRoleCargaObservacion())
        {
            if ($user->getPermisos()->contains($movimiento->getConceptoEntrada()->getRoleCargaObservacion()))///el role del usuario debe cargar la observacion antes de firmar 
            {
                $movimiento->setComentario($comentario);
                $em->flush();            
                $msge = 'Observacion generada exitosamente!';
                $this->addFlash('signok',$msge);                        
                return new JsonResponse(array('error' => true));                        
            } 
            else{
                $this->addFlash('signerror',"No tiene privilegios para cargar observaciones!!! ");                        
                return new JsonResponse(array('error' => true));             
            }
        }
        else{
                $this->addFlash('signerror',"El concepto del movimiento no tiene configurada la craga de observaciones!!! ");                        
                return new JsonResponse(array('error' => true));                    
        }
    }
    
    private function createFormSelectAlmacen($name = 'almacenes', $label = 'Cargar Formularios')
    {
        return $this->createFormBuilder()
                    ->add($name, 'entity', array('class' => 'MantAlmacenBundle:Almacen',
                                            'query_builder' => function(AlmacenRepository $er){
                                                                                                return $er->createQueryBuilder('u')
                                                                                                          ->where('u in (:depositos)')
                                                                                                          ->setParameter('depositos', $this->getUser()->getDepositos()->toArray());
                                                                                             }))  
                    ->add('save', 'submit', array('label'=> $label))
                    ->getForm();
    }
    
    public function detalleFormularioAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $movimiento = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock')->find($id);
        $data = array();
        foreach ($movimiento->getItems() as $item){
            $data[] = array('id'=>$item->getId(), 
                             'codigo'=>$item->getArticulo()->getArticuloMarca()->getArticulo()->getCodigo(),
                             'descripcion'=>$item->getArticulo()->getArticuloMarca()->getArticulo()->getDescripcion(),
                             'marca'=>$item->getArticulo()->getArticuloMarca()->getMarca(),
                             'cantidad'=>$item->getCantidad(),
                             'unitario'=>$item->getPrecioUnitario());
        }
        return new JsonResponse(array('data' =>$data));
    }
    
    public function cargarFormAutIngresoAction($id){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');
        
        $movimiento = $repository->find($id);        
        $entrada = new DocumentoEntrada();
        
        $repositoryCpto = $em->getRepository('MantAlmacenBundle:movimientos\ConceptoMovimiento');
        $repoArt = $em->getRepository('MantAlmacenBundle:ArticuloMarcaAlmacen');        
        $concepto = $repositoryCpto->getConceptoAsociado($entrada);        
        $entrada->setConceptoEntrada($concepto);
        $entrada->setFecha(new \DateTime());
        $entrada->setAlmacenDestino($movimiento->getAlmacenDestino());
        $entrada->setUserAlta($this->getUser());
        $entrada->setDocumentoAsociado($movimiento);
        
        foreach ($movimiento->getItems() as $item){
            $ama = $repoArt->getArticuloMarcaAlmacen($entrada->getAlmacenDestino(), $item->getArticulo()->getArticuloMarca());
            if (!$ama){
                $ama = new ArticuloMarcaAlmacen();
                $ama->setArticuloMarca($item->getArticulo()->getArticuloMarca());
                $ama->setAlmacen($entrada->getAlmacenDestino());
                $em->persist($ama);
            }
            $it = new ItemMovimiento();
            $it->setArticulo($ama);
            $it->setDescripcion($item->getDescripcion());
            $it->setCantidad($item->getCantidad());
            $it->setItemExterno($item);
            $it->setPrecioUnitario($item->getPrecioUnitario());
            $it->setPrecioTotal($item->getPrecioTotal());
            $it->setConfirmado(false);
            $it->setMovimiento($entrada);
            $it->setObservado($it->getItemObservado($item));
            $entrada->addItem($it);
        }
        $em->persist($entrada);
        $em->flush();
        $forms = array();
        foreach ($entrada->getItems() as $item){////genera una coleccion de formularios para autorizar cada unos de los items de ingreso
            $forms[$item->getId()] = $this->createFormAutorizacionItemIngreso('mant_almacen_autorizar_ingreso_item_ajax', $item->getId(), 'POST', $item->getCantidad())->createView();
        }
        $ok = $this->createFormAceptarAutorizacionItemIngreso('mant_almacen_fomr_autorizar_ingreso_stock_aceptar', $entrada->getId(), 'POST')->createView();
        $ca = $this->createFormCancelarAutorizacionItemIngreso('mant_almacen_fomr_autorizar_ingreso_stock_cancel', $entrada->getId(), 'POST')->createView();
        return $this->render('MantAlmacenBundle:movimientos:formAutIngreso.html.twig', array('movimiento'=>$entrada, 'forms'=>$forms, 'aceptar'=>$ok, 'cancelar'=>$ca)); 
    }  
    
    private function createFormAutorizacionItemIngreso($url, $item, $method, $cant=0) ////crea un formulario para dar ingreso a un formulario de entrada
    {
       return $this->createFormBuilder()
                    ->add('cant', 'text', array('label' => false, 'data'=>$cant))
                    ->add('save', 'submit', array('label'=>'Cambiar'))
                    ->setMethod($method)
                    ->setAction($this->generateUrl($url, array('item' => $item)))                    
                    ->getForm();
    } 
    
    private function createFormAceptarAutorizacionItemIngreso($url, $form, $method) ////crea un formulario para dar ingreso a un formulario de entrada
    {
       return $this->createFormBuilder()
                    ->add('save', 'submit', array('label'=>'Guardar Formulario'))
                    ->setMethod($method)
                    ->setAction($this->generateUrl($url, array('form' => $form)))                    
                    ->getForm();
    } 
    
    private function createFormCancelarAutorizacionItemIngreso($url, $form, $method) ////crea un formulario para dar ingreso a un formulario de entrada
    {
       return $this->createFormBuilder()
                    ->add('save', 'submit', array('label'=>'Cancelar Formulario'))
                    ->setMethod($method)
                    ->setAction($this->generateUrl($url, array('form' => $form)))                    
                    ->getForm();
    }     
    
    public function autorizarIngresoItemAjaxAction($item, Request $request){
        $form = $this->createFormAutorizacionItemIngreso('mant_almacen_autorizar_ingreso_item_ajax', $item, 'POST');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $itemMovimiento = $em->getRepository('MantAlmacenBundle:movimientos\ItemMovimiento')->find($item);
            $itemMovimiento->setConfirmado(($itemMovimiento->getCantidad() == $data['cant']));
            $itemMovimiento->setCantidad($data['cant']);
            $itemMovimiento->setPrecioTotal($data['cant']*$itemMovimiento->getPrecioUnitario());
            $em->flush();
            return new JsonResponse(array('ok' =>true));
        }
            return new JsonResponse(array('ok' =>false));        
    }
    
    ////finaliza la carga del Documento de Entrada  de Stock
    public function aceptarFormIngresoStockAction($form, Request $request){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');
        $movimiento = $repository->find($form); 
        $docAsociado = $repository->find($movimiento->getDocumentoAsociado()->getId()); 

        if (!$movimiento) {
            throw $this->createNotFoundException('No se encuentra el movimiento  ');
        }
        
        $itemsPendientesConsumir = $repository->getItemsCompraPendientesConsumir($movimiento->getAlmacenDestino());
        $observado = true;
        foreach ($movimiento->getItems() as $item){
            $movimiento->updateArticleItem($item);
            //$item->getArticulo()->updateStock($item->getCantidad());
            $observado = (($observado)&&($item->getCantidad() == $item->getItemExterno()->getCantidad()));
        }
        
        $docAsociado->setCerrado(true);
        $docAsociado->setObservado(!$observado);

        
        $movimiento->setConfirmado(true); /////el formulario se termina de procesar de manera normal
        $movimiento->setCerrado($movimiento->movimientoConfirmado());    
        
        /////////recupera el proximo numero de comprobante////////////////
        $options = $repository->getNumeroComprobante($movimiento->getTipoFormulario(), $movimiento->getDepositoAAfectar());
        $numero = 1;
        if (!$options){
            $options = new NumeracionFormulario();
            $options->setFormulario($movimiento->getTipoFormulario());
            $options->setDeposito($movimiento->getDepositoAAfectar());
            $em->persist($options);
        }
        else{
            $numero = $options->getProxNumero();
        }
        $movimiento->setNumeroComprobante($numero);
        $numero++;
        $options->setProxNumero($numero);
        ///////////fin recupero numero comprobante///////////////////////        
        $em->flush();        
        return $this->redirectToRoute('mant_almacen_autorizar_ingreso_stock');
        
    }
    
    public function cancelarFormIngresoStockAction($form, Request $request){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');
        $movimiento = $repository->find($form);         
        if (!$movimiento) {
            throw $this->createNotFoundException('No se encuentra el movimiento  ');
        }
        $em->remove($movimiento);
        $em->flush();        
        return $this->redirectToRoute('mant_almacen_autorizar_ingreso_stock');
    }
    
    public function deleteItemMovimientoAction($item){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('MantAlmacenBundle:movimientos\ItemMovimiento');
        $itemMov = $repository->find($item);         
        if (!$itemMov) {
            throw $this->createNotFoundException('No se encuentra el movimiento  ');
        }
        $em->remove($itemMov);
        $em->flush();        
        return new JsonResponse(array('ok' =>true));
    }   
    
    public function formulariosPausadosAction(Request $request)
    {
        $form = $this->createFormSelectAlmacen();
        if ($request->isMethod('POST')){
            $form->handleRequest($request);
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');            
            $movimientos = $repository->movimientosEnPausa($data['almacenes']);
            $forms = array();
            foreach ($movimientos as $mov)
            {
                $forms[$mov->getId()] = $this->createFormCancelFinishMovimientoStock('mant_almacen_form_fin_cancel', $mov->getId(), 'POST')->createView();
            }
            return $this->render('MantAlmacenBundle:movimientos:formPausados.html.twig', array('form'=>$form->createView(), 'movimientos' => $movimientos, 'forms' => $forms));
        }
        
        return $this->render('MantAlmacenBundle:movimientos:formPausados.html.twig', array('form'=>$form->createView()));
    }
    
    private function createFormCancelFinishMovimientoStock($url, $form, $method) ////crea un formulario para dar cancelar o terminar de procesar un movimiento de stock
    {
       return $this->createFormBuilder()
                    ->add('delete', 'submit', array('label'=>'Eliminar Formulario'))
                    ->add('finish', 'submit', array('label'=>'Continuar>>'))                    
                    ->setMethod($method)
                    ->setAction($this->generateUrl($url, array('form' => $form)))                    
                    ->getForm();
    }  
    
    public function finalizarCancelarFormAction($form, Request $request)
    {
        $formNext = $this->createFormCancelFinishMovimientoStock('mant_almacen_form_fin_cancel', $form, 'POST');
        $formNext->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $mov = $em->find('MantAlmacenBundle:movimientos\MovimientoStock', $form);
        if ($formNext->get('finish')->isClicked())
        {
            return $this->redirectToRoute('mant_almacen_entrada_stock_add_items', array('id' => $form, 'movimiento' => $mov));
        }
        else
        {
            $em = $this->getDoctrine()->getManager();
            $var = $request->get("data");
            $verifica = new VerificaClave();
            $verifica->setClave($var);
        
            $validator = $this->get('validator');
            $errors = $validator->validate($verifica);
            if (count($errors) > 0) 
            {
                $this->addFlash('signerror',"Contraseña incorrecta!!!");
                return new JsonResponse(array('error'=>true, 'message' => "Contraseña incorrecta"));
            }
            $em->remove($mov);
            $em->flush();
            $this->addFlash('signok',"Formulario eliminado exitosamente!!!");
            return new JsonResponse(array('error'=>true, 'message' => "Contraseña incorrecta"));
        }
    }
    
    public function addItemMovimientoStockPausaAction($mov)
    {
        return $this->redirectToRoute('mant_almacen_formularios_en_pausa');
        
    }
}
