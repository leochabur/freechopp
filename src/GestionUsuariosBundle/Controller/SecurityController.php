<?php

namespace GestionUsuariosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    public function loginAction(){
        
        $authUtils = $this->get('security.authentication_utils');
        $error = $authUtils->getLastAuthenticationError();
        
        $lastUsername = $authUtils->getLastUsername();
        
        return $this->render('GestionUsuariosBundle:Security:login.html.twig', array('error'=> $error, 'last_username'=>$lastUsername));
    }
    
    public function loginCheckAction(){
        
    }
    
}
