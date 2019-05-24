<?php

namespace Mant\AlmacenBundle\Entity\finanzas;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuentaCorriente
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mant\AlmacenBundle\Entity\finanzas\CuentaCorrienteRepository")
 */
class CuentaCorriente
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
     * @ORM\Column(name="activa", type="boolean", nullable=true)
     */
    private $activa;

    /**
    * @ORM\ManyToOne(targetEntity="Mant\AlmacenBundle\Entity\movimientos\EnteComercial") 
    * @ORM\JoinColumn(name="id_ente_comercial", referencedColumnName="id")
    */
    private $ente;

    /**
     * @ORM\OneToMany(targetEntity="MovimientoCuenta", mappedBy="ctacte", cascade={"persist", "remove"})
     */
    private $movimientos;

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
     * @return CuentaCorriente
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
     * Constructor
     */
    public function __construct()
    {
        $this->movimientos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set ente
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\EnteComercial $ente
     * @return CuentaCorriente
     */
    public function setEnte(\Mant\AlmacenBundle\Entity\movimientos\EnteComercial $ente = null)
    {
        $this->ente = $ente;

        return $this;
    }

    /**
     * Get ente
     *
     * @return \Mant\AlmacenBundle\Entity\movimientos\EnteComercial 
     */
    public function getEnte()
    {
        return $this->ente;
    }

    /**
     * Add movimientos
     *
     * @param \Mant\AlmacenBundle\Entity\finanzas\MovimientoCuenta $movimientos
     * @return CuentaCorriente
     */
    public function addMovimiento(\Mant\AlmacenBundle\Entity\finanzas\MovimientoCuenta $movimientos)
    {
        $this->movimientos[] = $movimientos;

        return $this;
    }

    /**
     * Remove movimientos
     *
     * @param \Mant\AlmacenBundle\Entity\finanzas\MovimientoCuenta $movimientos
     */
    public function removeMovimiento(\Mant\AlmacenBundle\Entity\finanzas\MovimientoCuenta $movimientos)
    {
        $this->movimientos->removeElement($movimientos);
    }

    /**
     * Get movimientos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMovimientos()
    {
        return $this->movimientos;
    }
}
