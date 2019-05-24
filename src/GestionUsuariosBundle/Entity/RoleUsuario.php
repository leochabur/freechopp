<?php

namespace GestionUsuariosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RoleUsuario
 *
 * @ORM\Table(name="roles_usuarios")
 * @ORM\Entity(repositoryClass="GestionUsuariosBundle\Entity\RoleUsuarioRepository")
 */
class RoleUsuario
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255)
     */
    private $role;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return RoleUsuario
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }
    
    public function __toString()
    {
        return $this->role;
    }
}
