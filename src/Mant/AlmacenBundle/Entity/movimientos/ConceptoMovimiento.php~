<?php

namespace Mant\AlmacenBundle\Entity\movimientos;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConceptoMovimiento
 *
 * @ORM\Table(name="conceptos_movimiento")
 * @ORM\Entity(repositoryClass="Mant\AlmacenBundle\Entity\movimientos\ConceptoMovimientoRepository")
 */
class ConceptoMovimiento
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
     * @var string
     *
     * @ORM\Column(name="concepto", type="string", length=150)
     */
    private $concepto;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="movAfectado", type="string", length=150)
     */
    private $movAfectado;
    
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $dobleFirma;
        
        
    /**
    * @ORM\ManyToOne(targetEntity="GestionUsuariosBundle\Entity\RoleUsuario") 
    * @ORM\JoinColumn(name="id_role_firma_1", referencedColumnName="id")
    */   
    private $roleFirma1;

    /**
    * @ORM\ManyToOne(targetEntity="GestionUsuariosBundle\Entity\RoleUsuario") 
    * @ORM\JoinColumn(name="id_role_firma_2", referencedColumnName="id")
    */   
    private $roleFirma2;
    
    /**
    * @ORM\ManyToOne(targetEntity="GestionUsuariosBundle\Entity\RoleUsuario") 
    * @ORM\JoinColumn(name="id_role_carga_observacion", referencedColumnName="id")
    */   
    private $roleCargaObservacion; ///para el caso de los formularios que queden observados y se requiera la doble firma, indica cual es el role del usuario que debe introducior el comentario     

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $observaFormulario;  ////para saber si marca el movimiento como observado
    
    /**
    * @ORM\ManyToOne(targetEntity="GestionUsuariosBundle\Entity\RoleUsuario") 
    * @ORM\JoinColumn(name="id_role_firma_autorizante", referencedColumnName="id")
    */   
    private $roleFirmaAutorizante;    ////en los casos como el de las OC que exceden el stock maximo, define cual es el role del usuario que debe autorizarla 
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
     * Set concepto
     *
     * @param string $concepto
     * @return ConceptoMovimiento
     */
    public function setConcepto($concepto)
    {
        $this->concepto = $concepto;

        return $this;
    }

    /**
     * Get concepto
     *
     * @return string 
     */
    public function getConcepto()
    {
        return $this->concepto;
    }
    
    public function __toString()
    {
        return $this->concepto;
    }

    /**
     * Set movAfectado
     *
     * @param string $movAfectado
     * @return ConceptoMovimiento
     */
    public function setMovAfectado($movAfectado)
    {
        $this->movAfectado = $movAfectado;

        return $this;
    }

    /**
     * Get movAfectado
     *
     * @return string 
     */
    public function getMovAfectado()
    {
        return $this->movAfectado;
    }

    /**
     * Set dobleFirma
     *
     * @param boolean $dobleFirma
     * @return ConceptoMovimiento
     */
    public function setDobleFirma($dobleFirma)
    {
        $this->dobleFirma = $dobleFirma;

        return $this;
    }

    /**
     * Get dobleFirma
     *
     * @return boolean 
     */
    public function getDobleFirma()
    {
        return $this->dobleFirma;
    }

    /**
     * Set roleFirma1
     *
     * @param \GestionUsuariosBundle\Entity\RoleUsuario $roleFirma1
     * @return ConceptoMovimiento
     */
    public function setRoleFirma1(\GestionUsuariosBundle\Entity\RoleUsuario $roleFirma1 = null)
    {
        $this->roleFirma1 = $roleFirma1;

        return $this;
    }

    /**
     * Get roleFirma1
     *
     * @return \GestionUsuariosBundle\Entity\RoleUsuario 
     */
    public function getRoleFirma1()
    {
        return $this->roleFirma1;
    }

    /**
     * Set roleFirma2
     *
     * @param \GestionUsuariosBundle\Entity\RoleUsuario $roleFirma2
     * @return ConceptoMovimiento
     */
    public function setRoleFirma2(\GestionUsuariosBundle\Entity\RoleUsuario $roleFirma2 = null)
    {
        $this->roleFirma2 = $roleFirma2;

        return $this;
    }

    /**
     * Get roleFirma2
     *
     * @return \GestionUsuariosBundle\Entity\RoleUsuario 
     */
    public function getRoleFirma2()
    {
        return $this->roleFirma2;
    }

    /**
     * Set observaFormulario
     *
     * @param boolean $observaFormulario
     * @return ConceptoMovimiento
     */
    public function setObservaFormulario($observaFormulario)
    {
        $this->observaFormulario = $observaFormulario;

        return $this;
    }

    /**
     * Get observaFormulario
     *
     * @return boolean 
     */
    public function getObservaFormulario()
    {
        return $this->observaFormulario;
    }
    
    public function userFirmaConcepto($usuario) ////consulta si el usuarui ouede firmar el concepto de acuerdo a su role
    {
        return ($usuario->getPermisos()->contains($this->roleFirma1) || $usuario->getPermisos()->contains($this->roleFirma2));
    }
    
    public function userAutorizaConcepto($usuario) ////consulta si el usuarui ouede autorizar el concepto de acuerdo a su role (Por ej las OC que exceden el stock maximo)
    {
        return ($usuario->getPermisos()->contains($this->roleFirmaAutorizante));
    }    

    /**
     * Set roleFirmaAutorizante
     *
     * @param \GestionUsuariosBundle\Entity\RoleUsuario $roleFirmaAutorizante
     * @return ConceptoMovimiento
     */
    public function setRoleFirmaAutorizante(\GestionUsuariosBundle\Entity\RoleUsuario $roleFirmaAutorizante = null)
    {
        $this->roleFirmaAutorizante = $roleFirmaAutorizante;

        return $this;
    }

    /**
     * Get roleFirmaAutorizante
     *
     * @return \GestionUsuariosBundle\Entity\RoleUsuario 
     */
    public function getRoleFirmaAutorizante()
    {
        return $this->roleFirmaAutorizante;
    }

    /**
     * Set roleCargaObservacion
     *
     * @param \GestionUsuariosBundle\Entity\RoleUsuario $roleCargaObservacion
     * @return ConceptoMovimiento
     */
    public function setRoleCargaObservacion(\GestionUsuariosBundle\Entity\RoleUsuario $roleCargaObservacion = null)
    {
        $this->roleCargaObservacion = $roleCargaObservacion;

        return $this;
    }

    /**
     * Get roleCargaObservacion
     *
     * @return \GestionUsuariosBundle\Entity\RoleUsuario 
     */
    public function getRoleCargaObservacion()
    {
        return $this->roleCargaObservacion;
    }
}
