<?php

namespace Mant\AlmacenBundle\Entity\finanzas;

use Doctrine\ORM\Mapping as ORM;

/**
 * MovimientoCuenta
 *
 * @ORM\Table(name="movimiento_cuenta")
 * @ORM\Entity(repositoryClass="Mant\AlmacenBundle\Entity\finanzas\MovimientoCuentaRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="integer")
 * @ORM\DiscriminatorMap({1:"MovimientoCuenta", 2:"MovimientoDebito"}) 
 */
abstract class MovimientoCuenta
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
     * @var boolean
     *
     * @ORM\Column(name="activa", type="boolean")
     */
    private $activa = true;

    /**
     * @var float
     *
     * @ORM\Column(name="monto", type="float")
     */
    private $monto;

    /**
     * @var string
     *
     * @ORM\Column(name="detalle", type="string", length=100, nullable=true)
     */
    private $detalle;        

    /**
     * @ORM\ManyToOne(targetEntity="CuentaCorriente", inversedBy="movimientos")
     * @ORM\JoinColumn(name="id_cta_cte", referencedColumnName="id", nullable=true)
     */
    private $ctacte;

    /**
    * @ORM\ManyToOne(targetEntity="Mant\AlmacenBundle\Entity\movimientos\MovimientoStock") 
    * @ORM\JoinColumn(name="id_movimiento", referencedColumnName="id", nullable=true)
    */
    private $movimientoStock;    

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
     * Set activa
     *
     * @param boolean $activa
     * @return MovimientoCuenta
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
     * Set ctacte
     *
     * @param \Mant\AlmacenBundle\Entity\finanzas\CuentaCorriente $ctacte
     * @return MovimientoCuenta
     */
    public function setCtacte(\Mant\AlmacenBundle\Entity\finanzas\CuentaCorriente $ctacte = null)
    {
        $this->ctacte = $ctacte;

        return $this;
    }

    /**
     * Get ctacte
     *
     * @return \Mant\AlmacenBundle\Entity\finanzas\CuentaCorriente 
     */
    public function getCtacte()
    {
        return $this->ctacte;
    }

    /**
     * Set movimientoStock
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\MovimientoStock $movimientoStock
     * @return MovimientoCuenta
     */
    public function setMovimientoStock(\Mant\AlmacenBundle\Entity\movimientos\MovimientoStock $movimientoStock = null)
    {
        $this->movimientoStock = $movimientoStock;

        return $this;
    }

    /**
     * Get movimientoStock
     *
     * @return \Mant\AlmacenBundle\Entity\movimientos\MovimientoStock 
     */
    public function getMovimientoStock()
    {
        return $this->movimientoStock;
    }

    /**
     * Set monto
     *
     * @param float $monto
     * @return MovimientoCuenta
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return float 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set detalle
     *
     * @param string $detalle
     * @return MovimientoCuenta
     */
    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;

        return $this;
    }

    /**
     * Get detalle
     *
     * @return string 
     */
    public function getDetalle()
    {
        return $this->detalle;
    }
}
