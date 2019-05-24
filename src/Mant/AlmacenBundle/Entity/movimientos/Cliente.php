<?php

namespace Mant\AlmacenBundle\Entity\movimientos;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cliente
 *
 * @ORM\Table(name="clientes")
 * @ORM\Entity(repositoryClass="Mant\AlmacenBundle\Entity\movimientos\ClienteRepository")
 */
class Cliente extends EnteComercial
{


	public function __toString()
	{
		return $this->getRazonSocial();
	}
}
