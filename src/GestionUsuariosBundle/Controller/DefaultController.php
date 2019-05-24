<?php

namespace GestionUsuariosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('GestionUsuariosBundle:Default:index.html.twig', array('name' => $name));
    }
}
