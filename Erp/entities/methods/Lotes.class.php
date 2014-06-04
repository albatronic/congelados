<?php

/**
 * Description of Lotes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Lotes extends LotesEntity {

    public function __toString() {
        return $this->getLote();
    }

    /**
     * Devuelve un array con todos los lotes VIGENTES del articulo indicado (opcional)
     * ordenados por fecha de caducidad ascendente (el más cercano a caducar, primero)
     * Cada elemento tiene la primarykey y el valor de $column
     *
     * @param integer $idArticulo El id de articulo (opcional)
     * @param string $column La columna a devolver. Por defecto "Lote"
     * @return array
     */
    public function fetchAll($idArticulo = '', $column = 'Lote', $filtroDescripcion = '%') {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            if ($idArticulo != '')
                $filtro = "(IDArticulo='" . $idArticulo . "')";
            else
                $filtro = "(1)";
            $filtro .= " AND Lote LIKE '{$filtroDescripcion}' AND Vigente='1'";
            $query = "SELECT IDLote as Id, $column as Value FROM lotes WHERE $filtro ORDER BY FechaCaducidad ASC;";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        return $rows;
    }
    
    public function getLotes($idArticulo = '', $column = 'Lote', $filtroDescripcion = '%') {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            if ($idArticulo != '')
                $filtro = "(IDArticulo='" . $idArticulo . "')";
            else
                $filtro = "(1)";
            $filtro .= " AND Lote LIKE '{$filtroDescripcion}'";
            $query = "SELECT IDLote as Id, $column as Value FROM lotes WHERE $filtro ORDER BY Lote ASC;";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        return $rows;
    }
    
    public function getLotesDescripcion($idArticulo = '', $column = 'Lote', $filtroDescripcion = '%') {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            if ($idArticulo != '')
                $filtro = "(IDArticulo='" . $idArticulo . "')";
            else
                $filtro = "(1)";
            $filtro .= " AND Lote LIKE '{$filtroDescripcion}'";
            $query = 'SELECT l.IDLote as Id, concat(a.Descripcion," - ",l.Lote) as Value FROM lotes l, articulos a WHERE l.IDArticulo=a.IDArticulo ORDER BY a.Descripcion,l.FechaFabricacion ASC;';
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        return $rows;
    }
    
    public function valida(array $rules) {
        $validacion = parent::valida($rules);
        if ($validacion) {
            if ($this->FechaFabricacion == "0000-00-00")
                $this->FechaFabricacion = date('Y-m-d');
            if ($this->FechaCaducidad <= $this->FechaFabricacion) {
                //Calcular la fecha de caducidad en base a la de fabricacion
                //y el número de días de caducidad del artículo
                $articulo = new Articulos($this->IDArticulo);
                $date = new Fecha($this->FechaFabricacion);
                $this->FechaCaducidad = $date->sumaDias($articulo->getCaducidad());
                unset($articulo);
                unset($date);
            }
        }
        return $validacion;
    }

    /**
     * Devuelve un array con todas las ubicaciones del $idAlmacen
     * donde hay existencias del lote
     *
     * @param integer $idAlmacen El id de almacen
     * @param string $filtroUbicacion Filtro para ubicaciones
     * @return array Las ubicaciones
     */
    public function getUbicaciones($idAlmacen, $filtroUbicacion = "%") {
        $ubicaciones = array();

        $mapas = new AlmacenesMapas();
        $mapasDataBase = $mapas->getDataBaseName();
        unset($mapas);

        $this->conecta();

        if (is_resource($this->_dbLink)) {
            //$query = "Call UbicacionesLote('{$idAlmacen}','{$this->IDLote}','{$filtroUbicacion}');";
            $query = "SELECT DISTINCT e.IDUbicacion AS Id, m.Ubicacion AS Value
                        FROM
                            {$this->_dataBaseName}.existencias e,
                            {$mapasDataBase}.almacenes_mapas m
                        WHERE
                            e.IDUbicacion = m.IDUbicacion AND
                            e.IDAlmacen= '{$idAlmacen}' AND
                            e.IDLote= '{$this->IDLote}' AND
                            e.Reales > 0 AND
                            m.Ubicacion LIKE '{$filtroUbicacion}'
                        ORDER BY m.Ubicacion";
            $this->_em->query($query);
            $ubicaciones = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }

        return $ubicaciones;
    }

    /**
     * Devuelve un array con los ubicaciones del almacen $idAlmacen
     * donde hay existencias reales del lote en curso
     * 
     * El array tiene tres elementos:
     * 
     *  Id : El id de la ubicacion
     * 
     *  Value: El nombre de la ubicacion
     * 
     *  Reales: Las existencias reales del lote
     * 
     * @param integer $idAlmacen El id de almacen
     * @return array Array de ubicaciones
     */
    public function getUbicacionesStock($idAlmacen) {

        $ubicaciones = array();

        $mapas = new AlmacenesMapas();
        $mapasDataBase = $mapas->getDataBaseName();
        unset($mapas);

        $this->conecta();

        if (is_resource($this->_dbLink)) {
            //$query = "Call UbicacionesLote('{$idAlmacen}','{$this->IDLote}','{$filtroUbicacion}');";
            $query = "SELECT e.IDUbicacion AS Id, m.Ubicacion AS Value, e.Reales AS Reales 
                        FROM
                            {$this->_dataBaseName}.existencias e,
                            {$mapasDataBase}.almacenes_mapas as m
                        WHERE
                            e.IDAlmacen= '{$idAlmacen}' AND
                            e.IDLote= '{$this->IDLote}' AND
                            e.Reales > 0 AND
                            e.IDUbicacion = m.IDUbicacion
                        ORDER BY m.Ubicacion";
            $this->_em->query($query);
            $ubicaciones = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }

        return $ubicaciones;
    }

    /**
     * Devuelve true o false dependiende si tiene o no movimientos de almacén
     * 
     * @return boolean TRUE si el lote tiene mvtos de almacen
     */
    public function TieneMvtos() {
        $mvtos = new MvtosAlmacen();
        $rows = $mvtos->cargaCondicion("Count(Id) as nLotes", "IDLote='{$this->getPrimaryKeyValue()}'");
        unset($mvtos);

        return ($rows[0]['nLotes'] > 0);
    }

}

?>
