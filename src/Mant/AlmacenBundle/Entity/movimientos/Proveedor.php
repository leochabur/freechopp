<?php

namespace Mant\AlmacenBundle\Entity\movimientos;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Proveedor
 *
 * @ORM\Table(name="proveedores")
 * @ORM\Entity(repositoryClass="Mant\AlmacenBundle\Entity\movimientos\ProveedorRepository")
 */
class Proveedor extends EnteComercial
{


	public function __toString()
	{
		return $this->getRazonSocial();
	}
    
}
