<?php

/**
 * Description of ZonasTransporte
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class ZonasTransporte extends ZonasTransporteEntity {

    public function __toString() {
        return $this->getZona();
    }

    /**
     * Devuelve un array con las agencias de transporte que operan en
     * la zona de transporte en curso.
     *
     * Se entiende por 'operar', que tenga alguna tarifa en la
     * tabla de portes.
     *
     * @return array (Id,Value)
     */
    public function getAgenciasOperadoras() {

        $rows = array();

        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "
                SELECT DISTINCT tp.IDAgencia AS Id, ag.Agencia AS Value
                FROM tablas_portes as tp, agencias as ag
                WHERE
                  tp.IDZona='{$this->IDZona}' AND
                  tp.IDAgencia=zt.IDAgencia
                ORDER BY ag.Agencia ASC";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();
        }

        return $rows;

    }


    /**
     * Devuelve un array con la tarifa de portes de la zona en curso
     *
     * @return array
     */
    public function getTablaPortes() {

        $tablaPortes = new TablaPortes();

        $rows = $tablaPortes->getTablaPortesZona($this->IDZona);
        unset($tablaPortes);

        return $rows;
    }
}

?>
