<?php

/**
 * Description of TraspasosLineas
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class TraspasosLineas extends TraspasosLineasEntity {

    public function __toString() {
        return $this->getIDLinea();
    }

    /**
     * Guarda la informacion (update)
     */
    public function save() {
        parent::save();

        //RECALCULAR EL TRASPASO
        $this->getIDTraspaso()->save();
    }

    /**
     * Validaciones antes de actualizar o crear
     */
    public function valida() {
        unset($this->_errores);

        if ($this->IDArticulo == '')
            $this->_errores[] = "Debe indicar un código de artículo";

        // Las unidades deben ser distintas a 0
        if ($this->Unidades == 0)
            $this->_errores[] = "Debe indicar la cantidad";

        // Compruebo la existencia del artículo
        $articulo = new Articulos($this->IDArticulo);
        if ($articulo->getStatus() == 0) {
            $this->_errores[] = "El artículo indicado no existe";
            unset($articulo);
        }

        // Si existe el articulo ...
        if (count($this->_errores) == 0) {

            // Si la descripcion está vacia, pongo la del artículo
            // Si trae algo, la respeto.
            if ($this->Descripcion == '')
                $this->setDescripcion($articulo->getDescripcion());

            if ($this->Precio == '')
                $this->setPrecio($articulo->getPmc());

            $this->TotalizaLinea();

            // Si el artículo es inventariable y la linea es de salida:
            // Comprobar existencias sin tener en cuenta lote y ubicación
            if (($articulo->getInventario()->getIDTipo() == '1') and ($this->Tipo == '0')) {
                $existencias = new Existencias();
                $stock = $existencias->getStock($this->IDArticulo, $this->IDAlmacen, 0, 0, -1, 'UMA');

                if ($stock['RE'] < $this->getUnidades()) {
                    $this->_alertas[] = "Stock insuficiente";
                    $this->_alertas[] = "------------------";
                }

                $this->_alertas[] = "Disponible: " . $stock['DI'];
                $this->_alertas[] = "Real: " . $stock['RE'];
                $this->_alertas[] = "Reservado: " . $stock['RV'];
                $this->_alertas[] = "Pte. Entrada: " . $stock['PE'];
            }
        }

        unset($articulo);
        unset($existencias);

        return ( count($this->_errores) == 0 );
    }

    /**
     * Valida antes del borrado
     * Devuelve TRUE o FALSE
     * Si hay errores carga el array $this->_errores
     * @return boolean
     */
    public function validaBorrado() {
        unset($this->_errores);
        if ($this->IDEstado != 0) {
            $this->_errores[] = "No se puede borrar la línea. Está en elaboración";
        }
        return (count($this->_errores) == 0);
    }

    /**
     * Si está en estado de Pte. de Confirmar (0)
     * Borra una linea de traspaso, después recalculo el traspaso
     */
    public function erase() {

        if ($this->IDEstado == 0) {
            parent::erase();

            // Recalculo la cabecera
            $this->getIDTraspaso()->save();
        }
    }

    /**
     * Expide la linea de traspaso de tipo 0 (la de envío)
     * Actualiza las existencias y marca la línea como expedida.
     * Genera la línea de traspaso de tipo 1 (la de entrada) poniendo el 'entrando' y
     * generar tantas líneas de recepción como líneas de expedición se hayan generado
     * para mantener las mismas unidades y lotes
     *
     * NO SE REALIZA NINGUN TRATAMIENTO CON LAS EXISTENCIAS SI EL ARTICULO NO ES INVENTARIABLE
     *
     * @return boolean
     */
    public function expide() {

        $ok = true;

        $articulo = new Articulos($this->IDArticulo);
        $esInventariable = $articulo->getInventario()->getIDTipo();

        if ($esInventariable) {
            // Quitar la reserva de mercancía
            $exi = new Existencias();
            $ok = $exi->quitaReserva($this->IDAlmacen, $this->IDArticulo, $this->Unidades, $this->UnidadMedida);
            unset($exi);
        }

        if (($esInventariable) and ($ok)) {
            // Expedir la línea de traspaso
            $expedicion = new Expediciones();
            $unidadesNetas = $expedicion->expide("TraspasosCab", $this->IDLinea);
            unset($expedicion);
        } else {
            $unidadesNetas = $this->Unidades;
        }

        if ($ok) {
            // Marcar la línea de traspaso como expedida y
            // poner las unidades netas expedidas
            $this->setIDEstado(2);
            $this->setUnidades($unidadesNetas);
            $this->totalizaLinea();
            $this->save();

            // Generar la línea de traspaso de tipo 1 (la de entrada) y poner el entrando
            $idAlmacenDestino = $this->getIDTraspaso()->getIDAlmacenDestino()->getIDAlmacen();
            $lineaEntrada = new TraspasosLineas();
            $lineaEntrada->setIDLinea('');
            $lineaEntrada->setIDTraspaso($this->IDTraspaso);
            $lineaEntrada->setTipo(1);
            $lineaEntrada->setIDArticulo($this->IDArticulo);
            $lineaEntrada->setDescripcion($this->Descripcion);
            $lineaEntrada->setUnidades($this->Unidades);
            $lineaEntrada->setUnidadMedida($this->UnidadMedida);
            $lineaEntrada->setIDAlmacen($idAlmacenDestino);
            $lineaEntrada->setPrecio($this->Precio);
            $lineaEntrada->setImporte($this->Importe);
            $lineaEntrada->setIDEstado(0);
            $idLineaEntrada = $lineaEntrada->create();
            unset($lineaEntrada);

            // Generar tantas líneas de recepción como líneas de expedición haya para
            // la línea de traspaso expedida
            $expedicion = new Expediciones();
            $rows = $expedicion->cargaCondicion("*","Entidad='TraspasosCab' and IDLineaEntidad='{$this->IDLinea}'");
            unset($expedicion);
            foreach ($rows as $row) {
                $recepcion = new Recepciones();
                $recepcion->setEntidad($row['Entidad']);
                $recepcion->setIDEntidad($row['IDEntidad']);
                $recepcion->setIDLineaEntidad($idLineaEntrada);
                $recepcion->setIDAlmacen($idAlmacenDestino);
                $recepcion->setIDAlmacenero($_SESSION['USER']['user']['id']);
                $recepcion->setIDArticulo($row['IDArticulo']);
                $recepcion->setUnidades($row['Unidades']);
                $recepcion->setUnidadMedida($row['UnidadMedida']);
                $recepcion->setUnidadesBrutas($row['Unidades']);
                $recepcion->setUnidadesNetas($row['Unidades']);
                $recepcion->setIDLote($row['IDLote']);
                $recepcion->create();
                unset($recepcion);
            }

            // Poner el entrando
            $exi = new Existencias();
            $ok = $exi->hazEntrando($idAlmacenDestino,$this->IDArticulo,$this->getUnidades(),$this->getUnidadMedida());
            unset($exi);
        }

        unset($articulo);

        return $ok;
    }

    /**
     * Recepciona la línea de traspaso de tipo 1 (la de entrada)
     * Actualiza las existencias y marca la línea como expedida.
     *
     * NO SE REALIZA NINGUN TRATAMIENTO CON LAS EXISTENCIAS SI EL ARTICULO NO ES INVENTARIABLE
     *
     * @return boolean
     */
    public function recepciona() {

        $ok = true;

        $articulo = new Articulos($this->IDArticulo);
        $esInventariable = $articulo->getInventario()->getIDTipo();

        if ($esInventariable) {
            // Quitar 'entrando'
            $exi = new Existencias();
            $ok = $exi->quitaEntrando($this->IDAlmacen, $this->IDArticulo, $this->Unidades, $this->UnidadMedida);
            unset($exi);
        }

        if (($esInventariable) and ($ok)) {
            // Recepcionar las líneas de recepción de la línea de traspaso
            $recepcion = new Recepciones();
            $unidadesNetas = $recepcion->recepciona("TraspasosCab", $this->IDLinea);
            unset($recepcion);
        } else {
            $unidadesNetas = $this->Unidades;
        }

        if ($ok) {
            // Marcar la línea de trapaso como recepcionada y
            // Poner las unidades netas recibidas
            // Recalcular la línea
            // Actualiza los precios del artículo
            $this->setIDEstado(3);
            $this->setUnidades($unidadesNetas);
            $this->TotalizaLinea();

            if ($unidadesNetas != 0) {
                //$articulo = new Articulos($this->IDArticulo);
                //$articulo->actualizaPrecios($unidadesNetas, abs($this->Importe / $unidadesNetas));
                //unset($articulo);
            }
            $this->save();
        }

        return $ok;
    }

    /**
     * Devuelve un array con los datos de cabecera de la expedición
     * de la linea de albarán en curso
     * 
     * El array es:
     * 
     *      IDAlmacen => ,
     *      IDAlmacenero => ,
     *      IDRepartidor => ,
     *      CreatedBy => ,
     *      CreatedAt => ,
     *      ModifiedBy => ,
     *      ModifiedAt => ,
     *      
     * @return array Array con la cabecera de la expedición
     */
    public function getCabeceraExpedicion() {
        
        $expedicion = new Expediciones();
        $cabecera = $expedicion->getCabecera("TraspasosCab", $this->IDLinea);
        unset($expedicion);
        
        return $cabecera;
    }
    
    /**
     * Devuelve un array de objetos Expediciones de la línea de traspaso
     * @return array Array de objetos Expediciones
     */
    public function getDetalleExpedicion() {

        $expedicion = new Expediciones();
        $lineas = $expedicion->getDetalle("TraspasosCab", $this->IDLinea);
        unset($expedicion);

        return $lineas;
    }

    /**
     * Devuelve un array de objetos Recepciones de la linea de traspaso
     * @return array Array de objetos Recepciones
     */
    public function getDetalleRecepcion() {

        $lineas = array();

        $recepcion = new Recepciones();
        $lineas = $recepcion->getDetalle("TraspasosCab", $this->IDLinea);
        unset($recepcion);

        return $lineas;
    }

    /**
     * Devuelve separados por guión la descripción de los lotes
     * que se han servido en la línea de traspaso
     *
     * @return string Descripcion de los lotes
     */
    public function getLotesEnviados() {

        $expedicion = new Expediciones();
        $lotes = $expedicion->getLotes("TraspasosCab", $this->IDLinea);
        unset($expedicion);

        return $lotes;
    }

    /**
     * Devuelve separados por guión la descripción de los lotes
     * que se han recibido en la línea de traspaso
     *
     * @return string Descripcion de los lotes
     */
    public function getLotesRecibidos() {

        $recepcion = new Recepciones();
        $lotes = $recepcion->getLotes("TraspasosCab", $this->IDLinea);
        unset($recepcion);

        return $lotes;
    }

    public function getUnidadMedidaArticulo() {
        return $this->IDArticulo->getUnidadMedida($this->UnidadMedida);
    }

    private function TotalizaLinea() {
        $this->setImporte($this->Unidades * $this->Precio);
    }

}

?>
