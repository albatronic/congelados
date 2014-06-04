<?php

/**
 * Description of Subfamilias
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Subfamilias extends SubfamiliasEntity {

    public function __toString() {
        return $this->getSubfamilia();
    }

    /**
     * Devuelve un array con todas las subfamilias de la familia indicada
     *
     * @param integer $idFamilia El id de la familia
     * @param string $column La columna a mostrar
     * @return array Array con las subfamilias
     */
    public function fetchAll($idFamilia, $column='Subfamilia') {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $filtro = "WHERE (IDFamilia='" . $idFamilia . "') ";

            $query = "SELECT IDSubfamilia as Id,$column as Value FROM subfamilias $filtro ORDER BY $column ASC;";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        $rows[] = array('Id' => '', Value => ':: Subfamilia');
        return $rows;
    }

}

?>
