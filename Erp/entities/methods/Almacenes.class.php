<?php

/**
 * Description of Almacenes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Almacenes extends AlmacenesEntity {

    public function __toString() {
        return ($this->Nombre != '') ? $this->getNombre() : "";
    }

    /**
     * Devuelve un array con todos los almacenes.
     * Si se indica el parametro $idEmpresa, solo muestra los
     * almacenes de esa empresa
     * Cada elemento tiene la primarykey y el valor de $column
     *
     * @param integer $idEmpresa EL id de la empresa (opcional)
     * @param string $column La columna a mostrar
     * @return array Array con los almacenes
     */
    public function fetchAll($idEmpresa = '', $column = 'Nombre') {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            if ($idEmpresa == '') {
                $query = "SELECT IDAlmacen as Id, $column as Value from almacenes order by $column ASC;";
            } else {
                $filtro = "WHERE (t1.IDEmpresa='" . $idEmpresa . "')  AND (t1.IDAlmacen=t2.IDAlmacen)";
                $query = "SELECT t1.IDAlmacen as Id, t2.$column as Value FROM empresas_almacenes as t1, almacenes as t2 $filtro ORDER BY t2.$column ASC;";
            }

            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        $rows[] = array('Id' => '', Value => ':: Indique un valor');
        return $rows;
    }

    /**
     * Devuelve un array con los almacenes de la empresa en curso
     * a los que tiene acceso el usuario $idUsuario.
     * 
     * Si no se indica $idUsuario se toma el usuario en curso.
     * 
     * @param integer $idUsuario
     * @return array Array de almacenes
     */
    public function getAlmacenesUsuario($idUsuario = '') {

        if ($idUsuario == '')
            $idUsuario = $_SESSION['USER']['user']['iu'];

        $usuario = new Agentes($idUsuario);
        $rows = $usuario->getAlmacenes();
        unset($usuario);

        return $rows;
    }

    /**
     * Devuelve un array con todas las ubicaciones del almacén
     * El array es del tipo array('Id' => , 'Value' => )
     *
     * @param string Filtro para la ubicacion (defecto %)
     * @return array Las ubicaciones del almacen
     */
    public function getUbicaciones($filtroUbicacion = "%") {
        $ubicaciones = array();

        $mapa = new AlmacenesMapas();
        $ubicaciones = $mapa->fetchAll($this->IDAlmacen, $filtroUbicacion);
        unset($mapa);

        return $ubicaciones;
    }

    /**
     * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     * OJO!!! ESTE METODO NO SE USA EN PRINCIPIO. HAY QUE REVISAR EL QUERY
     * YA QUE SE USAN TABLAS DE DIFERENTES BASES DE DATOS Y PARACE QUE NO ESTA BIEN
     * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     *
     * Devuelve un array con las ubicaciones disponibles en el almacén
     * Se entiende por ubicacion libre aquella que no tiene existencias
     * El array es del tipo array('Id' => , 'Value' => )
     * @return array Con las ubicaciones libres
     */
    public function getUbicacionesLibres() {

        $huecos = array();

        // LLamo al procedimiento almacenado 'UbicacionesLibres'
        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            //$query = "Call UbicacionesLibres('{$this->IDAlmacen}');";
            $query = "SELECT IDUbicacion as Id, Ubicacion as Value
                        FROM {$this->_dataBaseName}.almacenes_mapas
                        WHERE
                            IDAlmacen = '{$this->IDAlmacen}' AND
                            IDUbicacion NOT IN (
                                SELECT t1.IDUbicacion
                                FROM existencias as t1
                                GROUP BY t1.IDAlmacen, t1.IDUbicacion
                                HAVING t1.IDAlmacen =  '{$this->IDAlmacen}'
                                AND SUM( t1.Reales ) > 0
                            )
                        ORDER BY Ubicacion";
            $em->query($query);
            $huecos = $em->fetchResult();
            $em->desConecta();
        }
        unset($em);

        return $huecos;
    }

}

?>
