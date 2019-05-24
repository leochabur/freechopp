<?php

namespace GestionUsuariosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use GestionUsuariosBundle\Entity\Usuario;
use GestionUsuariosBundle\Form\UsuarioType;
use Symfony\Component\HttpFoundation\Request;
use GestionUsuariosBundle\Entity\UsuarioRepository;
use Symfony\Component\HttpFoundation\Response;

class UsuarioController extends Controller
{
    public function homeAction()
    {
        return $this->render('GestionUsuariosBundle:user:home.html.twig');
    }
    
    public function addAction()
    {
        $usuario = new Usuario();
        $form = $this->crearFormularioAltaUsuario($usuario);
        return $this->render('GestionUsuariosBundle:user:add.html.twig', array('form'=>$form->createView()));
    }
    
    private function crearFormularioAltaUsuario(Usuario $user)
    {
        return $this->createForm(new UsuarioType(), $user, array('action'=>$this->generateUrl('gestion_usuarios_create'), 'method'=>'POST'));
    }
    
    public function createAction(Request $request)
    {
        $usuario = new Usuario();
        $form = $this->crearFormularioAltaUsuario($usuario);
        $form->handleRequest($request);
        if ($form->isValid()){
            $password = $form->get('clave')->getData();
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($usuario, $password);
            $usuario->setClave($encoded);
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();
            return $this->redirectToRoute('gestion_usuarios_add');
        }
        return $this->render('GestionUsuariosBundle:user:add.html.twig', array('form'=>$form->createView()));
    }
    
    public function listarAction()
    {
        $em = $this->getDoctrine()->getManager();        
        $repo = $em->getRepository('GestionUsuariosBundle:Usuario');
        $users = $repo->getAllUser();
        $forms = array();
        foreach ($users as $user)
        {
            $forms[$user->getId()] = $this->getFormViewUser($user->getId(),'gestion_usuarios_view','POST')->createView();
        }
        return $this->render('GestionUsuariosBundle:user:list.html.twig', array('users'=>$users, 'forms' => $forms));
    }
    
    private function getFormViewUser($id, $url, $method)
    {
        return  $this->createFormBuilder()
                     ->add('view', 'submit', array('label'=>'Ver Credenciales'))
                     ->setAction($this->generateUrl($url, array('user' => $id)))
                     ->setMethod($method)
                     ->getForm();        
    }
    
    public function viewAction($user)
    {
        $em = $this->getDoctrine()->getManager();        
        $user = $em->find('GestionUsuariosBundle:Usuario', $user);        
        $formDepos = array();
        $formRoles = array();
        foreach ($user->getDepositos() as $depo)
        {
            $formDepos[$depo->getId()] = $this->getFormRemoveAction('Quitar Deposito', 'gestion_usuarios_quitar_deposito', $depo->getId(), $user->getId(), 'POST')->createView();
        }
        $formAddDeposito = $this->getFormAddAction('Agregar Deposito', 'gestion_usuarios_add_deposito', $user->getId(), 'MantAlmacenBundle:Almacen', 'almacenes', 'POST')->createView();
        
        foreach ($user->getPermisos() as $role)
        {
            $formRoles[$role->getId()] = $this->getFormRemoveAction('Quitar Role', 'gestion_usuarios_quitar_role', $role->getId(), $user->getId(), 'POST')->createView();
        }
        $formAddRole = $this->getFormAddAction('Agregar Role', 'gestion_usuarios_add_role', $user->getId(), 'GestionUsuariosBundle:RoleUsuario', 'roles', 'POST')->createView();
        return $this->render('GestionUsuariosBundle:user:view.html.twig', array('user'=>$user, 'formDepos' => $formDepos, 'formRoles' => $formRoles, 'addDepo' => $formAddDeposito, 'addRole' => $formAddRole));
    }
    
    public function getFormRemoveAction($label, $url, $id, $user, $method)
    {
        return  $this->createFormBuilder()
                     ->add('action', 'submit', array('label'=>$label))
                     ->setAction($this->generateUrl($url, array('user' => $user, 'id' => $id)))
                     ->setMethod($method)
                     ->getForm();   
    }
    
    public function deleteDepositoAction($user, $id)
    {
        $em = $this->getDoctrine()->getManager();        
        $user = $em->find('GestionUsuariosBundle:Usuario', $user);    
        $deposito = $em->find('MantAlmacenBundle:Almacen', $id);
        $user->removeDeposito($deposito);
        $em->flush();
        $this->addFlash('signok',"Deposito quitado exitosamente!");          
        return $this->redirectToRoute('gestion_usuarios_view', array('user' => $user->getId()));
    }
    
    public function deleteRoleAction($user, $id)
    {
        $em = $this->getDoctrine()->getManager();        
        $user = $em->find('GestionUsuariosBundle:Usuario', $user);    
        $role = $em->find('GestionUsuariosBundle:RoleUsuario', $id);
        $user->removeRole($role);
        $em->flush();
        $this->addFlash('signok',"Role quitado exitosamente!");          
        return $this->redirectToRoute('gestion_usuarios_view', array('user' => $user->getId()));      
    }
    
    public function getFormAddAction($label, $url, $user, $classEntity, $fieldName, $method)
    {
        return  $this->createFormBuilder()
                     ->add('action', 'submit', array('label'=>$label))
                     ->add($fieldName, 'entity', array('class' =>$classEntity))
                     ->setAction($this->generateUrl($url, array('user' => $user)))
                     ->setMethod($method)
                     ->getForm();   
    }
    
    public function addDepositoAction($user, Request $request)
    {
        $formAddDeposito = $this->getFormAddAction('Agregar Deposito', 'gestion_usuarios_add_deposito', $user, 'MantAlmacenBundle:Almacen', 'almacenes', 'POST');
        $formAddDeposito->handleRequest($request);
        $em = $this->getDoctrine()->getManager(); 
        $user = $em->find('GestionUsuariosBundle:Usuario', $user);    
        $deposito = $formAddDeposito->get('almacenes')->getData();
        if ($user->getDepositos()->contains($deposito))
        {
            $this->addFlash('signerror',"El usuario ya tiene asignado el deposito para operar!");
        }
        else{
            $user->addDeposito($deposito);
            $em->flush();
            $this->addFlash('signok',"Deposito asignado exitosamente!");            
        }
        return $this->redirectToRoute('gestion_usuarios_view', array('user' => $user->getId()));
    }
    
    public function addRoleAction($user, Request $request)
    {
        $em = $this->getDoctrine()->getManager(); 
        $user = $em->find('GestionUsuariosBundle:Usuario', $user);   
        if ($user->getPermisos()->count())
        {
            $this->addFlash('signerror',"El usuario ya tiene un rol asignado. Para cambiar debe borrar el actual!");  
        }
        else{
            $formAddRole = $this->getFormAddAction('Agregar Role', 'gestion_usuarios_add_role', $user->getId(), 'GestionUsuariosBundle:RoleUsuario', 'roles', 'POST');
            $formAddRole->handleRequest($request);
            $role = $formAddRole->get('roles')->getData();    
            $user->addRole($role);
            $em->flush();
            $this->addFlash('signok',"El role ha sido asignado exitosamente!");              
        }
        return $this->redirectToRoute('gestion_usuarios_view', array('user' => $user->getId()));        
    }
}
