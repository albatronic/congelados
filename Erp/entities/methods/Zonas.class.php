<?php

/**
 * Description of Zonas
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Zonas extends ZonasEntity {

    public function __toString() {
        return $this->getZona();
    }

    /**
     * Fuerzo la sucursal a la actual
     */
    protected function load() {
        $this->setIDSucursal($_SESSION['suc']);

        parent::load();
    }

    /**
     * Devuelve un array con todas las zonas comerciales
     * de la sucursal indicada. Si no se indica ninguna
     * se toma la actual.
     *
     * @param integer $IDSucursal
     * @param string $column
     * @return string
     */
    public function fetchAll($idSucursal='', $column='Zona') {
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
        $rows[] = array('Id' => '', Value => ':: Indique un Valor');
        return $rows;
    }

}

?>
