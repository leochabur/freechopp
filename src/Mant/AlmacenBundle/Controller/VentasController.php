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
use Mant\AlmacenBundle\Entity\movimientos\MovimientoStockRepository;
use Mant\AlmacenBundle\Entity\movimientos\NotaPedidoRepository;
use Mant\AlmacenBundle\Entity\movimientos\FacturaVenta;
use Mant\AlmacenBundle\Entity\finanzas\CuentaCorriente;
use Mant\AlmacenBundle\Entity\finanzas\MovimientoDebito;
use Mant\AlmacenBundle\Entity\movimientos\OrdenCompra;
use Mant\AlmacenBundle\Entity\movimientos\Cliente;
use Mant\AlmacenBundle\Form\movimientos\ClienteType;
use Mant\AlmacenBundle\Form\movimientos\OrdenCompraType;
use Mant\AlmacenBundle\Form\movimientos\FacturaVentaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use GestionUsuariosBundle\Entity\VerificaClave;

//use Symfony\Component\Validator\Validation;
class VentasController extends Controller
{
    public function facturaVentaAction()
    {
        $factura = new FacturaVenta();
        $form = $this->getFormFacturaVenta($factura);
        return $this->render('MantAlmacenBundle:movimientos:facturaVenta.html.twig', array('form'=>$form->createView())); 
    }

    private function getFormFacturaVenta($movimiento)
    {
        return $this->createForm(new FacturaVentaType(), $movimiento, array('action'=>$this->generateUrl('mant_almacen_factura_venta_procesar'), 'method'=>'POST'));
    }

    public function facturaVentaProcesarAction(Request $request)
    {
        $factura = new FacturaVenta();
        $form = $this->getFormFacturaVenta($factura);
        $form->handleRequest($request);
        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $factura->setUserAlta($user);            
            $em->persist($factura);
            $em->flush();
            return $this->redirectToRoute('mant_almacen_factura_venta_add_item', array('fact' => $factura->getId()));            
        }
        return $this->render('MantAlmacenBundle:movimientos:facturaVenta.html.twig', array('form'=>$form->createView()));         
    }

    public function facturaVentaAddItemAction($fact)
    {
        $em = $this->getDoctrine()->getManager();
        $factura = $em->find('MantAlmacenBundle:movimientos\FacturaVenta', $fact);
        if (!$factura){
            $this->addFlash('error',"Factura inexistente!!");
            return $this->redirectToRoute('mant_almacen_factura_venta');            
        }
        $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');
        $notas = $this->getFormLoadNotaPedido($factura->getCliente(), $factura->getId());
        $form = $this->createFormActionFacturaVenta($factura->getId(), 'mant_almacen_action_factura_venta', 'POST');
        return $this->render('MantAlmacenBundle:ventas:addItemFacturaVenta.html.twig', array('factura' => $factura, 'notas' => $notas->createView(), 'form' => $form->createView()));
    }

    private function getFormLoadNotaPedido($cliente, $idFact)
    {
        $cli = $cliente;
        return $this->createFormBuilder()
                    ->add('notaspedido', 'entity', array('class' => 'MantAlmacenBundle:movimientos\NotaPedido',
                                            'query_builder' => function(NotaPedidoRepository $er) use ($cli){
                                                                                                return $er->createQueryBuilder('u')
                                                                                                        ->where('u.cliente = :cliente AND u.confirmado = :confirmado AND u.facturado = :facturado')
                                                                                                        ->setParameter('cliente', $cli)
                                                                                                        ->setParameter('confirmado', true)
                                                                                                        ->setParameter('facturado', false);
                                                                                             }))  
                    ->add('factura', 'hidden', array('data' => $idFact))
                    ->add('save', 'submit', array('label'=>'Ver Detalle'))
                    ->setAction($this->generateUrl('mant_almacen_show_items_nota_pedido'))                    
                    ->getForm();      
    }

    public function showItemsNotaPedidoAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            try{
                $var = $request->request->get("form");
                
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');
                $nota = $repository->find($var['notaspedido']);
                $items = $repository->getItemsPendientesFactrarNotasPedido($nota);
                $forms = array();
                foreach ($items as $item) {
                    $forms[$item['item']->getId()] = $this->getFormAddItemNPFactura($item['item'], $item['cantidad'], $var['factura'])->createView();
                }
                return $this->render('MantAlmacenBundle:ventas:showItemsNP.html.twig', array('items' => $items, 'forms' => $forms));
            }
            catch (\Exception $e) {return new Response($e->getMessage());}
        }    
        return new Response("holaaaaaaaaaaaaa");    
    }

    private function createFormActionFacturaVenta($id, $url, $method)
    {
        return $this->createFormBuilder()
                    ->add('save', 'submit', array('label' => 'Guardar Formulario'))
                    ->add('cancel', 'submit', array('label' => 'Eliminar Formulario'))
                    ->add('pausa', 'submit', array('label' => 'Pausar Formulario'))
                    ->add('accion', 'hidden')
                    ->setMethod($method)
                    ->setAction($this->generateUrl($url, array('mov' => $id)))
                    ->getForm();
    }    

    public function actionFacturaVentaAction($mov, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');
        $factura = $repository->find($mov);
        if ($factura->getConfirmado()){
            $this->addFlash('error',"La fcatura se encuentra ya finalizada!!");
            return $this->redirectToRoute('mant_almacen_factura_venta');            
        }
        $form = $this->createFormActionFacturaVenta($mov, 'mant_almacen_action_factura_venta', 'POST');
        $form->handleRequest($request);
        $data = $form->getData();
        if ($data['accion'] == 'p')
        {
            $this->addFlash('response',"La fcatura se ha pausado exitosamente!!");
            return $this->redirectToRoute('mant_almacen_factura_venta');
        }
        elseif ($data['accion'] == 'c')
        {
            foreach ($factura->getItems() as $item) {
                $factura->deleteItemMovimiento($item);
            }
            $em->remove($factura);
            $em->flush();
            $this->addFlash('response',"La fcatura se ha eliminado exitosamente!!");
            return $this->redirectToRoute('mant_almacen_factura_venta');
        }
        elseif ($data['accion'] == 's')
        {
            /////////recupera el proximo numero de comprobante////////////////
            $options = $repository->getNumeroComprobante($factura->getTipoFormulario(), $factura->getDepositoAAfectar());
            $numero = 1;
            if (!$options){
                $options = new NumeracionFormulario();
                $options->setFormulario($factura->getTipoFormulario());
                $options->setDeposito($factura->getDepositoAAfectar());
                $em->persist($options);
            }
            else{
                $numero = $options->getProxNumero();
            }
            $factura->setNumeroComprobante($numero);
            $numero++;
            $options->setProxNumero($numero);
            ///////////fin recupero numero comprobante///////////////////////   
            $factura->setConfirmado(true);
            $repository = $em->getRepository('MantAlmacenBundle:finanzas\CuentaCorriente');
            $ctaCte = $repository->ctaCteEnteComercial($factura->getCliente());
            if (!$ctaCte){
                $ctaCte = new CuentaCorriente();
                $ctaCte->setEnte($factura->getCliente());
                $em->persist($ctaCte);
            }
            $debito = new MovimientoDebito();
            $debito->setMonto($factura->getImporteTotal());
            $debito->setDetalle($factura->getDescripcionFormulario()." ".$factura->getNumeroComprobante());
            $debito->setMovimientoStock($factura);
            $debito->setCtacte($ctaCte);
            $em->persist($debito);  
            $em->flush();           
            //$this->addFlash('response',"La fcatura se ha generado exitosamente!!");  

            return $this->redirectToRoute('mant_almacen_imprimir_comprobante', array('mov' => $factura->getId()));                          
        }
        return new Response($data['accion']);
    }   

    public function imprimirComprobanteAction($mov)    
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');
        $factura = $repository->find($mov);     
           
           

        $pdf = $this->get('app.fpdf');
        $pdf->AliasNbPages();
        $pdf->AddPage('P', 'A4');
        $pdf->SetFont('Times','',8);
        $pdf->SetAutoPageBreak(true,0);      
        
        $pdf = $this->setHeader($pdf, $pdf->getX(), $pdf->getY(), $factura);
        $pdf = $this->setDetalle($pdf, $factura);

        if (count($factura->getItems()) < 14){
            $pdf->setXY(0,148);
            $pdf->Line(0,148,210,148);
            $pdf->setY(150);
            $pdf = $this->setHeader($pdf, $pdf->getX(), $pdf->getY(), $factura);
            $pdf = $this->setDetalle($pdf, $factura);            
        }

        
        return new Response($pdf->Output(), 200, array('Content-Type' => 'application/pdf'));    
    }

    private function setDetalle($pdf, $factura)
    {
        foreach ($factura->getItems() as $item) {
            $pdf->Cell(100, 7, $item->getDescripcion(), 'L', 0, 'L', False);
            $pdf->Cell(30, 7, $item->getCantidad(), 0, 0, 'R', False);        
            $pdf->Cell(30, 7, '$ '.$item->getPrecioUnitario(), 0, 0, 'R', False);    
            $pdf->Cell(0, 7, '$ '.$item->getPrecioTotal(), 'R', 1, 'R', False);
        }
        $pdf->Cell(0, 7, '$ '.$factura->getImporteTotal(), 1, 1, 'R', False);        
        return $pdf;
    }

    private function setHeader($pdf, $x, $y, $mov){
        $logo = $this->get('kernel')->getRootDir() . '/../web/bundles/mantalmacen/images/masterbus-logo.jpg';
        $pdf->Cell(90,20,'',1, 0, 'C', False);
        $pdf->setX($x);
        $pdf->setY($y+2);  
        $pdf->Image($logo, $pdf->getX(), $pdf->getY(), 50, 15);   
        

        $pdf->setXY($x+90, $y);
        $pdf->SetFont('Times','',10);

        $ax = $pdf->getX();
        $ay = $pdf->getY();        
        $pdf->setXY($ax, $ay-2); 
        $pdf->Write(10,$mov->getDescripcionFormulario());
        
        $pdf->setXY($ax, $ay+2);        
        $pdf->Write(10, "Fecha:     ".$mov->getFecha()->format('d/m/Y'));
        
        $pdf->setXY($ax, $ay+6);
        $pdf->Write(10, "Numero:   ".str_pad($mov->getId(), 8, "0", STR_PAD_LEFT));

        $pdf->setXY($ax, $ay+10);
        $pdf->SetFont('Times','B',12);
        $pdf->Write(15, "Cliente:   ".$mov->getCliente());

        $pdf->setXY($x+90, $y);
        $pdf->Cell(0, 20, '', 1, 1, 'C', False);
        $pdf->SetFont('Times','',10);
        $pdf->Cell(100, 7, 'Descripcion', 1, 0, 'L', False);
        $pdf->Cell(30, 7, 'Cantidad', 1, 0, 'L', False);        
        $pdf->Cell(30, 7, 'Unitario', 1, 0, 'L', False);    
        $pdf->Cell(0, 7, 'Total', 1, 1, 'L', False);                     
        return $pdf;
    }

    private function getFormAddItemNPFactura($item, $cant, $idFact)
    {
        return $this->createFormBuilder()
                    ->add('cantidad', 'number', array('data' => number_format(($item->getCantidad()-$cant))))  
                    ->add('unitario', 'number', array('data' => $item->getPrecioUnitario()))    
                    ->add('item', 'hidden', array('data' => $item->getId()))                  
                    ->add('add', 'submit', array('label'=>'+'))   
                    ->setAction($this->generateUrl('mant_almacen_add_item_np_factura', array('fact' => $idFact)))                                 
                    ->getForm();            
    }

    public function facturarItemNotaPedidoAction($fact)
    {
        try
        {
            $request = $this->getRequest();
            if ($request->isXmlHttpRequest())
            {
                $var = $request->request->get("form");
                $cantVenta = $var['cantidad'];
                if (!is_numeric($cantVenta) || ($cantVenta <= 0))
                { /// no hay stock del producto
                    return new JsonResponse(array('status' => false, 'msge'=> 'Cantidad invalida!'));
                }                
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');
                $factura = $repository->find($fact);

                if ($factura->getConfirmado()){
                    return new JsonResponse(array('status' => false, 'msge'=> 'La factura ya ha sido emitida! No se puede modificar'));                    
                }

                $item = $em->find('MantAlmacenBundle:movimientos\ItemMovimiento', $var['item']);

               // $itemsCompraPendientes = $repository->getItemsOCPendientesDeConsumir($item->getArticulo(), $factura);

                if ($item->getArticulo()->getSActual() < $cantVenta)
                { /// no hay stock del producto
                    return new JsonResponse(array('status' => false, 'msge'=> 'No hay stock suficiente de '.$item.'! (Quedan en stock '.$item->getArticulo()->getSActual().')'));
                }
                
              //  foreach ($itemsCompraPendientes as $itc) {
                  //  if ($cantVenta > 0){
                       // if ($itc->getStockPendiente() >= $cantVenta){ ////la cantidad a vender es menor que la cantidad pendiente de consumir
                $itVenta = new ItemMovimiento();
                $itVenta->setCantidad($cantVenta);                            
                $itVenta->setDescripcion($item->getDescripcion());
                $itVenta->setPrecioUnitario($var['unitario']);
                $itVenta->updatePrecioTotal();
                $itVenta->setArticulo($item->getArticulo());
                $itVenta->setMovimiento($factura);
                $itVenta->setItemNotaPedido($item);
                $itVenta->setCostoCompra($item->getArticulo()->getPrecioCompra());
                $itVenta->setConfirmado(true);
              //  $itVenta->addItemsOrdenCompra($itc);
                $itVenta->getArticulo()->updateStock((-1)*$itVenta->getCantidad());
                $em->persist($itVenta);
                      //  }
                  //  }
             //   }
                $em->flush();
                return new JsonResponse(array('status' => true, 'msge'=>$item->getMovimiento()->getId().""));
            }
            return new JsonResponse(array('status' => false, 'msge'=>'EROROROROOROROR'));
        }
        catch (\Exception $e) {return new JsonResponse(array('msge'=>$e->getMessage()));}                    
    }

    public function viewChangeAction(Request $request)
    {
        $form = $this->createFormSelectDeposito();
        if ( $request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $data = $request->get('form'); 
                $deposito = $data['deposito'];
                $repository = $em->getRepository('MantAlmacenBundle:ArticuloDeposito');                
                $articulos = $repository->articulosCambioPrecios($deposito);
                $forms = array();
            /*    foreach ($articulos as $art)
                {   $label = "Activar";
                    $activar = 1;
                    if ($art->getActivo()){
                        $activar = 0;
                        $label = "Desactivar";
                    }
                    $forms[$art->getId()] = $this->createFormActionArticulo($art->getId(), 'mant_almacen_activar_desactivar_articulo', $label)->createView();
                }*/
                return $this->render('MantAlmacenBundle:ventas:listaArticulosChange.html.twig', array('form'=>$form->createView(), 'articulos' => $articulos, 'forms'=>$forms));                 
            }
        }
        return $this->render('MantAlmacenBundle:ventas:listaArticulosChange.html.twig', array('form'=>$form->createView()));         

    }

    private function createFormSelectDeposito($deposito = null)
    {
        $form = $this->createFormBuilder()
                    ->add('deposito', 'entity', array('class' => 'MantAlmacenBundle:Almacen',
                                            'query_builder' => function(AlmacenRepository $er){
                                                                                                return $er->createQueryBuilder('u')
                                                                                                          ->where('u in (:depositos)')
                                                                                                          ->setParameter('depositos', $this->getUser()->getDepositos()->toArray());
                                                                                             }))  
                    ->add('save', 'submit', array('label'=>'Cargar Articulos'));
        return $form->getForm();        
    }    

    public function updatePriceAction($id)
    {
        try{
            $em = $this->getDoctrine()->getManager();
            $articulo = $em->find('MantAlmacenBundle:ArticuloDeposito', $id);
            $articulo->setPrecioCompra($articulo->getUltPrecioCompra());
            $em->flush();
            return new JsonResponse(array('status' => true));
        }
        catch (\Exception $e) {return new JsonResponse(array('status' => false, 'msge'=>$e->getMessage()));}                            
    }

    public function altaClienteAction()
    {
        $cliente = new Cliente();
        $formCliente = $this->createFormAltaCliente($cliente);
        return $this->render('MantAlmacenBundle:ventas:addCliente.html.twig', array('form'=>$formCliente->createView()));  
    }    

    private function createFormAltaCliente($cliente)
    {
        return $this->createForm(new ClienteType(), $cliente, array('action'=>$this->generateUrl('mant_almacen_alta_cliente_venta_procesar'), 'method'=>'POST', 'user' => $this->getUser()));
    }

    public function altaClienteProcesarAction(Request $request)
    {
        $cliente = new Cliente();
        $form = $this->createFormAltaCliente($cliente);
        $form->handleRequest($request);
        if ($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($cliente);
            $em->flush();
            return $this->redirectToRoute('mant_almacen_alta_cliente_venta');
        }
        return $this->render('MantAlmacenBundle:ventas:addCliente.html.twig', array('form'=>$formCliente->createView()));         
    }

}
