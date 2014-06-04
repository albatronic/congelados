<?php

/**
 * Description of TablaPortes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class TablaPortes extends TablaPortesEntity {

    public function __toString() {
        return $this->getId();
    }

    /**
     * Devuelve un array con la tabla de portes de la agencia $idAgencia
     *
     * @param integer $idAgencia EL id de la agencia de transporte
     * @return array Tabla de portes
     */
    public function getTablaPortesAgencia($idAgencia) {

        $tabla = array();

        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "
                SELECT tp.IDZona,zt.Zona,tp.Kilos,tp.Importe
                FROM tabla_portes as tp, zonas_transporte as zt
                WHERE
                  tp.IDAgencia='{$idAgencia}' AND
                  tp.IDZona=zt.IDZona
                ORDER BY zt.Zona,Kilos ASC;
                ";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();
        }

        $zonaAnterior = '';
        foreach ($rows as $row) {

            if ($zonaAnterior != $row['IDZona'])
                $tabla[$row['IDZona']]['Zona'] = $row['Zona'];

            $tabla[$row['IDZona']]['Tarifa'][$row['Kilos']] = $row['Importe'];

            $zonaAnterior = $row['IDZona'];
        }

        return $tabla;
    }

    /**
     * Devuelve un array con la tabla de portes de la zona de transporte $idZona
     *
     * @param integer $idZona El id de la zona de transporte
     * @return array Tabla de portes
     */
    public function getTablaPortesZona($idZona) {

        $tabla = array();

        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "
                SELECT tp.IDAgencia,ag.Agencia,tp.Kilos,tp.Importe
                FROM tabla_portes as tp, agencias as ag
                WHERE
                  tp.IDZona='{$idZona}' AND
                  tp.IDAgencia=ag.IDAgencia
                ORDER BY ag.Agencia,Kilos ASC;
                ";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();
        }

        $agenciaAnterior = '';
        foreach ($rows as $row) {

            if ($agenciaAnterior != $row['IDAgencia'])
                $tabla[$row['IDAgencia']]['Agencia'] = $row['Agencia'];

            $tabla[$row['IDAgencia']]['Tarifa'][$row['Kilos']] = $row['Importe'];

            $agenciaAnterior = $row['IDAgencia'];
        }

        return $tabla;
    }

    /**
     * Devuelve un array con las TRES primeras agencias que tienen tarifa
     * para la zona y cantidad de kilos indicados.
     *
     * El orden es ascendente respecto a los kilos y el importe
     *
     * La estructura del array es
     *
     *      Id => El id de la agencia
     *      Value => El nombre de la agencia
     *      Kilos => Cantidad de kilos
     *      Importe => Precio para esos kilos
     *
     * @param integer $idZona
     * @param integer $kilos
     * @return array
     */
    public function getAgenciasZona($idZona, $kilos = '0') {

        $tabla = array();

        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "
                SELECT tp.IDAgencia as Id,ag.Agencia as Value,tp.Kilos as Kilos,tp.Importe as Importe
                FROM tabla_portes as tp, agencias as ag
                WHERE
                  tp.IDIDZona='{$idZona}' AND
                  tp.IDAgencia=ag.IDAgencia AND
                  tp.Kilos>='{$kilos}'
                ORDER BY tp.Kilos,tp.Importe ASC
                LIMIT 3
                ";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();
        }

        return $tabla;
    }

}

?>
