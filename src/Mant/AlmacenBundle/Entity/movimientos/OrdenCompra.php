<?php

namespace Mant\AlmacenBundle\Entity\movimientos;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OrdenCompra
 *
 * @ORM\Table(name="orden_compra")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks() 
 */
class OrdenCompra extends MovimientoStock
{
    
    /**
    * @ORM\ManyToOne(targetEntity="EnteComercial") 
    * @ORM\JoinColumn(name="id_entecomercial", referencedColumnName="id")
    * @Assert\NotBlank(message="El campo no puede permanecer en blanco!")    * 
    */   
    private $proveedor;   
    
    /**
    * @ORM\ManyToOne(targetEntity="Mant\AlmacenBundle\Entity\Almacen") 
    * @ORM\JoinColumn(name="id_almacen_destino", referencedColumnName="id")
    * @Assert\NotBlank(message="El campo no puede permanecer en blanco!")    * 
    */   
    private $almacenDestino;   

    /**
     * @var string
     *
     * @ORM\Column(name="numero_factura", type="string", length=100, nullable=true)
     * @Assert\NotBlank(message="El campo no puede permanecer en blanco!")  
     */
    private $numero;    

    /**
     * Set almacenDestino
     *
     * @param \Mant\AlmacenBundle\Entity\Almacen $almacenDestino
     * @return EntradaStock
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
    
    
    public function getDescripcionFormulario()
    {
        return "Factura de Compra";
    }
    

    /**
     * Set numero
     *
     * @param integer $numero
     * @return EntradaStock
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->numero;
    }
    
    public function getInstance()
    {
        return 4;
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
        //$item->setPrecioTotal($item->getCantidad() * $item->getPrecioUnitario());
        $item->getArticulo()->updateStock($item->getCantidad());  
        if (!$item->getArticulo()->getPrecioCompra()){
            $item->getArticulo()->setPrecioCompra($item->getPrecioUnitario());
        }      
        else{
          //  if ($item->getArticulo()->getPrecioCompra() < $item->getPrecioUnitario()){
                $item->getArticulo()->setUltPrecioCompra($item->getPrecioUnitario());
          //  }
        }
     }
     
     public function getProcesado()
     {

     }
     public function setProcesado($procesado)
     {

     }
     
     public function getTipoFormulario()
     {
         return 'fcp';
     }
     
    public function getDepositoAAfectar()     
    {
        return $this->almacenDestino;
    }     
    
    public function getItemConfirmado()    
    {
        return true;
    }
    
    public function marcarProcesado($procesado){

    }
    
    public function movimientoConfirmado()
    {
        return false;
    }    
     

    /**
     * Set proveedor
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\Proveedor $proveedor
     * @return OrdenCompra
     */
    public function setProveedor(\Mant\AlmacenBundle\Entity\movimientos\Proveedor $proveedor = null)
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor
     *
     * @return \Mant\AlmacenBundle\Entity\movimientos\Proveedor 
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }
    
    public function verificarItem($item, $sRealOrigen, $sRealDestino, $stockEnTransito)
    {
        $result = array('warning'=>false, 'ok' => true, 'msge'=>'');
        $st = $item->getArticulo()->getArticuloMarca()->getArticulo()->getStockArticuloAlmacen($this->almacenDestino);
        
        if ($st) ///si el articulo que tiene cargado el item tiene asignados un stock maximo, entonces corrobora las cantidades
        {
            if (($item->getCantidad() + $sRealDestino) > $st->getSMax()) ///verifica que el stock actual del articulo mas lo que se compra no supere el stock maximo
            {
                $result['warning'] = true;
                $result['msge'] = 'La cantidad ingresada supera el stock maximo del producto! La orden de compra quedara pendiente de autorizar '.$st;
            }
        }
        if ($item->getPrecioUnitario() == 0)
        {
            $result['ok'] = false;
            $result['msge'] = 'Debe cargar el importe al articulo';
        }
        if ($item->getCantidad() < 0)
        {
            $result['ok'] = false;
            $result['msge'] = 'No puede cargar cantidades negativas!!'.$item->getCantidad();          
        }
        return $result;
    }
    
    public function getDepositoAutorizante(){
        return $this->almacenDestino;
    }
    
    public function getNameProveedor()
    {
        return $this->proveedor;
    }
    
    public function setAutorizacionFormulario()
    {
     /*   $autorizado = true;
        foreach ($this->getItems() as $item)
        {
            $st = $item->getArticulo()->getArticuloMarca()->getArticulo()->getStockArticuloAlmacen($this->almacenDestino);
            if ($st)
            {
                $autorizado= $autorizado&&(($item->getCantidad() + $item->getArticulo()->getSReal()) <= $st->getSMax());
            }
        }
        $this->setAutorizado($autorizado);*/
    }
    
    public function getControlaStockPorMarca()    
    {
        return false;
    }    

    public function generaMovimientoCtaCte()    
    {
        return true;
    }

    public function getEnteComercial()
    {
        return $this->proveedor;
    }

    public function deleteItemMovimiento($item)
    {

    }       
}
