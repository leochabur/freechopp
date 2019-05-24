<?php

namespace Mant\AlmacenBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Mant\AlmacenBundle\Entity\Articulo;

/**
 * Almacen
 *
 * @ORM\Table(name="almacenes")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Mant\AlmacenBundle\Entity\AlmacenRepository")
 * @UniqueEntity(fields={"nombre"},
 *               message="Ya existe un deposito con ese nombre!")
 */
class Almacen
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
     * @ORM\Column(name="nombre", type="string", length=100)
     * @Assert\NotBlank(message="El campo no puede permanecer en blanco!")
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="ubicacion", type="string", length=100)
     */
    private $ubicacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activa", type="boolean")
     */
    private $activa;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_at", type="datetime")
     */
    private $createAt;
    
    
    /**
    * @ORM\ManyToOne(targetEntity="GestionUsuariosBundle\Entity\Usuario") 
    * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
    */
    private $user;
    
    
    /**
     * @ORM\ManyToMany(targetEntity="Articulo", mappedBy="almacenes")
     */    
    private $articulos;    


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
     * Set nombre
     *
     * @param string $nombre
     * @return Almacen
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set ubicacion
     *
     * @param string $ubicacion
     * @return Almacen
     */
    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;

        return $this;
    }

    /**
     * Get ubicacion
     *
     * @return string 
     */
    public function getUbicacion()
    {
        return $this->ubicacion;
    }

    /**
     * Set activa
     *
     * @param boolean $activa
     * @return Almacen
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
     * Set createAt
     *
     * @param \DateTime $createAt
     * @return Almacen
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;

        return $this;
    }

    /**
     * Get createAt
     *
     * @return \DateTime 
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createAt = new \DateTime();
        $this->activa = true;
    }    

    /**
     * Set user
     *
     * @param \GestionUsuariosBundle\Entity\Usuario $user
     * @return Almacen
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
     * Constructor
     */
    public function __construct()
    {
        $this->articulos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add articulos
     *
     * @param \Mant\AlmacenBundle\Entity\Articulo $articulos
     * @return Almacen
     */
    public function addArticulo(\Mant\AlmacenBundle\Entity\Articulo $articulos)
    {
        $this->articulos[] = $articulos;

        return $this;
    }

    /**
     * Remove articulos
     *
     * @param \Mant\AlmacenBundle\Entity\Articulo $articulos
     */
    public function removeArticulo(\Mant\AlmacenBundle\Entity\Articulo $articulos)
    {
        $this->articulos->removeElement($articulos);
    }

    /**
     * Get articulos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticulos()
    {
        return $this->articulos;
    }
    
    public function __toString()
    {
        return $this->nombre;
    }
}
