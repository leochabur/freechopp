<?php

namespace Mant\AlmacenBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Mant\AlmacenBundle\Entity\Articulo;

/**
 * ArticuloRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticuloDepositoRepository extends EntityRepository
{

    public function articulosPorDeposito($deposito)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT ad FROM MantAlmacenBundle:ArticuloDeposito ad WHERE ad.almacen = :deposito and ad.activo= :activo'
            )
            ->setParameter('deposito', $deposito)
            ->setParameter('activo', true)
            ->getResult();
    }    

    public function articulosAptosFraccionPorDeposito($deposito)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT ab.descripcion as base, ar.descripcion as fraccion, ab.cantXUnidad as cantidad, ad.id as idArt
                 FROM MantAlmacenBundle:ArticuloDeposito ad 
                 JOIN ad.articulo ar
                 JOIN ar.articuloBase ab
                 WHERE ad.almacen = :deposito and ad.activo= :activo'
            )
            ->setParameter('deposito', $deposito)
            ->setParameter('activo', true)
            ->getResult();
    }  

    public function getArticuloDeposito($articulo)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT ad FROM MantAlmacenBundle:ArticuloDeposito ad WHERE ad.articulo = :articulo')
            ->setParameter('articulo', $articulo)
            ->getOneOrNullResult();
    }  

    public function articulosCambioPrecios($deposito)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT ad
                 FROM MantAlmacenBundle:ArticuloDeposito ad 
                 WHERE ad.almacen = :deposito and ad.activo= :activo AND ad.precioCompra < ad.ultPrecioCompra AND ad.ultPrecioCompra <> 0'
            )
            ->setParameter('deposito', $deposito)
            ->setParameter('activo', true)
            ->getResult();
    }  


}
