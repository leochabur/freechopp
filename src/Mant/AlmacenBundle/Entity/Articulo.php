<?php

namespace Mant\AlmacenBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Mant\AlmacenBundle\Entity\Almacen;

/**
 * Articulo
 *
 * @ORM\Table(name="articulos")
 * @ORM\Entity(repositoryClass="Mant\AlmacenBundle\Entity\ArticuloRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"codigo"},
 *               message="Ya existe un articulo con ese codigo!")
 */
class Articulo
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
     * @ORM\Column(name="codigo", type="string", length=100, nullable=true)
     */
    private $codigo;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     * @Assert\NotBlank(message="El campo no puede permanecer en blanco!") 
     */
    private $descripcion;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="especificacion", type="text", nullable=true)
     */    
    
    private $especificacion;

    /**
     * @var string
     *
     * @ORM\Column(name="codBarras", type="text", nullable=true)
     */    
    
    private $codBarras;    

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable=false, options={"default":true})
     */
    private $activo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    
    /**
    * @ORM\ManyToOne(targetEntity="Clasificacion") 
    * @ORM\JoinColumn(name="id_clasificacion", referencedColumnName="id")
    */    
    private $clasificacion;
      
    /**
    * @ORM\ManyToOne(targetEntity="Unidad") 
    * @ORM\JoinColumn(name="id_unidad", referencedColumnName="id")
    * @Assert\NotBlank(message="El campo no puede permanecer en blanco!")     
    */  
    private $unidad;
    

    /**
     * @var integer
     *
     * @ORM\Column(name="cant_x_unidad", type="integer", nullable=false) 
     * @Assert\NotBlank(message="El campo no puede permanecer en blanco!") 
     */
    private $cantXUnidad;  ////


    /**
    * @ORM\ManyToOne(targetEntity="Articulo", inversedBy="articulosFraccionados") 
    * @ORM\JoinColumn(name="id_articulo_base", referencedColumnName="id", nullable=true)
    */   
    private $articuloBase;   ////para un articulo que se fracciono indica cual es el que le da origen, Ej. para una botella seria la caja
    
    /**
     * @ORM\OneToMany(targetEntity="Articulo", mappedBy="articuloBase")
     */
    private $articulosFraccionados; ///indica cuales son los artiucolos fraccionados

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
        $this->activo = true;
    }     




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
     * Set codigo
     *
     * @param string $codigo
     * @return Articulo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Articulo
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set especificacion
     *
     * @param string $especificacion
     * @return Articulo
     */
    public function setEspecificacion($especificacion)
    {
        $this->especificacion = $especificacion;

        return $this;
    }

    /**
     * Get especificacion
     *
     * @return string 
     */
    public function getEspecificacion()
    {
        return $this->especificacion;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return Articulo
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Articulo
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
     * Set sMin
     *
     * @param float $sMin
     * @return Articulo
     */
    public function setSMin($sMin)
    {
        $this->sMin = $sMin;

        return $this;
    }

    /**
     * Get sMin
     *
     * @return float 
     */
    public function getSMin()
    {
        return $this->sMin;
    }

    /**
     * Set sMax
     *
     * @param float $sMax
     * @return Articulo
     */
    public function setSMax($sMax)
    {
        $this->sMax = $sMax;

        return $this;
    }

    /**
     * Get sMax
     *
     * @return float 
     */
    public function getSMax()
    {
        return $this->sMax;
    }

    /**
     * Set sIdeal
     *
     * @param float $sIdeal
     * @return Articulo
     */
    public function setSIdeal($sIdeal)
    {
        $this->sIdeal = $sIdeal;

        return $this;
    }

    /**
     * Get sIdeal
     *
     * @return float 
     */
    public function getSIdeal()
    {
        return $this->sIdeal;
    }

    /**
     * Set cantXUnidad
     *
     * @param float $cantXUnidad
     * @return Articulo
     */
    public function setCantXUnidad($cantXUnidad)
    {
        $this->cantXUnidad = $cantXUnidad;

        return $this;
    }

    /**
     * Get cantXUnidad
     *
     * @return float 
     */
    public function getCantXUnidad()
    {
        return $this->cantXUnidad;
    }

    /**
     * Set clasificacion
     *
     * @param \Mant\AlmacenBundle\Entity\Clasificacion $clasificacion
     * @return Articulo
     */
    public function setClasificacion(\Mant\AlmacenBundle\Entity\Clasificacion $clasificacion = null)
    {
        $this->clasificacion = $clasificacion;

        return $this;
    }

    /**
     * Get clasificacion
     *
     * @return \Mant\AlmacenBundle\Entity\Clasificacion 
     */
    public function getClasificacion()
    {
        return $this->clasificacion;
    }

    /**
     * Set unidad
     *
     * @param \Mant\AlmacenBundle\Entity\Unidad $unidad
     * @return Articulo
     */
    public function setUnidad(\Mant\AlmacenBundle\Entity\Unidad $unidad = null)
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return \Mant\AlmacenBundle\Entity\Unidad 
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * Set sActual
     *
     * @param float $sActual
     * @return Articulo
     */
    public function setSActual($sActual)
    {
        $this->sActual = $sActual;

        return $this;
    }

    /**
     * Get sActual
     *
     * @return float 
     */
    public function getSActual()
    {
        return $this->sActual;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->articulosFraccionados = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set articuloBase
     *
     * @param \Mant\AlmacenBundle\Entity\Articulo $articuloBase
     * @return Articulo
     */
    public function setArticuloBase(\Mant\AlmacenBundle\Entity\Articulo $articuloBase = null)
    {
        $this->articuloBase = $articuloBase;

        return $this;
    }

    /**
     * Get articuloBase
     *
     * @return \Mant\AlmacenBundle\Entity\Articulo 
     */
    public function getArticuloBase()
    {
        return $this->articuloBase;
    }

    /**
     * Add articulosFraccionados
     *
     * @param \Mant\AlmacenBundle\Entity\Articulo $articulosFraccionados
     * @return Articulo
     */
    public function addArticulosFraccionado(\Mant\AlmacenBundle\Entity\Articulo $articulosFraccionados)
    {
        $this->articulosFraccionados[] = $articulosFraccionados;

        return $this;
    }

    /**
     * Remove articulosFraccionados
     *
     * @param \Mant\AlmacenBundle\Entity\Articulo $articulosFraccionados
     */
    public function removeArticulosFraccionado(\Mant\AlmacenBundle\Entity\Articulo $articulosFraccionados)
    {
        $this->articulosFraccionados->removeElement($articulosFraccionados);
    }

    /**
     * Get articulosFraccionados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticulosFraccionados()
    {
        return $this->articulosFraccionados;
    }

    public function __toString()
    {
        return $this->descripcion;
    }

    /**
     * Set codBarras
     *
     * @param string $codBarras
     * @return Articulo
     */
    public function setCodBarras($codBarras)
    {
        $this->codBarras = $codBarras;

        return $this;
    }

    /**
     * Get codBarras
     *
     * @return string 
     */
    public function getCodBarras()
    {
        return $this->codBarras;
    }


/*
    public function setPrecioCompra($precioCompra)
    {
        $this->precioCompra = $precioCompra;

        return $this;
    }


    public function getPrecioCompra()
    {
        return $this->precioCompra;
    }
    */
}
