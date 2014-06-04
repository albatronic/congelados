<?php

/**
 * Description of MvtosAlmacen
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class MvtosAlmacen extends MvtosAlmacenEntity {

    public function __toString() {
        return $this->getId();
    }

    /**
     * Devuelve el numero de documento que ha generado el movimiento de almacen
     * @return string El numero de documento
     */
    public function getNumeroDocumento() {

        $numeroDocumento = "";

        $documento = $this->getDocumento();
        if ($documento)
            $numeroDocumento = $documento->getNumeroDocumento();

        unset($documento);

        return $numeroDocumento;
    }

    /**
     * Devuelve el objeto que ha provocado el movimiento de almacen
     *
     * @return string El numero de documento
     */
    public function getDocumento() {

        $documento = NULL;

        if (($this->IDTipo->getId() != '') and ($this->IDDocumento)) {
            // Averiguar la entidad que lo ha generado
            $tipoMovimiento = new TiposMvtosAlmacen($this->IDTipo->getId());
            $entidad = $tipoMovimiento->getTipoDocumento();
            unset($tipoMovimiento);
            if ($entidad)
                $documento = new $entidad($this->IDDocumento);
        }

        return $documento;
    }

    protected function load() {
        if ($this->Id == '') {
            $this->setIDAgente($_SESSION['USER']['user']['id']);
            $this->setFecha('');
            $this->setHora(date('H:i:s'));
        } else
            parent::load();
    }

    /**
     * Genera un movimiento de almacén y actualiza el stock
     *
     * No se creará movimiento de almacén y por lo tanto tampoco se actualizarán existencias:
     *
     *  * si el artículo no está sujeto a control de inventario
     *
     *  * si el almacén está sujeto a control de ubicaciones y no se ha indicado ninguna
     *
     *  * si el artículo está sujeto a control de trazabilidad y no se ha indicado ningún lote
     *
     *  * si no hay existencias suficientes y el artículo bloquea stock
     *
     * @param string $documento El literal que identifica el controlador que provoca el movimiento
     * @param string $signo El signo que tendrá el movimiento (E = Entrada, S = Salida)
     * @param integer $idDocumento El id del documento que provoca el movimiento (albaran, pedido, traspaso, inventario, etc)
     * @param integer $idAlmacen El id del almacén
     * @param integer $idArticulo El id del articulo
     * @param integer $idLote El id del lote
     * @param integer $idUbicacion El id de la ubicacion
     * @param integer $flagDeposito Indica si el movimineto se ha de realizar contra stock de deposito o no (0, 1)
     * @param array $valores Array con los valores a actualizar
     * @return boolean Si el artículo no está sujeto a inventario devuelve TRUE, en caso contrario devuelve TRUE o FALSE dependiendo del éxito de la operación
     */
    public function genera($documento, $signo, $idDocumento, $idAlmacen, $idArticulo, $idLote, $idUbicacion, $flagDeposito, array $valores) {

        $ok = false;

        $articulo = new Articulos($idArticulo);

        // Si el artículo está sujeto a inventario
        if ($articulo->getInventario()->getIDTipo()) {

            $tipoMvto = new TiposMvtosAlmacen();
            $row = $tipoMvto->cargaCondicion("Id,Signo", "TipoDocumento='{$documento}' and Signo='{$signo}'");
            unset($tipoMvto);
            $idTipo = $row[0]['Id'];
            $signo = $row[0]['Signo'];

            if ($signo) {

                if ($valores['UM'] == '')
                    $valores['UM'] = 'UMA';

                $this->setIDTipo($idTipo);
                $this->setIDAlmacen($idAlmacen);
                $this->setIDArticulo($idArticulo);
                $this->setIDLote($idLote);
                $this->setIDUbicacion($idUbicacion);
                $this->setIDDocumento($idDocumento);
                switch ($signo) {
                    case 'E':
                        $this->setUnidadesE($articulo->convertUnit($valores['UM'], 'UMA', $valores['Reales']));
                        $this->setPalesE($valores['Pales']);
                        $this->setCajasE($valores['Cajas']);
                        break;
                    case 'S':
                        $this->setUnidadesS($articulo->convertUnit($valores['UM'], 'UMA', $valores['Reales']));
                        $this->setPalesS($valores['Pales']);
                        $this->setCajasS($valores['Cajas']);
                        break;
                }

                if ($this->validaMovimiento($signo)) {
                    if (parent::create()) {
                        if ($signo == 'S') {
                            $valores['Reales'] = -1 * $valores['Reales'];
                            $valores['Pales'] = -1 * $valores['Pales'];
                            $valores['Cajas'] = -1 * $valores['Cajas'];
                        }
                        $ok = true;
                        $exi = new Existencias();
                        $exi->actualiza($idAlmacen, $idArticulo, $idLote, $idUbicacion, $flagDeposito, $valores);
                        $this->_errores = $exi->getErrores();
                        unset($exi);
                    } else
                        print_r($this->_errores);
                } else
                    print_r($this->_errores);
            }
        } else
            $ok = true;

        unset($articulo);

        return $ok;
    }

    /**
     * Valida la idoneidad del movimiento.
     *
     * Chequea si se aplica o no la trazabilidad, la ubicacion y el bloqueo de stock
     *
     * @param char $signo El signo del movimiento (E=Entrada, S=Salida)
     * @return boolean
     */
    private function validaMovimiento($signo) {

        $articulo = new Articulos($this->IDArticulo);
        $almacen = new Almacenes($this->IDAlmacen);

        $hayTrazabilidad = $articulo->getTrazabilidad()->getIDTipo();
        $hayBloqueoStock = $articulo->getBloqueoStock()->getIDTipo();
        $hayControlUbicaciones = $almacen->getControlUbicaciones()->getIDTipo();

        if ($hayBloqueoStock) {
            
            $exi = new Existencias();
            $stock = $exi->getStock($this->IDArticulo, $this->IDAlmacen, $this->IDLote, $this->IDUbicacion, '', 'UMA');
            unset($exi);  
            
            switch ($signo) {
                case 'S':
                    $okBloqueoStock = ($stock['RE'] >= ($this->UnidadesE + $this->UnidadesS) );
                    break;
                case 'E':
                    $unidades = $this->UnidadesE + $this->UnidadesS;
                    $okBloqueoStock = ( ($unidades >= 0) or ($stock['RE'] >= abs($unidades)) );
                    break;
            }
        } else
            $okBloqueoStock = true;

        $okTrazabilidad = ( (!$hayTrazabilidad) or ($this->IDLote != 0) );
        $okUbicacion = ( (!$hayControlUbicaciones) or ($this->IDUbicacion != 0) );

        if (!$okBloqueoStock)
            $this->_errores[] = "No hay stock suficiente para " . $articulo->getDescripcion();
        if (!$okTrazabilidad)
            $this->_errores[] = "Debe indicar el lote para " . $articulo->getDescripcion();
        if (!$okUbicacion)
            $this->_errores[] = "Debe indicar la ubicación para " . $articulo->getDescripcion();

        unset($articulo);
        unset($almacen);

        return (count($this->_errores) == 0);
    }

}

?>
