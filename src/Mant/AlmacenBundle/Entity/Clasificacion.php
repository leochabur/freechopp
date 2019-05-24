<?php

namespace Mant\AlmacenBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Clasificacion
 *
 * @ORM\Table(name="calsificacionarticulos")
 * @ORM\Entity(repositoryClass="Mant\AlmacenBundle\Entity\ClasificacionRepository")
 * @UniqueEntity("clasificacion", message="Clasificacion ya existente en la Base de Datos!")
 * @ORM\HasLifecycleCallbacks()
 *  
 */
class Clasificacion
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
     * @ORM\Column(name="clasificacion", type="string", length=150)
     * @Assert\NotBlank(message="El campo no puede permanecer en blanco!") 
     */
    private $clasificacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;
    

    /**
    * @ORM\ManyToOne(targetEntity="GestionUsuariosBundle\Entity\Usuario") 
    * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
    */
    private $user;
    
 
     /**
     *
     * @ORM\Column(name="activa", type="boolean")
     */   
    private $activa;

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
     * Set clasificacion
     *
     * @param string $clasificacion
     * @return Clasificacion
     */
    public function setClasificacion($clasificacion)
    {
        $this->clasificacion = $clasificacion;

        return $this;
    }

    /**
     * Get clasificacion
     *
     * @return string 
     */
    public function getClasificacion()
    {
        return $this->clasificacion;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Clasificacion
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set user
     *
     * @param \GestionUsuariosBundle\Entity\Usuario $user
     * @return Clasificacion
     */
    public function setUser(\GestionUsuariosBundle\Entity\Usuario $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \GestionUsuariosBundle\Entity\Usuario 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set activa
     *
     * @param boolean $activa
     * @return Clasificacion
     */
    public function setActiva($activa)
    {
        $this->activa = $activa;

        return $this;
    }

    /**
     * Get activa
     *
     * @return boolean 
     */
    public function getActiva()
    {
        return $this->activa;
    }
    
    
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
        $this->activa = true;
    } 
    
    public function __toString()
    {
        return $this->clasificacion;
    }
}
