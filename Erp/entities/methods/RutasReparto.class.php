<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 14.05.2012 17:19:59
 */

/**
 * @orm:Entity(rutas_reparto)
 */
class RutasReparto extends RutasRepartoEntity {

    public function __toString() {
        return $this->getIDRuta();
    }

    /**
     * Fuerzo la sucursal a la actual
     */
    protected function load() {
        $this->setIDSucursal($_SESSION['suc']);

        parent::load();
    }

    /**
     * Devuelve un array con todas las rutas de reparto
     * de la sucursal indicada. Si no se indica ninguna
     * se toma la actual.
     *
     * Añade un item "FUERA DE RUTA" con id=0
     *
     * @param integer $IDSucursal
     * @param string $column
     * @return string
     */
    public function fetchAll($idSucursal='', $column='Descripcion') {
        $this->conecta();

        if ($idSucursal == '')
            $idSucursal = $_SESSION['suc'];

        if (is_resource($this->_dbLink)) {
            $query = "SELECT " . $this->_primaryKeyName . " as Id,$column as Value FROM " . $this->_tableName . " WHERE IDSucursal='" . $idSucursal . "' ORDER BY $column ASC;";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->setStatus($this->_em->numRows());
            $this->_em->desConecta();
            unset($this->_em);
        }
        $rows[] = array('Id' => '0', Value => ':: Fuera de Ruta');
        $rows[] = array('Id' => '', Value => ':: Indique un Valor');
        return $rows;
    }

}

?>