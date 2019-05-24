<?php

namespace Mant\AlmacenBundle\Entity\movimientos;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MovimientoStock
 *
 * @ORM\Table(name="movimientos_stock")
 * @ORM\Entity(repositoryClass="Mant\AlmacenBundle\Entity\movimientos\MovimientoStockRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="integer")
 * @ORM\DiscriminatorMap({1:"MovimientoStock", 2:"EntradaStock", 3:"SalidaStock", 4:"OrdenCompra", 5:"NotaPedido", 6:"FacturaVenta"})
 * @ORM\HasLifecycleCallbacks()
 */
abstract class MovimientoStock
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date")
     * @Assert\NotBlank(message="El campo no puede permanecer en blanco!")      * 
     */
    private $fecha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;
    
    
    /**
    * @ORM\ManyToOne(targetEntity="GestionUsuariosBundle\Entity\Usuario") 
    * @ORM\JoinColumn(name="id_usuario_alta", referencedColumnName="id")
    */      
    private $userAlta;
    
    
    /**
     * @ORM\OneToMany(targetEntity="ItemMovimiento", mappedBy="movimiento", cascade={"persist", "remove", "refresh"})
     */    
    private $items;
    
    /**
     *
     * @ORM\Column(name="cerrado", type="boolean") 
     */
    private $cerrado = false; ///indica si el formulario a sido procesado despues de la carga, por ejemplo las transferencias que necesitan ser confirmadas por el destinatario
    
    /**
     *
     * @ORM\Column(name="observado", type="boolean", nullable=false, options={"default":false})
     */
    private $observado = false; ///para los formulario que asi lo requieran indica que hay algo fuera de lo normal, se necesita finalizar por parte de algun administrador
    
      /**
     *
     * @ORM\Column(name="confirmado", type="boolean") 
     */
    private $confirmado = false; ///indica si el formulario ya se le ha asignado un numero y ha quedado confirmado en la BD
    
      /**
     *
     * @ORM\Column(name="facturado", type="boolean", nullable=true, options={"default"=false}) 
     */
    private $facturado = false; ///para el caso de las Notas de Pedido indica si ya se han facturado
    
    
    /**
     * @var integer
     
     * @ORM\Column(name="numeroComprobante", type="integer", nullable=true)
     */
    private $numeroComprobante;

    /**
    * @ORM\ManyToOne(targetEntity="Mant\AlmacenBundle\Entity\Almacen") 
    * @ORM\JoinColumn(name="id_deposito", referencedColumnName="id")
    */   
    private $depositoAfectado;    
    
    /**
    * @ORM\ManyToOne(targetEntity="GestionUsuariosBundle\Entity\Usuario") 
    * @ORM\JoinColumn(name="id_firma_user_1", referencedColumnName="id")
    */      
    private $firmaUsuario1;  //////////cuando el formulario quede observado debe ser firmado por dos usuarios
    
    /**
    * @ORM\ManyToOne(targetEntity="GestionUsuariosBundle\Entity\Usuario") 
    * @ORM\JoinColumn(name="id_firma_user_2", referencedColumnName="id")
    */      
    private $firmaUsuario2;    
    
      /**
     * @var Text
     
     * @ORM\Column(type="text", nullable=true)
     */  
    private $comentario;
    
    /**
    * @ORM\ManyToOne(targetEntity="ConceptoMovimiento") 
    * @ORM\JoinColumn(name="id_concepto", referencedColumnName="id")       
    */   
    private $conceptoEntrada;      
    
    /**
    * @ORM\ManyToOne(targetEntity="Mant\AlmacenBundle\Entity\Almacen") 
    * @ORM\JoinColumn(name="id_deposito_autorizante", referencedColumnName="id")
    */   
    private $depositoAutorizante;    ////representa cual es el deposito que tiene que autorizar el documento    
    
    
    /**
    * @ORM\ManyToOne(targetEntity="MovimientoStock", inversedBy="documentoBase") 
    * @ORM\JoinColumn(name="id_movimiento", referencedColumnName="id")
    */          
    private $documentoAsociado;   ////hace referencia al movimiento que le da origen
    
    
    /**
     * @ORM\OneToMany(targetEntity="MovimientoStock", mappedBy="documentoAsociado")
     */
    private $documentoBase;
    
    /**
    * @ORM\Column(type="boolean", nullable=true, options={"default" = true}) 
    **/
    private $autorizado;////para el caso que se requiera se marca como autorizado o no , por ejemplo para el caso de las ordenes de compra que soliciten mas cantidad que el stock maximo marca el movimiento como pendiente de autorizar

    /**
    * @ORM\ManyToOne(targetEntity="GestionUsuariosBundle\Entity\Usuario") 
    * @ORM\JoinColumn(name="id_firma_autorizante", referencedColumnName="id")
    */      
    private $firmaAutorizante;    

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaAutorizacion", type="datetime", nullable=true)
     */
    private $fechaAutorizacion; ///en el caso, por ejemplo, de las OC que exceden el stock maximo y quedan como pednientes, al monento de salvar la observacion se actualiza la fecha que se realiza la accion correpondiente
    
    protected $roleAutorizante;///cada Movimiento de Stcok que hereda de la clase, instancia cual es el role que se encaragra, de ser necesario, de autorizar el movimiento
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
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return MovimientoStock
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return MovimientoStock
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set userAlta
     *
     * @param \GestionUsuariosBundle\Entity\Usuario $userAlta
     * @return MovimientoStock
     */
    public function setUserAlta(\GestionUsuariosBundle\Entity\Usuario $userAlta = null)
    {
        $this->userAlta = $userAlta;

        return $this;
    }

    /**
     * Get userAlta
     *
     * @return \GestionUsuariosBundle\Entity\Usuario 
     */
    public function getUserAlta()
    {
        return $this->userAlta;
    }

    /**
     * Add items
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $items
     * @return MovimientoStock
     */
    public function addItem(\Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $items
     */
    public function removeItem(\Mant\AlmacenBundle\Entity\movimientos\ItemMovimiento $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItems()
    {
        return $this->items;
    }
    
    public abstract function getDescripcionFormulario();

    public function __toString()
    {
        return $this->getDescripcionFormulario();
    }

    /**
     * Set cerrado
     *
     * @param boolean $cerrado
     * @return MovimientoStock
     */
    public function setCerrado($cerrado)
    {
        $this->cerrado = $cerrado;

        return $this;
    }

    /**
     * Get cerrado
     *
     * @return boolean 
     */
    public function getCerrado()
    {
        return $this->cerrado;
    }
    
    /**
     * @ORM\PrePersist
     */
     public function setPrePersist()
     {
         $this->createdAt = new \DateTime();
         $this->depositoAfectado = $this->getDepositoAAfectar();
         $this->depositoAutorizante = $this->getDepositoAutorizante();
     }
     
     public abstract function getInstance();
     
     public abstract function getAlmacenOrigenData();
     public abstract function getAlmacenDestinoData();
     public abstract function updateArticleItem(ItemMovimiento $item);
     public abstract function getProcesado();
     public abstract function setProcesado($procesado);         
     public abstract function getTipoFormulario();
     public abstract function getDepositoAAfectar(); //////para saber a que deposito debe afectar el formulario
     public abstract function getDepositoAutorizante(); //////para saber a que deposito debe autorizar el formulario 
     public abstract function getItemConfirmado();////devuelve si el item que se agrega al formulario queda confirmado o no
     public abstract function marcarProcesado($procesado);
     public abstract function movimientoConfirmado(); /////indica si el movimiento una vez cargado debe ser confirmado, caso las transferencias que deben ser confirmadas por el receptor y/o las ordenes de compra
     public abstract function verificarItem($item, $sRealOrigen, $sRealDestino, $stockEnTransito);
     public abstract function getNameProveedor();
     public abstract function setAutorizacionFormulario();     
     public abstract function getControlaStockPorMarca(); ///indica si el control de stock se realiza para los articulos con marca o solo articulos de manera abstract
     public abstract function generaMovimientoCtaCte();
     public abstract function getEnteComercial();
    /**
     * Set numeroComprobante
     *
     * @param integer $numeroComprobante
     * @return MovimientoStock
     */
    public function setNumeroComprobante($numeroComprobante)
    {
        $this->numeroComprobante = $numeroComprobante;

        return $this;
    }

    /**
     * Get numeroComprobante
     *
     * @return integer 
     */
    public function getNumeroComprobante()
    {
        return $this->numeroComprobante;
    }

    /**
     * Set depositoAfectado
     *
     * @param \Mant\AlmacenBundle\Entity\Almacen $depositoAfectado
     * @return MovimientoStock
     */
    public function setDepositoAfectado(\Mant\AlmacenBundle\Entity\Almacen $depositoAfectado = null)
    {
        $this->depositoAfectado = $depositoAfectado;

        return $this;
    }

    /**
     * Get depositoAfectado
     *
     * @return \Mant\AlmacenBundle\Entity\Almacen 
     */
    public function getDepositoAfectado()
    {
        return $this->depositoAfectado;
    }

    /**
     * Set observado
     *
     * @param boolean $observado
     * @return MovimientoStock
     */
    public function setObservado($observado)
    {
        $this->observado = $observado;

        return $this;
    }

    /**
     * Get observado
     *
     * @return boolean 
     */
    public function getObservado()
    {
        return $this->observado;
    }

    /**
     * Set confirmado
     *
     * @param boolean $confirmado
     * @return MovimientoStock
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
     * Set comentario
     *
     * @param string $comentario
     * @return MovimientoStock
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;

        return $this;
    }

    /**
     * Get comentario
     *
     * @return string 
     */
    public function getComentario()
    {
        return $this->comentario;
    }

    /**
     * Set firmaUsuario1
     *
     * @param \GestionUsuariosBundle\Entity\Usuario $firmaUsuario1
     * @return MovimientoStock
     */
    public function setFirmaUsuario1(\GestionUsuariosBundle\Entity\Usuario $firmaUsuario1 = null)
    {
        $this->firmaUsuario1 = $firmaUsuario1;

        return $this;
    }

    /**
     * Get firmaUsuario1
     *
     * @return \GestionUsuariosBundle\Entity\Usuario 
     */
    public function getFirmaUsuario1()
    {
        return $this->firmaUsuario1;
    }

    /**
     * Set firmaUsuario2
     *
     * @param \GestionUsuariosBundle\Entity\Usuario $firmaUsuario2
     * @return MovimientoStock
     */
    public function setFirmaUsuario2(\GestionUsuariosBundle\Entity\Usuario $firmaUsuario2 = null)
    {
        $this->firmaUsuario2 = $firmaUsuario2;

        return $this;
    }

    /**
     * Get firmaUsuario2
     *
     * @return \GestionUsuariosBundle\Entity\Usuario 
     */
    public function getFirmaUsuario2()
    {
        return $this->firmaUsuario2;
    }

    /**
     * Set conceptoEntrada
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\ConceptoMovimiento $conceptoEntrada
     * @return MovimientoStock
     */
    public function setConceptoEntrada(\Mant\AlmacenBundle\Entity\movimientos\ConceptoMovimiento $conceptoEntrada = null)
    {
        $this->conceptoEntrada = $conceptoEntrada;

        return $this;
    }

    /**
     * Get conceptoEntrada
     *
     * @return \Mant\AlmacenBundle\Entity\movimientos\ConceptoMovimiento 
     */
    public function getConceptoEntrada()
    {
        return $this->conceptoEntrada;
    }

    /**
     * Set depositoAutorizante
     *
     * @param \Mant\AlmacenBundle\Entity\Almacen $depositoAutorizante
     * @return MovimientoStock
     */
    public function setDepositoAutorizante(\Mant\AlmacenBundle\Entity\Almacen $depositoAutorizante = null)
    {
        $this->depositoAutorizante = $depositoAutorizante;

        return $this;
    }

    /**
     * Set documentoAsociado
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\MovimientoStock $documentoAsociado
     * @return MovimientoStock
     */
    public function setDocumentoAsociado(\Mant\AlmacenBundle\Entity\movimientos\MovimientoStock $documentoAsociado = null)
    {
        $this->documentoAsociado = $documentoAsociado;

        return $this;
    }

    /**
     * Get documentoAsociado
     *
     * @return \Mant\AlmacenBundle\Entity\movimientos\MovimientoStock 
     */
    public function getDocumentoAsociado()
    {
        return $this->documentoAsociado;
    }

    /**
     * Add documentoBase
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\MovimientoStock $documentoBase
     * @return MovimientoStock
     */
    public function addDocumentoBase(\Mant\AlmacenBundle\Entity\movimientos\MovimientoStock $documentoBase)
    {
        $this->documentoBase[] = $documentoBase;

        return $this;
    }

    /**
     * Remove documentoBase
     *
     * @param \Mant\AlmacenBundle\Entity\movimientos\MovimientoStock $documentoBase
     */
    public function removeDocumentoBase(\Mant\AlmacenBundle\Entity\movimientos\MovimientoStock $documentoBase)
    {
        $this->documentoBase->removeElement($documentoBase);
    }

    /**
     * Get documentoBase
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDocumentoBase()
    {
        return $this->documentoBase;
    }
    
    public function firmarMovimientoObservado($usuario)
    {
        if ((!$this->firmaUsuario1) && ((!$this->firmaUsuario2)))/// aun no ha firmado ningun usuario
        { 
            $this->firmaUsuario1 = $usuario;
        }
        elseif (!$this->firmaUsuario2)
        {
            if ($this->firmaUsuario1->tieneRolDe($usuario->getPermisos())) ///el usuario ya ha firmado el movimiento en la firma 1
            {
                throw new \Exception('El rol del usuario ya ha realizado la firma!');
            }
            else
            {
                $this->firmaUsuario2 = $usuario;
            }
        }
        else
        {
            throw new \Exception('Ya se ha completado el esquema de firmas!');
        }
    }
    
    public function esquemaFirmaCompleto()
    {
        return ($this->firmaUsuario1 && $this->firmaUsuario2);
    }

    /**
     * Set autorizado
     *
     * @param boolean $autorizado
     * @return MovimientoStock
     */
    public function setAutorizado($autorizado)
    {
        $this->autorizado = $autorizado;

        return $this;
    }

    /**
     * Get autorizado
     *
     * @return boolean 
     */
    public function getAutorizado()
    {
        return $this->autorizado;
    }

    /**
     * Set firmaAutorizante
     *
     * @param \GestionUsuariosBundle\Entity\Usuario $firmaAutorizante
     * @return MovimientoStock
     */
    public function setFirmaAutorizante(\GestionUsuariosBundle\Entity\Usuario $firmaAutorizante = null)
    {
        $this->firmaAutorizante = $firmaAutorizante;

        return $this;
    }

    /**
     * Get firmaAutorizante
     *
     * @return \GestionUsuariosBundle\Entity\Usuario 
     */
    public function getFirmaAutorizante()
    {
        return $this->firmaAutorizante;
    }

    /**
     * Set facturado
     *
     * @param boolean $facturado
     * @return MovimientoStock
     */
    public function setFacturado($facturado)
    {
        $this->facturado = $facturado;

        return $this;
    }

    /**
     * Get facturado
     *
     * @return boolean 
     */
    public function getFacturado()
    {
        return $this->facturado;
    }

    /**
     * Set fechaAutorizacion
     *
     * @param \DateTime $fechaAutorizacion
     * @return MovimientoStock
     */
    public function setFechaAutorizacion($fechaAutorizacion)
    {
        $this->fechaAutorizacion = $fechaAutorizacion;

        return $this;
    }

    /**
     * Get fechaAutorizacion
     *
     * @return \DateTime 
     */
    public function getFechaAutorizacion()
    {
        return $this->fechaAutorizacion;
    }
    
    public function getImporteTotal()
    {
        $sum = 0;
        foreach ($this->items as $item){
            $sum+=$item->getPrecioTotal();
        }
        return $sum;
    }
}
