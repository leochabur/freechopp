<?php

namespace Mant\AlmacenBundle\Entity\movimientos;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * SalidaStock
 *
 * @ORM\Table(name="salidas_stock")
 * @ORM\Entity
 */
class SalidaStock extends MovimientoStock
{
    /**
    * @ORM\ManyToOne(targetEntity="Mant\AlmacenBundle\Entity\Almacen") 
    * @ORM\JoinColumn(name="id_almacen_origen", referencedColumnName="id")
    * @Assert\NotBlank(message="El campo no puede permanecer en blanco!")  * 
    */   
    private $almacenOrigen;        
    
    public function getDescripcionFormulario()
    {
        return "Salida de Stock";
    }
    
    public function updateItems()
    {
        
    }    

    /**
     * Set almacenOrigen
     *
     * @param \Mant\AlmacenBundle\Entity\Almacen $almacenOrigen
     * @return SalidaStock
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
    
    public function getInstance()
    {
        return 3;
    }
   
     public function getAlmacenOrigenData()
     {
         return $this->almacenOrigen;
     }
     
     public function getAlmacenDestinoData()
     {
        return $this->almacenOrigen;   
     } 
     
     public function updateArticleItem(ItemMovimiento $item)
     {
        $item->setPrecioTotal($item->getCantidad() * $item->getPrecioUnitario());
        $item->getArticulo()->updateStock((-1)*($item->getCantidad()));            
     }     
   
     public function getProcesado()
     {

     }
     public function setProcesado($procesado)
     {

     }
     public function getTipoFormulario()
     {
         return 'out';
     }     
     
    public function getDepositoAAfectar()     
    {
        return $this->almacenOrigen;
    }
    
    public function getItemConfirmado()    
    {
        return true;
    }
    
    public function marcarProcesado($procesado){

    }    
    
    public function movimientoConfirmado()
    {
        return true;
    }    
    
    public function verificarItem($item, $sRealOrigen, $sRealDestino, $stockEnTransito)
    {
        return array('ok'=>true, 'warning'=>false, 'msge' => '');
    }    
    
    public function getDepositoAutorizante(){
        return null;
    }    
    
    public function getNameProveedor()
    {
        return "";
    }    
    
    public function setAutorizacionFormulario()
    {
        $autorizado = true;
        $this->setAutorizado($autorizado);
    } 
    
    public function getControlaStockPorMarca()    
    {
        return true;
    }    

    public function generaMovimientoCtaCte()    
    {
        return false;
    }    

    public function getEnteComercial()
    {
        return null;
    }        
}
