<?php

namespace GestionUsuariosBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Usuario
 *
 * @ORM\Table(name="usuarios")
 * @ORM\Entity(repositoryClass="GestionUsuariosBundle\Entity\UsuarioRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Usuario implements AdvancedUserInterface, \Serializable
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
     * @ORM\Column(name="username", type="string", length=40)
     */
    private $username;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=140)
     */
    private $nombre;
    
    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=140)
     */
    private $apellido;
    
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="clave", type="string", length=255)
     */
    private $clave;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var string
     *
    /**@ORM\Column(type="string", columnDefinition="ENUM('ROLE_USER', 'ROLE_ADMIN', 'ROLE_PAX', 'ROLE_SUPER_ADMIN')", nullable=false) 
     */
    private $role;
    
    /**
     * @ORM\ManyToMany(targetEntity="Mant\AlmacenBundle\Entity\Almacen")
     * @ORM\JoinTable(name="depositos_por_usuario",
     *      joinColumns={@ORM\JoinColumn(name="id_usuario", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_deposito", referencedColumnName="id")}
     *      )
     */
    private $depositos;    


    /**
     * @Assert\Count(
     *      max = 1,
     *      maxMessage = "Solo se permite asignar un role al usuario!"
     * )
     * @ORM\ManyToMany(targetEntity="RoleUsuario")
     * @ORM\JoinTable(name="roles_por_usuario",
     *      joinColumns={@ORM\JoinColumn(name="id_usuario", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_role", referencedColumnName="id")}
     *      )
     */
    private $roles;    

    public function __construct(){
        $this->activo=true;
        $this->depositos = new \Doctrine\Common\Collections\ArrayCollection();        
    }
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
     * Set username
     *
     * @param string $username
     * @return Usuario
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set clave
     *
     * @param string $clave
     * @return Usuario
     */
    public function setClave($clave)
    {
        $this->clave = $clave;

        return $this;
    }

    /**
     * Get clave
     *
     * @return string 
     */
    public function getClave()
    {
        return $this->clave;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return Usuario
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return Usuario
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Usuario
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     * @return Usuario
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string 
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Usuario
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
    
    /**
    * @ORM\PrePersist
    */
    public function setActiveValue()
    {
        $this->activo = 1;
    }
    

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    
    public function getRoles()
    {
        return array($this->role);
    }
    
    public function getPassword()
    {
        return $this->clave;
    }
    
    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        
    }
    
    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->clave,
            $this->activo
        ));
    }
     
    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->clave,
            $this->activo
        ) = unserialize($serialized);
    }
    
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()  
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->activo;
    }    
    
    public function __toString()
    {
        return $this->apellido.", ".$this->nombre;
    }

    /**
     * Add depositos
     *
     * @param \Mant\AlmacenBundle\Entity\Almacen $depositos
     * @return Usuario
     */
    public function addDeposito(\Mant\AlmacenBundle\Entity\Almacen $depositos)
    {
        $this->depositos[] = $depositos;

        return $this;
    }

    /**
     * Remove depositos
     *
     * @param \Mant\AlmacenBundle\Entity\Almacen $depositos
     */
    public function removeDeposito(\Mant\AlmacenBundle\Entity\Almacen $depositos)
    {
        $this->depositos->removeElement($depositos);
    }

    /**
     * Get depositos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDepositos()
    {
        return $this->depositos;
    }

    /**
     * Add roles
     *
     * @param \GestionUsuariosBundle\Entity\RoleUsuario $roles
     * @return Usuario
     */
    public function addRole(\GestionUsuariosBundle\Entity\RoleUsuario $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \GestionUsuariosBundle\Entity\RoleUsuario $roles
     */
    public function removeRole(\GestionUsuariosBundle\Entity\RoleUsuario $roles)
    {
        $this->roles->removeElement($roles);
    }
    
    public function getPermisos()
    {
        return $this->roles;
    }
    
    public function tieneRolDe($roles)
    {
        foreach ($roles as $role)
        {
            if ($this->roles->contains($role))
                return true;
        }
        return false;
    }
    
}
