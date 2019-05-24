<?php

namespace Mant\AlmacenBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Marca
 *
 * @ORM\Table(name="marcas")
 * @ORM\Entity(repositoryClass="Mant\AlmacenBundle\Entity\MarcaRepository")
 * @UniqueEntity("marca", message="Marca ya existente en la Base de Datos!")
 * @ORM\HasLifecycleCallbacks() 
 */
class Marca
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
     * @ORM\Column(name="marca", type="string", length=100)
     * @Assert\NotBlank(message="El campo no puede permanecer en blanco!") 
     */
    private $marca;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activa", type="boolean")
     */
    private $activa;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;


    /**
    * @ORM\ManyToOne(targetEntity="GestionUsuariosBundle\Entity\Usuario") 
    * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
    */
    private $user;

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
     * Set marca
     *
     * @param string $marca
     * @return Marca
     */
    public function setMarca($marca)
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get marca
     *
     * @return string 
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set activa
     *
     * @param boolean $activa
     * @return Marca
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Marca
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
     * @return Marca
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
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
        $this->activa = true;
    }           
    
    public function __toString()
    {
        return $this->marca;
    }    
}
