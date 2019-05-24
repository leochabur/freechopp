<?php

namespace Mant\AlmacenBundle\Entity\opciones;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccesoUsuario
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class AccesoUsuario
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
     * @var \DateTime
     *
     * @ORM\Column(name="fechaAcceso", type="datetime")
     */
    private $fechaAcceso;
    
    /**
    * @ORM\ManyToOne(targetEntity="GestionUsuariosBundle\Entity\Usuario") 
    * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
    */          
    private $usuario;


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
     * Set fechaAcceso
     *
     * @param \DateTime $fechaAcceso
     * @return AccesoUsuario
     */
    public function setFechaAcceso($fechaAcceso)
    {
        $this->fechaAcceso = $fechaAcceso;

        return $this;
    }

    /**
     * Get fechaAcceso
     *
     * @return \DateTime 
     */
    public function getFechaAcceso()
    {
        return $this->fechaAcceso;
    }

    /**
     * Set usuario
     *
     * @param \GestionUsuariosBundle\Entity\Usuario $usuario
     * @return AccesoUsuario
     */
    public function setUsuario(\GestionUsuariosBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \GestionUsuariosBundle\Entity\Usuario 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
    
    /**
     * @ORM\PrePersist
     */
     public function setPrePersist()
     {
         $this->fechaAcceso = new \DateTime();
     }
         
}
