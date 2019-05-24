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
        $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');
        $notas = $this->getFormLoadNotaPedido($factura->getCliente(), $factura->getId());
        return $this->render('MantAlmacenBundle:ventas:addItemFacturaVenta.html.twig', array('factura' => $factura, 'notas' => $notas->createView()));
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
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');
                $factura = $repository->find($fact);

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
