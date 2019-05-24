<?php

namespace Mant\AlmacenBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Mant\AlmacenBundle\Entity\AlmacenRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\JsonResponse;

class InformesController extends Controller
{
    public function ctaCteArticuloAction(Request $request)
    {
        $form = $this->createFormCtaCteArt();
        if ( $request->isMethod('POST'))
        {   
            $form->handleRequest($request);
            if (!$form->isValid())
            {
                return $this->render('MantAlmacenBundle:informes:resumenCtaCteArticulo.html.twig', array('form' => $form->createView()));
            }
            $em = $this->getDoctrine()->getManager();
            $data = $request->request->get('form'); 
            $deposito = $em->find('MantAlmacenBundle:Almacen', $data['deposito']);
            $repository = $em->getRepository('MantAlmacenBundle:ArticuloMarcaAlmacen');
            $articulos = $repository->articulosPorAlmacenConMovimientos($deposito, $data['desde'], $data['hasta']);//articulosPorAlmacen($deposito);
            $forms = array();
            foreach($articulos as $articulo)
            {
                $forms[$articulo['id']] = $this->createFormResumenCtaArticulo('mant_almacen_informes_view_ctacte_articulo', $articulo['id'], $data['deposito'], $data['desde'], $data['hasta'])->createView();
            }
            return $this->render('MantAlmacenBundle:informes:resumenCtaCteArticulo.html.twig', array('form' => $form->createView(), 'articulos' => $articulos, 'forms' => $forms));
        }
        return $this->render('MantAlmacenBundle:informes:resumenCtaCteArticulo.html.twig', array('form' => $form->createView()));
    }
    
    public function createFormResumenCtaArticulo($url, $articulo, $deposito, $desde, $hasta)
    {
        return $this->createFormBuilder()
                    ->add('load', 'submit', array('label'=>'Ver Resumen'))
                    ->add('desde', 'hidden', array('data' => $desde))
                    ->add('hasta', 'hidden', array('data' => $hasta))
                    ->setAction($this->generateUrl($url, array('art' => $articulo, 'dep' => $deposito)))
                    ->setMethod('POST')
                    ->getForm();
    }
    
    private function createFormCtaCteArt($deposito = null)
    {
        $form = $this->createFormBuilder()
                    ->add('deposito', 'entity', array('class' => 'MantAlmacenBundle:Almacen',
                                            'query_builder' => function(AlmacenRepository $er){
                                                                                                return $er->createQueryBuilder('u')
                                                                                                          ->where('u in (:depositos)')
                                                                                                          ->setParameter('depositos', $this->getUser()->getDepositos()->toArray());
                                                                                             }))  
                    ->add('desde', 'date', array('widget' => 'single_text', 'constraints' => array(new NotBlank())))
                    ->add('hasta', 'date', array('widget' => 'single_text', 'constraints' => array(new NotBlank())))
                    ->add('save', 'submit', array('label'=>'Cargar Articulos'));
        return $form->getForm();        
    }
    
    public function viewResumenArticuloAction($art, $dep)
    {
        $request = $this->getRequest();
        $data = $request->request->get('form'); 
        $em = $this->getDoctrine()->getManager();
        $articulo = $em->find('MantAlmacenBundle:Articulo', $art);
        $deposito = $em->find('MantAlmacenBundle:Almacen', $dep);

        $repository = $em->getRepository('MantAlmacenBundle:movimientos\MovimientoStock');
        $movimientos = $repository->findMovimientosArticulos($articulo, $deposito, $data['desde'], $data['hasta']);
        return $this->render('MantAlmacenBundle:informes:viewCtaCteArticulo.html.twig', array('movimientos' => $movimientos, 'articulo'=>$articulo, 'desde'=>$data['desde'], 'hasta'=>$data['hasta']));
    }
    
    public function listadoArticulosAction(Request $request)
    {
        $form = $this->createSelectAlmacen();
        if ( $request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $data = $request->get('form'); 
                $deposito = $data['deposito'];
                $repository = $em->getRepository('MantAlmacenBundle:ArticuloMarcaAlmacen');                
                $articulos = $repository->allArticulosPorDeposito($deposito);
                $forms = array();
                foreach ($articulos as $art)
                {   $label = "Activar";
                    $activar = 1;
                    if ($art->getActivo()){
                        $activar = 0;
                        $label = "Desactivar";
                    }
                    $forms[$art->getId()] = $this->createFormActionArticulo($art->getId(), 'mant_almacen_activar_desactivar_articulo', $label)->createView();
                }
                return $this->render('MantAlmacenBundle:informes:listaArticulos.html.twig', array('form'=>$form->createView(), 'articulos' => $articulos, 'forms'=>$forms));                 
            }
        }
        return $this->render('MantAlmacenBundle:informes:listaArticulos.html.twig', array('form'=>$form->createView()));        
    }
    
    private function createFormActionArticulo($id, $url, $label)
    {
        $form = $this->createFormBuilder()
                    ->add('action', 'submit', array('label'=>$label))
                    ->setAction($this->generateUrl($url, array('art' => $id)));
        return $form->getForm();        
    }    
    
    public function activarDesactivarArticuloAction(Request $request, $art)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $articulo = $em->find('MantAlmacenBundle:ArticuloMarcaAlmacen', $art);
            $articulo->setActivo((!$articulo->getActivo()));
            $em->flush();
            return new JsonResponse(array('error'=>true, 'msge' => $art));
        }
    }
    
    private function createSelectAlmacen()
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
}
