<?php

namespace Mant\AlmacenBundle\Entity\opciones;

use Doctrine\ORM\Mapping as ORM;

/**
 * NumeracionFormulario
 *
 * @ORM\Table(name="numeracion")
 * @ORM\Entity
 */
class NumeracionFormulario
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
     * @var integer
     *
     * @ORM\Column(name="proxNumero", type="integer")
     */
    private $proxNumero;
    
    /**
     * @var String
     *
     * @ORM\Column()
     */
    private $formulario;

    /**
    * @ORM\ManyToOne(targetEntity="Mant\AlmacenBundle\Entity\Almacen") 
    * @ORM\JoinColumn(name="id_almacen", referencedColumnName="id")
    */  
    private $deposito;

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
     * Set proxNumero
     *
     * @param integer $proxNumero
     * @return NumeracionFormulario
     */
    public function setProxNumero($proxNumero)
    {
        $this->proxNumero = $proxNumero;

        return $this;
    }

    /**
     * Get proxNumero
     *
     * @return integer 
     */
    public function getProxNumero()
    {
        return $this->proxNumero;
    }

    /**
     * Set formulario
     *
     * @param string $formulario
     * @return NumeracionFormulario
     */
    public function setFormulario($formulario)
    {
        $this->formulario = $formulario;

        return $this;
    }

    /**
     * Get formulario
     *
     * @return string 
     */
    public function getFormulario()
    {
        return $this->formulario;
    }

    /**
     * Set deposito
     *
     * @param \Mant\AlmacenBundle\Entity\Almacen $deposito
     * @return NumeracionFormulario
     */
    public function setDeposito(\Mant\AlmacenBundle\Entity\Almacen $deposito = null)
    {
        $this->deposito = $deposito;

        return $this;
    }

    /**
     * Get deposito
     *
     * @return \Mant\AlmacenBundle\Entity\Almacen 
     */
    public function getDeposito()
    {
        return $this->deposito;
    }
}
