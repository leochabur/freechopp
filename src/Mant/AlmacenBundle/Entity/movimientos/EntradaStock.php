<?php

namespace Mant\AlmacenBundle\Entity\movimientos;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EntradaStock
 *
 * @ORM\Table(name="entrada_stock")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks() 
 */
class EntradaStock extends MovimientoStock
{
    /**
    * @ORM\ManyToOne(targetEntity="Mant\AlmacenBundle\Entity\Almacen") 
    * @ORM\JoinColumn(name="id_almacen_destino", referencedColumnName="id")
    * @Assert\NotBlank(message="El campo no puede permanecer en blanco!")    * 
    */   
    private $almacenDestino;   
    
    
    /**
     * @ORM\Column(name="numero", type="integer", nullable=true)
     * @ORM\GeneratedValue(strategy="AUTO")
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
        return "Entrada de Stock";
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
        return 2;
    }
    
     public function getAlmacenOrigenData()
     {
         
     }
     
     public function getAlmacenDestinoData()
     {
        return $this->almacenDestino;    
     }
     
     public function updateArticleItem(ItemMovimiento $item)
     {
        $item->setPrecioTotal($item->getCantidad() * $item->getPrecioUnitario());
        $item->getArticulo()->updateStock($item->getCantidad());
       // $item->getArticulo()->setImporte($item->getPrecioUnitario());        
     }
     
     public function getProcesado()
     {

     }
     public function setProcesado($procesado)
     {

     }
     
     public function getTipoFormulario()
     {
         return 'inp';
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
        return true;
    }    
    
    public function verificarItem($item, $sRealOrigen, $sRealDestino, $stockEnTransito)
    {
        $st = $item->getArticulo()->getArticuloMarca()->getArticulo()->getStockArticuloAlmacen($this->almacenDestino);        
        if (($item->getCantidad() + $sRealDestino) > $st->getSMax()) ///verifica que el stock actual del articulo mas lo que se compra no supere el stock maximo
        {
            $result = array();
            $result['warning'] = true;
            $result['msge'] = 'La cantidad ingresada supera el stock maximo del producto! El movimiento quedara pendiente de autorizar ';
            return $result;
        }        
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
        return false;
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
