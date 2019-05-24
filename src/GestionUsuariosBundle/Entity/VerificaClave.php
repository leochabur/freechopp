<?php
namespace GestionUsuariosBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class VerificaClave
{
    
    /**
     * @SecurityAssert\UserPassword(
     *     message = "Wrong value for your current password"
     * )
     */    
    private $clave;
    

    public function setClave($clave)
    {
        $this->clave = $clave;
    }
}