<?php

namespace Mant\AlmacenBundle\Entity\movimientos;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * NotaPedido
 *
 * @ORM\Table(name="nota_pedido")
 * @ORM\Entity(repositoryClass="Mant\AlmacenBundle\Entity\movimientos\NotaPedidoRepository")
 */
class NotaPedido extends MovimientoStock
{

    /**
    * @ORM\ManyToOne(targetEntity="Mant\AlmacenBundle\Entity\Almacen") 
    * @ORM\JoinColumn(name="id_almacen_destino", referencedColumnName="id")
    * @Assert\NotBlank(message="El campo no puede permanecer en blanco!")    * 
    */   
    private $almacenDestino;   

    /**
    * @ORM\ManyToOne(targetEntity="EnteComercial") 
    * @ORM\JoinColumn(name="id_entecomercial", referencedColumnName="id")
    * @Assert\NotBlank(message="El campo no puede permanecer en blanco!")    * 
    */   
    private $cliente;       

    /**
     * @ORM\ManyToOne(targetEntity="FacturaVenta", inversedBy="notasPedido")
     * @ORM\JoinColumn(name="id_factura_venta", referencedColumnName="id")
     */
    private $factura;    

     public function getInstance()
     {
        return 5;
     }
     
     public function getAlmacenOrigenData()
     {
        return null;
     }

     public function getAlmacenDestinoData()
     {
        return $this->almacenDestino;
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
         return 'npc';
     }

     public function getDepositoAAfectar() 
     {
        return $this->almacenDestino;
     }

     public function getDepositoAutorizante()
     {

     }

     public function getItemConfirmado()
     {
        return true;
     }

     public function marcarProcesado($procesado)
     {

     }

     public function movimientoConfirmado()
     {
        return true;
     }

     public function verificarItem($item, $sRealOrigen, $sRealDestino, $stockEnTransito)
     {

     }

     public function getNameProveedor()
     {
        return $this->cliente;
     }

     public function setAutorizacionFormulario()
     {

     }

     public function getControlaStockPorMarca()
     {

     }

     public function generaMovimientoCtaCte()
     {
        return false;
     }

     public function getEnteComercial()
     {
        return $this->cliente;
     }

    public function getDescripcionFormulario()
    {
        return "Nota de Pedido";
    }


    public function getDetalle()
    {
        return $this->getDescripcionFormulario();
    }

    /**
     * Set almacenDestino
     *
     * @param \Mant\AlmacenBundle\Entity\Almacen $almacenDestino
     * @return NotaPedido
     */
    public function setAlmacenDestino(\Mant\AlmacenBundle\Entity\Almacen $almacenDestino = null)
    {
        $this->almacenDestino = $almacenDestino;

        return $this;
    }

    /**
     * Get almacenDestino
     *
     * @return \Mant\AlmacenBundle\Entity\Almacen 
     */
    public function getAlmacenDestino()
    {
        return $this->almacenDestino;
    }

    /**
     * Set cliente
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\EnteComercial $cliente
     * @return NotaPedido
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

    public function __toString()
    {
        return $this->getDescripcionFormulario()." N°: ".$this->getNumeroComprobante()."  Fecha: ".$this->getFecha()->format('d/m/Y');
    }
}
