<?php

/**
 * Description of Agencias
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Agencias extends AgenciasEntity {

    public function __toString() {
        return $this->getAgencia();
    }


    /**
     * Devuelve un array con las Zonas de transporte donde opera la agencia
     * en curso.
     *
     * Se entiende por 'operar', que tenga alguna tarifa en la
     * tabla de portes.
     *
     * @return array (Id,Value)
     */
    public function getZonasOperacion() {

        $rows = array();

        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "
                SELECT DISTINCT tp.IDZona AS Id, zt.Zona AS Value
                FROM tablas_portes as tp, zonas_transporte as zt
                WHERE
                  tp.IDAgencia='{$this->IDAgencia}' AND
                  tp.IDZona=zt.IDZona
                ORDER BY zt.Zona ASC";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();
        }

        return $rows;

    }

    /**
     * Devuelve un array con la tarifa de portes de la agencia en curso
     * 
     * @return array
     */
    public function getTablaPortes() {

        $tablaPortes = new TablaPortes();

        $rows = $tablaPortes->getTablaPortesAgencia($this->IDAgencia);
        unset($tablaPortes);

        return $rows;
    }
}

?>
