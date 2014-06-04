<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 17.12.2011 19:00:12
 */

/**
 * @orm:Entity(almacenes_mapas)
 */
class AlmacenesMapas extends AlmacenesMapasEntity {

    public function __toString() {
        if ($this->getUbicacion())
            return $this->getUbicacion();
        else
            return "";
    }

    /**
     * Devuelve todas las ubicaciones definidas para el almacen $idAlmacen
     * 
     * @param integer $idAlmacen
     * @param string $filtroUbicacion
     * @return array Las Ubicaciones
     */
    public function fetchAll($idAlmacen, $filtroUbicacion="%") {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = 
            "select IDUbicacion as Id, Ubicacion as Value
            from almacenes_mapas
            where IDAlmacen='{$idAlmacen}' and Ubicacion like '{$filtroUbicacion}'
            order by Ubicacion;";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        return $rows;
    }
}

?>