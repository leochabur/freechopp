<?php

namespace Mant\AlmacenBundle\Entity\movimientos;

use Doctrine\ORM\EntityRepository;
use Mant\AlmacenBundle\Entity\movimientos\TransferenciaStock;
use Mant\AlmacenBundle\Entity\movimientos\OrdenCompra;

/**
 * MovimientoStockRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MovimientoStockRepository extends EntityRepository
{
    
    public function getNotasPedidoPendientesFacturar($cliente)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT np
                           FROM MantAlmacenBundle:movimientos\NotaPedido np 
                           WHERE np.confirmado = :confirmado AND np.facturado = :facturado AND np.cliente = :cliente                                  
                           ORDER BY np.numeroComprobante'
            )
            ->setParameter('confirmado', true)
            ->setParameter('facturado', false)      
            ->setParameter('cliente', $cliente)  
            ->getResult();
    }

    public function getItemsPendientesFactrarNotasPedido($nota)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT it as item, sum(ifv.cantidad) as cantidad 
                           FROM MantAlmacenBundle:movimientos\ItemMovimiento it
                           LEFT JOIN it.itemsFacturaVenta ifv 
                           WHERE it.movimiento = :movimiento 
                           GROUP BY it'
            )
            ->setParameter('movimiento', $nota)
            ->getResult();
    }    

    public function getItemsOCPendientesDeConsumir($articulo, $movimiento)  ///
    {
        return $this->getEntityManager()
            ->createQuery('SELECT it
                           FROM MantAlmacenBundle:movimientos\ItemMovimiento it
                           JOIN it.articulo a
                           JOIN it.movimiento mov
                           WHERE (mov INSTANCE OF :movimiento) AND (a = :articulo) AND (it.stockPendiente > 0)
                           ORDER BY it.id'
            )
            ->setParameter('movimiento', $this->getEntityManager()->getClassMetadata(OrdenCompra::class))
            ->setParameter('articulo', $articulo)            
            ->getResult();
    }     
    
    public function existeMovimientoAbierto($id)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT m FROM MantAlmacenBundle:movimientos\MovimientoStock m WHERE m.id = :id and m.cerrado = :cerrado'
            )
            ->setParameter('cerrado', false)
            ->setParameter('id', $id)
            ->getOneOrNullResult();
    }    
    
    public function getNumeroComprobante($tipo, $almacen)
    {
        try {
                return $this->getEntityManager()
                    ->createQuery(
                        'SELECT n FROM MantAlmacenBundle:opciones\NumeracionFormulario n WHERE n.deposito = :deposito and n.formulario = :form'
                    )
                    ->setParameter('deposito', $almacen)
                    ->setParameter('form', $tipo)
                    ->getSingleResult();  
            } catch (\Doctrine\ORM\NoResultException $e) {
                    return null;
            }
    }
    
    public function getFormulariosAlmacenPorFecha($almacen, $desde, $hasta){
        return $this->getEntityManager()
                    ->createQuery(
                        'SELECT m FROM MantAlmacenBundle:movimientos\MovimientoStock m WHERE m.fecha between :desde and :hasta and m.depositoAfectado = :deposito and m.confirmado = :cerrado'
                    )
                    ->setParameter('desde', $desde)
                    ->setParameter('hasta', $hasta)
                    ->setParameter('deposito', $almacen)
                    ->setParameter('cerrado', true)
                    ->getResult();        
    }
    
    public function findMovimientosObservados($almacen)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT t 
                           FROM MantAlmacenBundle:movimientos\MovimientoStock t 
                           WHERE t.confirmado = :confirmado AND t.cerrado = :cerrado AND t.depositoAfectado = :almacen AND t.observado = :observado AND ((t.firmaUsuario1 IS NULL) OR (t.firmaUsuario2 IS NULL))
                           ORDER BY t.numeroComprobante'
            )
            ->setParameter('confirmado', true)
            ->setParameter('cerrado', true)            
            ->setParameter('almacen', $almacen)
            ->setParameter('observado', true)
            ->getResult();
    }

    public function getItemsCompraPendientesConsumir($almacen)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT it
                           FROM MantAlmacenBundle:movimientos\ItemMovimiento it
                           JOIN it.movimiento m
                           WHERE (it.cantidad < it.stockPendiente) AND  (m INSTANCE OF :type)'
            )
            ->setParameter('type', $this->getEntityManager()->getClassMetadata(OrdenCompra::class))
            ->getResult();
    }    
    
    public function findMovimientosArticulos($articulo, $deposito, $desde, $hasta) ///dado un articulo y un deposito devuelve todos los movimientos correspondientes al mismo
    {
        return $this->getEntityManager()
                    ->createQuery('SELECT m as most, it.cantidad as cant, it.precioUnitario as precio, it.precioTotal as total, a.descripcion as codigo
                                   FROM MantAlmacenBundle:movimientos\MovimientoStock m
                                   JOIN m.items it
                                   JOIN it.articulo ama
                                   JOIN ama.articuloMarca am
                                   JOIN am.articulo a
                                   WHERE a = :articulo AND ama.almacen = :deposito AND m.confirmado = :confirmado AND m.fecha BETWEEN :desde AND :hasta
                                   ORDER BY m.fecha, m.createdAt')
                    ->setParameter('confirmado', true)
                    ->setParameter('deposito', $deposito)
                    ->setParameter('articulo', $articulo)       
                    ->setParameter('desde', $desde)   
                    ->setParameter('hasta', $hasta)   
                    ->getResult();
    }
    
    public function movimientosPendientesDeAutorizar($almacen)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT t FROM MantAlmacenBundle:movimientos\MovimientoStock t WHERE t.confirmado = :confirmado AND t.cerrado = :cerrado AND t.depositoAutorizante = :almacen AND t.autorizado = :autorizado ORDER BY t.numeroComprobante'
            )
            ->setParameter('confirmado', true)
            ->setParameter('cerrado', false)     
            ->setParameter('autorizado', false)                     
            ->setParameter('almacen', $almacen)
            ->getResult();
    }
    
    public function findDocumentosAFacturar($proveedor)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT de 
                           FROM MantAlmacenBundle:movimientos\DocumentoEntrada de 
                           INNER JOIN MantAlmacenBundle:movimientos\OrdenCompra oc WITH oc = de.documentoAsociado
                           WHERE oc.proveedor = :proveedor AND de.afectadoAFactura = :afectado'
            )
            ->setParameter('proveedor', $proveedor)           
            ->setParameter('afectado', false)                
            ->getResult();
    } 
    
    public function movimientosEnPausa($almacen)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT t FROM MantAlmacenBundle:movimientos\MovimientoStock t WHERE t.confirmado = :confirmado AND t.depositoAfectado = :almacen ORDER BY t.fecha'
            )
            ->setParameter('confirmado', false)
            ->setParameter('almacen', $almacen)
            ->getResult();
    }    
}
