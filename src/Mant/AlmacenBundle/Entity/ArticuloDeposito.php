<?php

namespace Mant\AlmacenBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticuloDeposito
 *
 * @ORM\Table(name="articulos_por_deposito")
 * @ORM\Entity(repositoryClass="Mant\AlmacenBundle\Entity\ArticuloDepositoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ArticuloDeposito
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
    * @ORM\ManyToOne(targetEntity="Mant\AlmacenBundle\Entity\Articulo") 
    * @ORM\JoinColumn(name="id_articulo", referencedColumnName="id")
    */      
    private $articulo;

    /**
    * @ORM\ManyToOne(targetEntity="Mant\AlmacenBundle\Entity\Almacen") 
    * @ORM\JoinColumn(name="id_almacen", referencedColumnName="id")
    */      
    private $almacen;    

    /**
     * @var float
     *
     * @ORM\Column(name="sMin", type="float", nullable=false, options={"default": 0.0})
     */
    private $sMin = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="sMax", type="float", nullable=true)
     */
    private $sMax;

    /**
     * @var float
     *
     * @ORM\Column(name="sActual", type="float", nullable=false, options={"default": 0.0})
     */
    private $sActual = 0;
     

    /**
     * @var float
     *
     * @ORM\Column(name="precioCompra", type="float", nullable=false, options={"default": 0.0})
     */
    private $precioCompra = 0;       ////se usa para calcular el precio de venta


    /**
     * @var float
     *
     * @ORM\Column(name="ultPrecioCompra", type="float", nullable=false, options={"default": 0.0})
     */
    private $ultPrecioCompra = 0;       


    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable=false, options={"default":true})
     */
    private $activo;   

    /**
     * @var float
     *
     * @ORM\Column(name="markup", type="float", nullable=false, options={"default": 0.0})
     */
    private $markup = 0;   
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }


    public function getPrecioVenta()
    {
        return ($this->precioCompra * (1+($this->markup/100)));
    }

    public function getPrecioVentaActualizado()
    {
        return ($this->ultPrecioCompra * (1+($this->markup/100)));
    }    
    /**
     * Set sMin
     *
     * @param float $sMin
     * @return ArticuloDeposito
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
     * @return ArticuloDeposito
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
     * Set sActual
     *
     * @param float $sActual
     * @return ArticuloDeposito
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
     * Set precioCompra
     *
     * @param float $precioCompra
     * @return ArticuloDeposito
     */
    public function setPrecioCompra($precioCompra)
    {
        $this->precioCompra = $precioCompra;

        return $this;
    }

    /**
     * Get precioCompra
     *
     * @return float 
     */
    public function getPrecioCompra()
    {
        return $this->precioCompra;
    }


    /**
     * Set articulo
     *
     * @param \Mant\AlmacenBundle\Entity\Articulo $articulo
     * @return ArticuloDeposito
     */
    public function setArticulo(\Mant\AlmacenBundle\Entity\Articulo $articulo = null)
    {
        $this->articulo = $articulo;

        return $this;
    }

    /**
     * Get articulo
     *
     * @return \Mant\AlmacenBundle\Entity\Articulo 
     */
    public function getArticulo()
    {
        return $this->articulo;
    }

    /**
     * Set almacen
     *
     * @param \Mant\AlmacenBundle\Entity\Almacen $almacen
     * @return ArticuloDeposito
     */
    public function setAlmacen(\Mant\AlmacenBundle\Entity\Almacen $almacen = null)
    {
        $this->almacen = $almacen;

        return $this;
    }

    /**
     * Get almacen
     *
     * @return \Mant\AlmacenBundle\Entity\Almacen 
     */
    public function getAlmacen()
    {
        return $this->almacen;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return ArticuloDeposito
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
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->activo = true;
    }       

    public function updateStock($stock)
    {
        $this->sActual+= $stock;
    }

    /**
     * Set markup
     *
     * @param float $markup
     * @return ArticuloDeposito
     */
    public function setMarkup($markup)
    {
        $this->markup = $markup;

        return $this;
    }

    /**
     * Get markup
     *
     * @return float 
     */
    public function getMarkup()
    {
        return $this->markup;
    }

    /**
     * Set ultPrecioCompra
     *
     * @param float $ultPrecioCompra
     * @return ArticuloDeposito
     */
    public function setUltPrecioCompra($ultPrecioCompra)
    {
        $this->ultPrecioCompra = $ultPrecioCompra;

        return $this;
    }

    /**
     * Get ultPrecioCompra
     *
     * @return float 
     */
    public function getUltPrecioCompra()
    {
        return $this->ultPrecioCompra;
    }
}
