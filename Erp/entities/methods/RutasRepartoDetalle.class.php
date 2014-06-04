<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 14.05.2012 18:12:47
 */

/**
 * @orm:Entity(rutas_reparto_detalle)
 */
class RutasRepartoDetalle extends RutasRepartoDetalleEntity {

    public function __toString() {
        return $this->getId();
    }

    /**
     * Devuelve el id de ruta, el día de reparto y el id del repartidor asignados
     * a la dirección de entrega $idDirec a partir de $fecha
     * 
     * El día de visita será el día de la semana inmediatamente siguiente 
     * al indicado en $fecha
     * 
     * Si la direccion de entrega no estuviese incluida en ninguna ruta,
     * se devuelve la ruta 0 (Fuera de ruta), día 0 y repartidor 0
     * 
     * @param integer $idDirec
     * @param date $fecha
     * @return array Array con el id de ruta y el día
     */
    public function getRutaDiaRepartidor($idDirec,$fecha) {

        $ruta = array();

        $fecha = new Fecha($fecha);
        $diaSemana = $fecha->getDiaSemana();
        unset($fecha);

        $rows = $this->cargaCondicion("IDRuta,Dia,IDRepartidor","IDDirec='{$idDirec}' and Dia>'$diaSemana'","Dia ASC");
        if (count($rows) == 0) {
            $rows = $this->cargaCondicion("IDRuta,Dia,IDRepartidor","IDDirec='{$idDirec}'","Dia ASC");
            if (count($rows) == 0)
                $rows[0] = array('IDRuta' => '0', 'Dia' => '0', 'IDRepartidor' => '0');
        }
        
        $ruta = $rows[0];

        return $ruta;
    }
}

?>