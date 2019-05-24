<?php

namespace Mant\AlmacenBundle\Entity\movimientos;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * FacturaVenta
 *
 * @ORM\Table(name="facturas_venta")
 * @ORM\Entity(repositoryClass="Mant\AlmacenBundle\Entity\movimientos\FacturaVentaRepository")
 */
class FacturaVenta extends MovimientoStock
{

    /**
    * @ORM\ManyToOne(targetEntity="EnteComercial") 
    * @ORM\JoinColumn(name="id_entecomercial", referencedColumnName="id")
    * @Assert\NotBlank(message="El campo no puede permanecer en blanco!")    * 
    */   
    private $cliente;   
    
    /**
    * @ORM\ManyToOne(targetEntity="Mant\AlmacenBundle\Entity\Almacen") 
    * @ORM\JoinColumn(name="id_almacen_origen", referencedColumnName="id")
    * @Assert\NotBlank(message="El campo no puede permanecer en blanco!")    * 
    */   
    private $almacenOrigen;   

    
    /**
     * @ORM\OneToMany(targetEntity="NotaPedido", mappedBy="factura")
     */
    private $notasPedido;  /////representa todas las notas de pedido que son facturadas   



    public function __construct()
    {
        $this->notasPedido = new \Doctrine\Common\Collections\ArrayCollection();
    }    

     public function getInstance()
     {
        return 6;
     }
     
     public function getAlmacenOrigenData()
     {
        return $this->almacenOrigen;
     }

     public function getAlmacenDestinoData()
     {
        return null;
     }

     public function updateArticleItem(ItemMovimiento $item)
     {

     }

     public function getProcesado()
     {

     }

     public function setProcesado($procesado)  
     {

     }  

     public function getTipoFormulario()
     {
        return 'fvc'; ////fact vta cliente
     }

     public function getDepositoAAfectar() //////para saber a que deposito debe afectar el formulario
     {
        return $this->almacenOrigen;
     }

     public function getDepositoAutorizante() //////para saber a que deposito debe autorizar el formulario 
     {

     }

     public function getItemConfirmado()////devuelve si el item que se agrega al formulario queda confirmado o no
     {

     }

     public function marcarProcesado($procesado)
     {

     }

     public function movimientoConfirmado()
     {

     }

     public function verificarItem($item, $sRealOrigen, $sRealDestino, $stockEnTransito)
     {

     }

     public function getNameProveedor()
     {

     }

     public function setAutorizacionFormulario()
     {

     }  

     public function getControlaStockPorMarca()
     {

     }

     public function generaMovimientoCtaCte()
     {
        return true;
     }
     
     public function getEnteComercial()
     {
        return $this->cliente;
     }

    public function getDescripcionFormulario()
    {
        return "Factura de Venta";
    }      

    /**
     * Set numero
     *
     * @param string $numero
     * @return FacturaVenta
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set cliente
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\EnteComercial $cliente
     * @return FacturaVenta
     */
    public function setCliente(\Mant\AlmacenBundle\Entity\movimientos\EnteComercial $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \Mant\AlmacenBundle\Entity\movimientos\EnteComercial 
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set almacenOrigen
     *
     * @param \Mant\AlmacenBundle\Entity\Almacen $almacenOrigen
     * @return FacturaVenta
     */
    public function setAlmacenOrigen(\Mant\AlmacenBundle\Entity\Almacen $almacenOrigen = null)
    {
        $this->almacenOrigen = $almacenOrigen;

        return $this;
    }

    /**
     * Get almacenOrigen
     *
     * @return \Mant\AlmacenBundle\Entity\Almacen 
     */
    public function getAlmacenOrigen()
    {
        return $this->almacenOrigen;
    }

    /**
     * Add notasPedido
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\NotaPedido $notasPedido
     * @return FacturaVenta
     */
    public function addNotasPedido(\Mant\AlmacenBundle\Entity\movimientos\NotaPedido $notasPedido)
    {
        $this->notasPedido[] = $notasPedido;

        return $this;
    }

    /**
     * Remove notasPedido
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\NotaPedido $notasPedido
     */
    public function removeNotasPedido(\Mant\AlmacenBundle\Entity\movimientos\NotaPedido $notasPedido)
    {
        $this->notasPedido->removeElement($notasPedido);
    }

    /**
     * Get notasPedido
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotasPedido()
    {
        return $this->notasPedido;
    }
}
