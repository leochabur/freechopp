<?php

namespace Mant\AlmacenBundle\Entity\movimientos;

use Doctrine\ORM\Mapping as ORM;

/**
 * ItemMovimiento
 *
 * @ORM\Table(name="items_movimientos")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()  
 */
class ItemMovimiento
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="cantidad", type="decimal")
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="precioUnitario", type="float")
     */
    private $precioUnitario;
    
    
    /**
    * @ORM\ManyToOne(targetEntity="MovimientoStock", inversedBy="items") 
    * @ORM\JoinColumn(name="id_movimiento_stock", referencedColumnName="id")
    */   
    private $movimiento;
    
    
    /**
    * @ORM\ManyToOne(targetEntity="Mant\AlmacenBundle\Entity\ArticuloDeposito") 
    * @ORM\JoinColumn(name="id_articulo_deposito", referencedColumnName="id")
    */       
    private $articulo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="precioTotal", type="float")
     */
    private $precioTotal;
    


    /////////////////////Asocia un item de venta a uno o mas items de la nota de pedido////////////////////////////////////////////////////
    /**
    * @ORM\ManyToOne(targetEntity="ItemMovimiento", inversedBy="itemsFacturaVenta") 
    * @ORM\JoinColumn(name="id_item_nota_pedido", referencedColumnName="id")
    */   
    private $itemNotaPedido;  //Para un item de venta, referencia al item de la nota de pedido que se esta facturando
 
    /**
     * @ORM\OneToMany(targetEntity="ItemMovimiento", mappedBy="itemNotaPedido")
     */
    private $itemsFacturaVenta; /// Para un item de la nota de pedido, representa todos los items de la factura de venta que lo facturan
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    /////////////Asocia varios items de la orden de compra con varios items de la factura de venta/////////////////////////////////////////
     /**
    * ORM\ManyToMany(targetEntity="ItemMovimiento", inversedBy="itemsFactura", cascade={"persist", "remove"}) 
    * ORM\JoinTable(name="item_oc_item_factura")
    */   
//    private $itemsOrdenCompra;  //Para un item de venta, referencia al item de compra que se consume (para calculat el costo de la venta)  
    
        /**
     * ORM\ManyToMany(targetEntity="ItemMovimiento", mappedBy="itemsOrdenCompra", cascade={"persist", "remove"})
     */
//    private $itemsFactura; /// Para un item de la orden compra, representa todos los items de venta que lo consumen
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * @var boolean
     *
     * @ORM\Column(name="confirmado", type="boolean", options={"default":true})
     */
    private $confirmado; ///para saber si el item se encuentra ya confirmado en el caso que se necesite contrastar la salida con la entrada


    
    /**
     * @var boolean
     *
     * @ORM\Column(name="observado", type="boolean", nullable=false, options={"default":false})
     */
    private $observado; ///al agregar el item al formulario indica si el mismo es observado o no -caso de movimientos cuyas cantidaddes exceden los stock maximos-


    /**
     * @var string
     *
     * @ORM\Column(name="stockPendiente", type="decimal", nullable=true)
     */
    private $stockPendiente;  ///representa el stock pendiente que queda por consumir

    /**
     * @var string
     *
     * @ORM\Column(name="costoCompra", type="float")
     */
    private $costoCompra = 0; ///para un item de venta, representa cual fue el costo de compra del mismo    


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function getCosto()
    {
        return $this->articulo->getPrecioCompra();
        /*
        $i=0;
        $suma=0;
        foreach ($this->itemsOrdenCompra as $value) {
            $i++;
            $suma+= $value->getPrecioUnitario();
        }
        $i=$i?$i:1;
        return ($suma/$i);*/

    }


    /**
     * Set cantidad
     *
     * @param string $cantidad
     * @return ItemMovimiento
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return string 
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return ItemMovimiento
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
     * Set precioUnitario
     *
     * @param string $precioUnitario
     * @return ItemMovimiento
     */
    public function setPrecioUnitario($precioUnitario)
    {
        $this->precioUnitario = $precioUnitario;

        return $this;
    }

    /**
     * Get precioUnitario
     *
     * @return string 
     */
    public function getPrecioUnitario()
    {
        return $this->precioUnitario;
    }

    /**
     * Set precioTotal
     *
     * @param string $precioTotal
     * @return ItemMovimiento
     */
    public function setPrecioTotal($precioTotal)
    {
        $this->precioTotal = $precioTotal;

        return $this;
    }

    /**
     * Get precioTotal
     *
     * @return string 
     */
    public function getPrecioTotal()
    {
        return $this->precioTotal;
    }

    public function updatePrecioTotal()
    {
        $this->precioTotal = ($this->cantidad * $this->precioUnitario);
    }

    /**
     * Set movimiento
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\MovimientoStock $movimiento
     * @return ItemMovimiento
     */
    public function setMovimiento(\Mant\AlmacenBundle\Entity\movimientos\MovimientoStock $movimiento = null)
    {
        $this->movimiento = $movimiento;

        return $this;
    }

    /**
     * Get movimiento
     *
     * @return \Mant\AlmacenBundle\Entity\movimientos\MovimientoStock 
     */
    public function getMovimiento()
    {
        return $this->movimiento;
    }


    
    public function __toString()
    {
        return $this->descripcion;
    }
    
    public function updateArticle(){
        $this->precioTotal = ($this->cantidad * $this->precioUnitario);
        $this->articulo->updateStock($this->cantidad);
    }


    /**
     * Set confirmado
     *
     * @param boolean $confirmado
     * @return ItemMovimiento
     */
    public function setConfirmado($confirmado)
    {
        $this->confirmado = $confirmado;

        return $this;
    }

    /**
     * Get confirmado
     *
     * @return boolean 
     */
    public function getConfirmado()
    {
        return $this->confirmado;
    }

    /**
     * Set itemExterno
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemExterno
     * @return ItemMovimiento
     */
    public function setItemExterno(\Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemExterno = null)
    {
        $this->itemExterno = $itemExterno;

        return $this;
    }

    /**
     * Get itemExterno
     *
     * @return \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento 
     */
    public function getItemExterno()
    {
        return $this->itemExterno;
    }

    /**
     * Add itemsOriginales
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsOriginales
     * @return ItemMovimiento
     */
    public function addItemsOriginale(\Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsOriginales)
    {
        $this->itemsOriginales[] = $itemsOriginales;

        return $this;
    }

    /**
     * Remove itemsOriginales
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsOriginales
     */
    public function removeItemsOriginale(\Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsOriginales)
    {
        $this->itemsOriginales->removeElement($itemsOriginales);
    }

    /**
     * Get itemsOriginales
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItemsOriginales()
    {
        return $this->itemsOriginales;
    }
    
    public function getCantidadOriginal()
    {
        foreach ($this->itemsOriginales as $io){
            if ($io->getItemExterno() === $this){
                return $io->getCantidad();
            }
        }
    }
    
    public function getImporteOriginal()
    {
        foreach ($this->itemsOriginales as $io){
            if ($io->getItemExterno() === $this){
                return $io->getPrecioTotal();
            }
        }
    }
    
    public function getObservado()
    {
        foreach ($this->itemsOriginales as $io){
            if ($io->getItemExterno() === $this){
                if ($io->getCantidad() != $this->cantidad)
                    return true;
                else
                    return false;
            }
        }
        return true;
    }    

    /**
     * Set observado
     *
     * @param boolean $observado
     * @return ItemMovimiento
     */
    public function setObservado($observado)
    {
        $this->observado = $observado;
        return $this;
    }
    
    public function getItemObservado($item) ////dado unitem compara si las cantidades y el importe unitario coincide
    {
        return !(($this->cantidad == $item->getCantidad()) && ($this->precioUnitario == $item->getPrecioUnitario()));
    }

    /**
     * Set articulo
     *
     * @param \Mant\AlmacenBundle\Entity\ArticuloDeposito $articulo
     * @return ItemMovimiento
     */
    public function setArticulo(\Mant\AlmacenBundle\Entity\ArticuloDeposito $articulo = null)
    {
        $this->articulo = $articulo;

        return $this;
    }

    /**
     * Get articulo
     *
     * @return \Mant\AlmacenBundle\Entity\ArticuloDeposito 
     */
    public function getArticulo()
    {
        return $this->articulo;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->observado = false;
        $this->stockPendiente = $this->cantidad;
    }         

    /**
     * Set stockPendiente
     *
     * @param string $stockPendiente
     * @return ItemMovimiento
     */
    public function setStockPendiente($stockPendiente)
    {
        $this->stockPendiente = $stockPendiente;

        return $this;
    }

    /**
     * Get stockPendiente
     *
     * @return string 
     */
    public function getStockPendiente()
    {
        return $this->stockPendiente;
    }

    /**
     * Set itemNotaPedido
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemNotaPedido
     * @return ItemMovimiento
     */
    public function setItemNotaPedido(\Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemNotaPedido = null)
    {
        $this->itemNotaPedido = $itemNotaPedido;

        return $this;
    }

    /**
     * Get itemNotaPedido
     *
     * @return \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento 
     */
    public function getItemNotaPedido()
    {
        return $this->itemNotaPedido;
    }

    /**
     * Add itemsFacturaVenta
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsFacturaVenta
     * @return ItemMovimiento
     */
    public function addItemsFacturaVentum(\Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsFacturaVenta)
    {
        $this->itemsFacturaVenta[] = $itemsFacturaVenta;

        return $this;
    }

    /**
     * Remove itemsFacturaVenta
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsFacturaVenta
     */
    public function removeItemsFacturaVentum(\Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsFacturaVenta)
    {
        $this->itemsFacturaVenta->removeElement($itemsFacturaVenta);
    }

    /**
     * Get itemsFacturaVenta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItemsFacturaVenta()
    {
        return $this->itemsFacturaVenta;
    }

    /**
     * Set itemOrdenCompra
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemOrdenCompra
     * @return ItemMovimiento
     */
    public function setItemOrdenCompra(\Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemOrdenCompra = null)
    {
        $this->itemOrdenCompra = $itemOrdenCompra;

        return $this;
    }

    /**
     * Get itemOrdenCompra
     *
     * @return \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento 
     */
    public function getItemOrdenCompra()
    {
        return $this->itemOrdenCompra;
    }

    /**
     * Add itemsFacturaVentaOC
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsFacturaVentaOC
     * @return ItemMovimiento
     */
    public function addItemsFacturaVentaOC(\Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsFacturaVentaOC)
    {
        $this->itemsFacturaVentaOC[] = $itemsFacturaVentaOC;

        return $this;
    }

    /**
     * Remove itemsFacturaVentaOC
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsFacturaVentaOC
     */
    public function removeItemsFacturaVentaOC(\Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsFacturaVentaOC)
    {
        $this->itemsFacturaVentaOC->removeElement($itemsFacturaVentaOC);
    }

    /**
     * Get itemsFacturaVentaOC
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItemsFacturaVentaOC()
    {
        return $this->itemsFacturaVentaOC;
    }

    /**
     * Set costoCompra
     *
     * @param float $costoCompra
     * @return ItemMovimiento
     */
    public function setCostoCompra($costoCompra)
    {
        $this->costoCompra = $costoCompra;

        return $this;
    }

    /**
     * Get costoCompra
     *
     * @return float 
     */
    public function getCostoCompra()
    {
        return $this->costoCompra;
    }

    /**
     * Add itemsOrdenCompra
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsOrdenCompra
     * @return ItemMovimiento
     */
    public function addItemsOrdenCompra(\Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsOrdenCompra)
    {
        $this->itemsOrdenCompra[] = $itemsOrdenCompra;

        return $this;
    }

    /**
     * Remove itemsOrdenCompra
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsOrdenCompra
     */
    public function removeItemsOrdenCompra(\Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsOrdenCompra)
    {
        $this->itemsOrdenCompra->removeElement($itemsOrdenCompra);
    }

    /**
     * Get itemsOrdenCompra
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItemsOrdenCompra()
    {
        return $this->itemsOrdenCompra;
    }

    /**
     * Add itemsFactura
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsFactura
     * @return ItemMovimiento
     */
    public function addItemsFactura(\Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsFactura)
    {
        $this->itemsFactura[] = $itemsFactura;

        return $this;
    }

    /**
     * Remove itemsFactura
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsFactura
     */
    public function removeItemsFactura(\Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $itemsFactura)
    {
        $this->itemsFactura->removeElement($itemsFactura);
    }

    /**
     * Get itemsFactura
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItemsFactura()
    {
        return $this->itemsFactura;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->itemsFacturaVenta = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
