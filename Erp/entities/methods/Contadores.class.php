<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 15.02.2012 12:41:19
 */

/**
 * @orm:Entity(contadores)
 */
class Contadores extends ContadoresEntity {

    public function __toString() {
        return $this->getIDContador();
    }

    /**
     * Devuelve un array (Id,Value) con las series de contadores
     * de la sucursal y tipo indicado
     * El tipo se corresponde con los definidos en la clase abstracta 'TiposContadores'
     *
     * @param integer $idSucursal
     * @param integer $idTipo
     * @return array Array con los contadores
     */
    public function fetchAll($idSucursal, $idTipo) {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = "select IDContador as Id, Serie as Value from {$this->_dataBaseName}.contadores where IDSucursal='{$idSucursal}' and IDTipo='{$idTipo}' order by Predefinido DESC;";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        return $rows;
    }

    /**
     * Devuelve el objeto contador para la sucursal, tipo y serie indicado
     * Si no se indica serie, se toma la por defecto.
     * 
     * @param integer $idSucursal
     * @param integer $idTipo
     * @param string $serie (opcional)
     * @return Contadores El objeto contador
     */
    public function dameContador($idSucursal, $idTipo, $serie='') {

        if ($serie != '')
            $filtroSerie = "Serie='{$serie}'";
        else
            $filtroSerie = "Predefinido='1'";

        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = "select IDContador from {$this->_dataBaseName}.contadores where (IDSucursal='{$idSucursal}') and (IDTipo='{$idTipo}') and ({$filtroSerie});";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
            $contador = new Contadores($rows[0]['IDContador']);
        }
        return $contador;
    }

    /**
     * Genera un número de documento en base al tipo de contador
     * Incrementa en uno el valor actual y lo actualiza
     * 
     * @return string El número de documento
     */
    public function asignaContador() {
        $nuevo = $this->Contador + 1;
        $documento = $this->getSerie() . (string) ($nuevo);
        $this->setContador($nuevo);
        $this->save();

        return $documento;
    }
}

?>