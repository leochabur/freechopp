<?php

namespace Mant\AlmacenBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Mant\AlmacenBundle\Form\AlmacenType;
use Mant\AlmacenBundle\Entity\Almacen;
use Mant\AlmacenBundle\Form\ClasificacionType;
use Mant\AlmacenBundle\Entity\Clasificacion;
use Mant\AlmacenBundle\Form\MarcaType;
use Mant\AlmacenBundle\Entity\Marca;
use Mant\AlmacenBundle\Form\ArticuloType;
use Mant\AlmacenBundle\Form\ArticuloAlmacenType;
use Mant\AlmacenBundle\Entity\Articulo;
use Symfony\Component\HttpFoundation\Request;
use Mant\AlmacenBundle\Entity\AlmacenRepository;
use Symfony\Component\HttpFoundation\Response;
use Mant\AlmacenBundle\Form\ArticuloMarcaAlmacenType;
use Mant\AlmacenBundle\Form\ArticuloBaseType;
use Mant\AlmacenBundle\Entity\ArticuloMarcaAlmacen;
use Mant\AlmacenBundle\Entity\MarcaRepository;
use Mant\AlmacenBundle\Entity\ArticuloMarca;
use Symfony\Component\HttpFoundation\JsonResponse;
use Mant\AlmacenBundle\Entity\ArticuloDeposito;

class AlmacenController extends Controller
{
    public function addAction()
    {
        $almacen = new Almacen();
        $form = $this->crearFormularioAltaAlmacen($almacen);
        return $this->render('MantAlmacenBundle:options:addAlmacen.html.twig', array('form'=>$form->createView()));        
    }
    
    private function crearFormularioAltaAlmacen(Almacen $almacen)
    {
        return $this->createForm(new AlmacenType(), $almacen, array('action'=>$this->generateUrl('gestion_mant_create_almacen'), 'method'=>'POST'));
    } 
    
    public function createalmacenAction(Request $request)
    {
        $almacen = new Almacen();
        $form = $this->crearFormularioAltaAlmacen($almacen);
        $form->handleRequest($request);
        if ($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $almacen->setUser($user);
            $em->persist($almacen);
            $em->flush();
            $this->addFlash(
                            'response',
                            'Se ha almacenado con exito el deposito en la Base de Datos!'
                            );            
            return $this->redirectToRoute('mant_almacen_addAlmacen');
        }
        return $this->render('MantAlmacenBundle:options:addAlmacen.html.twig', array('form'=>$form->createView()));
    }
    
    public function listAction()
    {
        $almacenes = $this->getDoctrine()->getRepository(Almacen::class)->findAlmacenesActivas();
        return $this->render('MantAlmacenBundle:options:listaAlmacenes.html.twig', array('almacenes'=>$almacenes));        
    }
    
    public function addclasificacionAction()
    {
        $clasificacion = new Clasificacion();
        $form = $this->crearFormularioAltaClasificacion($clasificacion);
        return $this->render('MantAlmacenBundle:options:addClasificacion.html.twig', array('form'=>$form->createView()));        
    }
    
    private function crearFormularioAltaClasificacion(Clasificacion $class)
    {
        return $this->createForm(new ClasificacionType(), $class, array('action'=>$this->generateUrl('gestion_mant_create_clasificacion'), 'method'=>'POST'));
    }
    
    public function createclasificacionAction(Request $request)
    {
        $clasificacion = new Clasificacion();
        $form = $this->crearFormularioAltaClasificacion($clasificacion);
        $form->handleRequest($request);
        if ($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $clasificacion->setUser($user);
            $em->persist($clasificacion);
            $em->flush();
            $this->addFlash(
                            'response',
                            'Se ha almacenado con exito la clasificacion en la Base de Datos!'
                            );            
            return $this->redirectToRoute('mant_almacen_add_clasificacion');
        }
        return $this->render('MantAlmacenBundle:options:addClasificacion.html.twig', array('form'=>$form->createView()));
    } 
    
////
    public function addmarcaAction()
    {
        $marca = new Marca();
        $form = $this->crearFormularioAltaMarca($marca);
        return $this->render('MantAlmacenBundle:options:addMarca.html.twig', array('form'=>$form->createView()));        
    }
    
    private function crearFormularioAltaMarca(Marca $marca)
    {
        return $this->createForm(new MarcaType(), $marca, array('action'=>$this->generateUrl('gestion_mant_create_marca'), 'method'=>'POST'));
    }
    
    public function createmarcaAction(Request $request)
    {
        $marca = new Marca();
        $form = $this->crearFormularioAltaMarca($marca);
        $form->handleRequest($request);
        if ($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $marca->setUser($user);
            $em->persist($marca);
            $em->flush();
            $this->addFlash(
                            'response',
                            'Se ha almacenado con exito la Marca en la Base de Datos!'
                            );            
            return $this->redirectToRoute('mant_almacen_add_marca');
        }
        return $this->render('MantAlmacenBundle:options:addMarca.html.twig', array('form'=>$form->createView()));
    }
    
////Manejo de articulos
    public function addarticuloAction()
    {
        $articulo = new Articulo();
        $form = $this->crearFormularioAltaArticulo($articulo);
        return $this->render('MantAlmacenBundle:options:addArticulo.html.twig', array('form'=>$form->createView()));        
    }
    
    private function crearFormularioAltaArticulo(Articulo $articulo)
    {
        return $this->createForm(new ArticuloType(), $articulo, array('action'=>$this->generateUrl('gestion_mant_create_articulo'), 'method'=>'POST'));
    } 
    
    public function createarticuloAction(Request $request)
    {
        $articulo = new Articulo();
        $form = $this->crearFormularioAltaArticulo($articulo);
        $form->handleRequest($request);

        if ($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $depositos =  $em->getRepository('MantAlmacenBundle:Almacen')->findAlmacenesActivas();
            $em->persist($articulo);  
            foreach ($depositos as $deposito) {
                $articuloDeposito = new ArticuloDeposito();
                $articuloDeposito->setArticulo($articulo);
                $articuloDeposito->setAlmacen($deposito);
                $em->persist($articuloDeposito);
            }               
            $em->flush();
            $this->addFlash(
                            'response',
                            'Se ha almacenado con exito el Articulo en la Base de Datos!'
                            );            
            return $this->redirectToRoute('mant_almacen_add_articulo');
        }
        return $this->render('MantAlmacenBundle:options:addArticulo.html.twig', array('form'=>$form->createView()));
    }
    
    public function editArticuloMarcaAlmacenAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $articulo = $em->getRepository('MantAlmacenBundle:ArticuloMarcaAlmacen')->find($id);
        
        $form = $this->createEditArticuloAlmacenForm($articulo);
        return $this->render('MantAlmacenBundle:options:editArticuloMarcaAlmacen.html.twig', array('articulo' => $articulo,'form'=>$form->createView()));
    }
    
    private function createEditArticuloAlmacenForm(ArticuloMarcaAlmacen $articulo)
    {
        return $this->createForm(new ArticuloMarcaAlmacenType(), $articulo, array('action' => $this->generateUrl('gestion_mant_update_articulo_marca_almacen', array('id' => $articulo->getId())), 'method' => 'POST'));
        
    }
    
    public function updateArticuloMarcaAlmacenAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $articulo = $em->getRepository('MantAlmacenBundle:ArticuloMarcaAlmacen')->find($id);
        $form = $this->createEditArticuloAlmacenForm($articulo);
        $form->handleRequest($request);
        if ($form->isValid()){
            $em->flush();
            $this->addFlash(
                            'response',
                            'Se ha modificado con exito el Articulo en la Base de Datos!'
                            );              
            return $this->redirectToRoute('mant_almacen_list_articulos');
        }
        return $this->render('MantAlmacenBundle:options:editArticuloAlmacen.html.twig', array('articulo' => $articulo,'form'=>$form->createView()));        
        
        
    }
    
    private function createFormSelectAlmacen()
    {
        return $this->createFormBuilder()
                        ->add('almacenes', 'entity', array('class' => 'MantAlmacenBundle:Almacen',
                                            'query_builder' => function(AlmacenRepository $er){
                                                                                                return $er->createQueryBuilder('u')
                                                                                                          ->where('u in (:depositos)')
                                                                                                          ->setParameter('depositos', $this->getUser()->getDepositos()->toArray());
                                                                                             }))  
                        ->add('save', 'submit', array('label'=>'Cargar Articulos'))
                        ->getForm();
    }
    
    public function listarArticulosAction(Request $request)
    {
            $form = $this->createFormSelectAlmacen();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $articulos = $em->getRepository('MantAlmacenBundle:ArticuloDeposito')->articulosPorDeposito($data['almacenes']);
                $formDelete = $this->createEditStockInicialForm('mant_articulo_update_stock_inicial', 'POST', ':ART_ID'); 
                return $this->render('MantAlmacenBundle:options:listArticles.html.twig', 
                                      array('form'=>$form->createView(), 'articulos' => $articulos, 'almacen' => $data['almacenes'], 'formUpdateStockInicial' => $formDelete->createView()));   
            }
            else{
                return $this->render('MantAlmacenBundle:options:listArticles.html.twig', array('form'=>$form->createView())); 
            }
    }
    
    private function createEditStockInicialForm($url, $method, $id)
    {
       return $this->createFormBuilder()
                    ->add('valor', 'hidden')
                    ->setAction($this->generateUrl($url, array('id' => $id)))
                    ->setMethod($method)
                    ->getForm();
    }
    
    public function updateStockInicialAction($id, Request $request)
    {
        try{
                $em = $this->getDoctrine()->getManager();
                $articulo = $em->getRepository('MantAlmacenBundle:ArticuloDeposito')->find($id);
                $smin = $request->request->get('form')['valor'];
                $response = new JsonResponse();
                if (!is_numeric($smin)){
                    $response->setData(array('ok' => false, 'msge' => 'El dato ingresado es invalido!'));
                    return $response;
                }                    
                $articulo->setSActual($smin);
                $em->flush();
                $response->setData(array('ok' => true, 'msge' => 'Stock modificado exitosamente!'));
                return $response;
            }catch(Exception $e) {
                                                        return new Response($e->getMessage());
                                                     }
    }
    
    public function stockXDepositoAction(Request $request)
    {
            $form = $this->createFormSelectAlmacen();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();             
                $em = $this->getDoctrine()->getManager();
                $articulos = $em->getRepository('MantAlmacenBundle:ArticuloDeposito')->articulosPorDeposito($data['almacenes']);
                return $this->render('MantAlmacenBundle:options:listStockArticles.html.twig', 
                                      array('form'=>$form->createView(), 'articulos' => $articulos, 'almacen' => $data['almacenes']));   
            }
            else{
                return $this->render('MantAlmacenBundle:options:listStockArticles.html.twig', array('form'=>$form->createView())); 
            }        
    }
    
    public function editArticuloBaseAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $articulo = $em->getRepository('MantAlmacenBundle:Articulo')->find($id);        
        $form = $this->createFormEditArtBase($articulo);
        return $this->render('MantAlmacenBundle:options:editArticuloBase.html.twig', array('form'=>$form->createView(), 'articulo' => $articulo, 'addmca' => $this->addFormMarcaArticulo($id, 'mant_articulo_add_mca_art_base', 'POST')->createView())); 
    }
    
    public function updateArticuloBaseAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $articulo = $em->getRepository('MantAlmacenBundle:Articulo')->find($id);        
        $form = $this->createFormEditArtBase($articulo);        
        $form->handleRequest($request);
        if ($form->isValid()){
            $em->flush();
            $this->addFlash(
                            'response',
                            'Se ha modificado con exito el Articulo en la Base de Datos!'
                            );              
            return $this->redirectToRoute('mant_almacen_stock_por_deposito');
        }
        return $this->render('MantAlmacenBundle:options:editArticuloBase.html.twig', array('form'=>$form->createView(), 'articulo' => $articulo)); 
    }
    
    private function addFormMarcaArticulo($idArticulo, $url, $method){
        return  $this->createFormBuilder()
                     ->add('marcas', 'entity', array('class' => 'MantAlmacenBundle:Marca',
                                                        'query_builder' => function(MarcaRepository $er){
                                                                                                            return $er->createQueryBuilder('u');
                                                                                                          }))
                     ->add('save', 'submit', array('label'=>'Agregar Marca'))
                     ->setAction($this->generateUrl($url, array('id' => $idArticulo)))
                     ->setMethod($method)
                     ->getForm();        
        
    }
    
    public function addMarcaArtBaseAction($id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $articulo = $em->getRepository('MantAlmacenBundle:Articulo')->find($id);  
        try{ 
            $form = $this->addFormMarcaArticulo($id, 'mant_articulo_add_mca_art_base', 'POST');        
            $form->handleRequest($request);    
            if ($form->isValid()) {
                $marca = $form->getData()['marcas'];
                
                $artMarca = $articulo->getArticuloMarca($marca);
                if (!$artMarca){
                    $artMarca = new ArticuloMarca();
                    $artMarca->setArticulo($articulo);
                    $artMarca->setMarca($marca);
                    $em->persist($artMarca);
                    foreach ($articulo->getAlmacenes() as $almacen){
                        $artMcaAlm = new ArticuloMarcaAlmacen();
                        $artMcaAlm->setAlmacen($almacen);
                        $artMcaAlm->setArticuloMarca($artMarca);
                        $em->persist($artMcaAlm);
                    }
                    $em->flush();
                    $response = new JsonResponse(array('state' => true));
                    return $response;                    
                }
                else{
                    $response = new JsonResponse(array('state' => false, 'msge' => 'Ya existe la marca para el articulo!!'));
                    return $response;
                }      
            }

        }catch(Exception $e) {
                                $response = new JsonResponse(array('state' => false, 'msge' => $e->getMessage()));
                                return $response;            
                            }
    }
    
    private function createFormEditArtBase($articulo)
    {
        $form = $this->createForm(new ArticuloBaseType(), $articulo, 
                                  array('action' => $this->generateUrl('mant_articulo_update_articulo_base', array('id' => $articulo->getId())), 
                                        'method' => 'POST'));     
        return $form;
    }
    
    
    public function setStockMinimoMaximoAction(Request $request)
    {
       $form = $this->generateFormSelectAlmacen("mant_set_stock_minimo_maximo", "POST");
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();
            $articulos = $em->getRepository('MantAlmacenBundle:ArticuloDeposito')->articulosPorDeposito($data['almacen']);
            
            $forms = array();
            foreach ($articulos as $articulo)
            {
                $forms[$articulo->getId()] = $this->getFormUpdateSMM('mant_articulo_update_stock_min_max', 'POST', $articulo)->createView(); 
            }
            
            return $this->render('MantAlmacenBundle:options:selectAlmacenAction.html.twig', 
                                   array('form'=>$form->createView(), 'title'=>'Modificar parametros venta', 'articulos'=>$articulos, 'forms' => $forms));
       }
       return $this->render('MantAlmacenBundle:options:selectAlmacenAction.html.twig', array('form'=>$form->createView(), 'title'=>'Modificar parametros venta')); 
    }
    
    private function getFormUpdateSMM($url, $method, $articulo)
    {
        return  $this->createFormBuilder()
                     ->add('descripcion', 'text', array('data' => $articulo->getArticulo()->getDescripcion()))        
                     ->add('pcompra', 'number', array('data' => $articulo->getPrecioCompra()))
                     ->add('markup', 'number', array('data' => $articulo->getMarkup()))
                     ->add('save', 'submit', array('label'=>'Guardar'))
                     ->setAction($this->generateUrl($url, array('idArt' => $articulo->getId())))
                     ->setMethod($method)
                     ->getForm();            
    }
    
    public function updateStockMaxMinAction($idArt, Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            try{
                $em = $this->getDoctrine()->getManager();
                $articulo = $em->find('MantAlmacenBundle:ArticuloDeposito', $idArt);

                $form = $this->getFormUpdateSMM('mant_articulo_update_stock_min_max', 'POST', $articulo);
                $form->handleRequest($request);
                 
                if ($form->isValid()) {
                    $data = $form->getData();             
                    $articulo->setMarkup($data['markup']);
                    $articulo->setPrecioCompra($data['pcompra']);
                    $articulo->getArticulo()->setDescripcion($data['descripcion']);
                    $em->flush();
                    return new JsonResponse(array('ok' => true, 'msge' => 'Actuzalizacion realizada exitosamente!'));
                }
                else{
                    return new JsonResponse(array('ok' => false, 'msge' => 'Error al realizar la accion!'));
                }
            }
            catch (\Exception $e) {
                    return new JsonResponse(array('ok' => false, 'msge' => $e->getMessage()));  
            }
                    
        }
        else
        {
            return new JsonResponse(array('ok' => false, 'msge' => 'Peticion incorrecta'));   
        }

    }

    private function generateFormSelectAlmacen($url, $method, $label = 'Siguiente...')
    {
        return  $this->createFormBuilder()
                    ->add('almacen', 'entity', array('class' => 'MantAlmacenBundle:Almacen',
                                            'query_builder' => function(AlmacenRepository $er){
                                                                                                return $er->createQueryBuilder('u')
                                                                                                          ->where('u in (:depositos)')
                                                                                                          ->setParameter('depositos', $this->getUser()->getDepositos()->toArray());
                                                                                             }))  
                     ->add('save', 'submit', array('label'=> $label))
                     ->setAction($this->generateUrl($url))
                     ->setMethod($method)
                     ->getForm();
    }
    

    
    public function importArtAlmacenAction(Request $request)
    {
        $form = $this->createFormImportArticulos('POST');
        $form->handleRequest($request);
        if ($form->isValid())
        {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $articulosOrigen = $em->getRepository('MantAlmacenBundle:ArticuloMarcaAlmacen')->articulosConMarcaPorDeposito($data['origen']);            
            $articulosDestino = $em->getRepository('MantAlmacenBundle:ArticuloMarcaAlmacen')->articulosConMarcaPorDeposito($data['destino']);             
            foreach ($articulosOrigen as $articulo)
            {
                if (!in_array($articulo, $articulosDestino))
                {
                    $ama = new ArticuloMarcaAlmacen();
                    $ama->setArticuloMarca($articulo);
                    $ama->setAlmacen($data['destino']);
                    $ama->setUsuario($this->getUser());
                    $em->persist($ama);
                }
            }
            $em->flush();
            return new Response("No esiste");    
        }
        return $this->render('MantAlmacenBundle:options:importArtAlmacen.html.twig', array('form' => $form->createView()));
    }
    
    private function createFormImportArticulos($method)
    {
        return  $this->createFormBuilder()
                    ->add('origen', 'entity', array('class' => 'MantAlmacenBundle:Almacen',
                                            'query_builder' => function(AlmacenRepository $er){
                                                                                                return $er->createQueryBuilder('u')
                                                                                                          ->where('u in (:depositos)')
                                                                                                          ->setParameter('depositos', $this->getUser()->getDepositos()->toArray());
                                                                                             }))  
                    ->add('destino', 'entity', array('class' => 'MantAlmacenBundle:Almacen',
                                            'query_builder' => function(AlmacenRepository $er){
                                                                                                return $er->createQueryBuilder('u')
                                                                                                          ->where('u in (:depositos)')
                                                                                                          ->setParameter('depositos', $this->getUser()->getDepositos()->toArray());
                                                                                             }))                                                                                               
                     ->add('save', 'submit', array('label'=>'Importar'))
                     ->setMethod($method)
                     ->getForm();
    } 

    public function fraccionarArticuloAction(Request $request)
    {
        $form = $this->generateFormSelectAlmacen("mant_almacen_stock_fraccionar_articulos", "POST", 'Cargar Articulos');
        if ( $request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if ($form->isValid())
            {
                $data = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $articulos = $em->getRepository('MantAlmacenBundle:ArticuloDeposito')->articulosAptosFraccionPorDeposito($data['almacen']);
                $forms = array();
                foreach ($articulos as $art) {
                    $forms[$art['idArt']] = $this->getFormFraccionArticulo($art['idArt'], 'mant_almacen_stock_fraccionar_articulos_action')->createView();
                }
                return $this->render('MantAlmacenBundle:informes:fraccionarArticulos.html.twig', array('form' => $form->createView(), 'articulos' => $articulos, 'forms' => $forms));                
            }   
        }
        return $this->render('MantAlmacenBundle:informes:fraccionarArticulos.html.twig', array('form' => $form->createView()));
    }

    private function getFormFraccionArticulo($idArticulo, $url)
    {
        return  $this->createFormBuilder()
                     ->add('cantidad', 'choice', array('choices'  => array('1'=>1,'2'=>2,'3'=>3),'choices_as_values' => true,))
                     ->add('save', 'submit', array('label'=>'Convertir'))
                     ->add('articulo', 'hidden', array('data' => $idArticulo))                     
                     ->setAction($this->generateUrl($url))
                     ->setMethod('POST')
                     ->getForm();            
    }  

    public function procesarFraccionarArticuloAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            try
            {
                $data = $request->get('form');
                $em = $this->getDoctrine()->getManager();
                $repo = $em->getRepository('MantAlmacenBundle:ArticuloDeposito'); 
                $artDepoUnitario = $repo->find($data['articulo']);
                $artDepoBulto = $repo->getArticuloDeposito($artDepoUnitario->getArticulo()->getArticuloBase());

                $cantidad = $data['cantidad'];

                $response = new JsonResponse(array('msge' => 'apriete '.$artDepoBulto->getArticulo()));
                return $response;
            }
            catch (\Exception $e) {
                            $response = new JsonResponse(array('msge' => $e->getMessage()));
            return $response;
            }
        }
    }     
}
