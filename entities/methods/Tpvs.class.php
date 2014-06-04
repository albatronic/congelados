<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 15.04.2012 00:56:42
 */

/**
 * @orm:Entity(tpvs)
 */
class Tpvs extends TpvsEntity {

    public function __toString() {
        return $this->getNombre();
    }

    /**
     * Devuelve un array con los tpvs de la sucursal indicada
     * o en su defecto la sucursal en curso
     *
     * @param integer $idSucursal
     * @return array
     */
    public function fetchAll($idSucursal = '', $default = true) {

        if ($idSucursal == '')
            $idSucursal = $_SESSION['suc'];

        $em = new EntityManager("datos" . $_SESSION['emp']);
        $link = $em->getDbLink();

        if (is_resource($link)) {
            $query = "select IDTpv as Id, Nombre as Value from {$this->getDatabaseName()}.tpvs where IDSucursal='" . $idSucursal . "'";
            $em->query($query);
            $tpvs = $em->fetchResult();
            $em->desConecta();
        }

        return $tpvs;
    }
    /**
     * Devuelve un array con los tpvs definidos para la sucursal $idSucursal
     *
     * Si no se indica la sucursal, se toma la sucursal en curso
     *
     * @param integer $idSucursal El id de sucursal (opcional)
     * @return array Array de Tpvs
     */
    public function getTpvsSucursal($idSucursal='') {

        if ($idSucursal == '')
            $idSucursal = $_SESSION['suc'];

        $sucursal = new Sucursales($idSucursal);
        $rows = $sucursal->getTpvs();
        unset($sucursal);

        return $rows;
    }
}

?>