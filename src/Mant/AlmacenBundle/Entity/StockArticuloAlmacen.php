<?php

namespace Mant\AlmacenBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StockArticuloAlmacen
 *
 * @ORM\Table(name="stocks_articulos_almacen")
 * @ORM\Entity
 */
class StockArticuloAlmacen
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
     * @var float
     *
     * @ORM\Column(name="sMin", type="float", nullable=true)
     */
    private $sMin;

    /**
     * @var float
     *
     * @ORM\Column(name="sMax", type="float", nullable=true)
     */
    private $sMax;

    /**
     * @var float
     *
     * @ORM\Column(name="sIdeal", type="float", nullable=true)
     */
    private $sIdeal;


    /**
    * @ORM\ManyToOne(targetEntity="Articulo", inversedBy="stocksArticulos") 
    * @ORM\JoinColumn(name="id_articulo", referencedColumnName="id")
    */        
    private $articulo;
    
    /**
    * @ORM\ManyToOne(targetEntity="Almacen") 
    * @ORM\JoinColumn(name="id_almacen", referencedColumnName="id")
    */        
    private $almacen;    
    
    
    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */    
    private $ubicacion;

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
     * Set sMin
     *
     * @param float $sMin
     * @return StockArticuloAlmacen
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
     * @return StockArticuloAlmacen
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
     * @return StockArticuloAlmacen
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
     * Set articulo
     *
     * @param \Mant\AlmacenBundle\Entity\Articulo $articulo
     * @return StockArticuloAlmacen
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
     * @return StockArticuloAlmacen
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
     * Set ubicacion
     *
     * @param string $ubicacion
     * @return StockArticuloAlmacen
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
    
    public function __toString()
    {
        return "stock ";
    }
}
