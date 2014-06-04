<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 23.03.2012 19:13:09
 */

/**
 * @orm:Entity(manufac_lineas)
 */
class ManufacLineas extends ManufacLineasEntity {

    public function __toString() {
        return $this->getIDLinea();
    }

    /**
     * Guarda una línea y totaliza el padre
     */
    public function save() {

        parent::save();

        //RECALCULAR EL Padre
        $this->getIDManufac()->save();
    }

    /**
     * Crea una línea y totaliza el padre
     */
    public function create() {

        $lastId = parent::create();
        if ($lastId != NULL) {
            //RECALCULAR EL Padre
            $this->getIDManufac()->save();
        }
        return $lastId;
    }

    /**
     * Si está en estado de Pte. de Confirmar (0)
     * Borra una linea de elaboración, después recalculo el parte de elaboración
     */
    public function erase() {

        if ($this->IDEstado == 0) {
            parent::erase();

            // Recalculo el albaran
            $this->getIDManufac()->save();
        }
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
                $stock = $existencias->getStock($this->IDArticulo, $this->IDAlmacen, 0, 0, -1, $this->UnidadMedida);

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
     * Expide la linea de elaboracion de tipo 0 (la de envío)
     * Actualiza las existencias y marca la línea como expedida.
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
            // Expedir la línea de elaboración
            $expedicion = new Expediciones();
            $unidadesNetas = $expedicion->expide("ManufacCab", $this->IDLinea);
            unset($expedicion);
        } else {
            $unidadesNetas = $this->Unidades;
        }

        if ($ok) {
            // Marcar la línea de elaboracion como expedida y
            // poner las unidades netas expedidas
            $this->setIDEstado(2);
            $this->setUnidades($unidadesNetas);
            $this->totalizaLinea();
            $this->save();
        }

        unset($articulo);

        return $ok;
    }

    /**
     * Recepciona la línea de elaboración de tipo 1 (la de entrada)
     * Actualiza las existencias y marca la línea como recepcionada.
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
            // Recepcionar la línea de elaboración
            $recepcion = new Recepciones();
            $unidadesNetas = $recepcion->recepciona("ManufacCab", $this->IDLinea);
            unset($recepcion);
        } else {
            $unidadesNetas = $this->Unidades;
        }

        if ($ok) {
            // Marcar la línea de elaboración como recepcionada y
            // Poner las unidades netas recibidas
            // Recalcular la línea
            $this->setIDEstado(3);
            $this->setUnidades($unidadesNetas);
            $this->TotalizaLinea();

            // Actualiza los precios del artículo
            /**
             * ACTUALMENTE DESACTIVADO
            if ($unidadesNetas != 0) {
                $articulo = new Articulos($this->IDArticulo);
                $articulo->actualizaPrecios($unidadesNetas, abs($this->Importe / $unidadesNetas));
                unset($articulo);
            }
            */
            $this->save();
        }

        return $ok;
    }

    /**
     * Devuelve un array con los datos de cabecera de la expedición
     * de la linea de manufactura en curso
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
        $cabecera = $expedicion->getCabecera("ManufacCab", $this->IDLinea);
        unset($expedicion);
        
        return $cabecera;
    }
    
    /**
     * Devuelve un array de objetos Expediciones de la línea de elaboración
     * @return array Array de objetos Expediciones
     */
    public function getDetalleExpedicion() {

        $expedicion = new Expediciones();
        $lineas = $expedicion->getDetalle("ManufacCab", $this->IDLinea);
        unset($expedicion);

        return $lineas;
    }

    /**
     * Devuelve un array de objetos Recepciones de la linea de elaboracion
     * @return array Array de objetos Recepciones
     */
    public function getDetalleRecepcion() {

        $recepcion = new Recepciones();
        $lineas = $recepcion->getDetalle("ManufacCab", $this->IDLinea);
        unset($recepcion);

        return $lineas;
    }

    /**
     * Devuelve separados por guión la descripción de los lotes
     * que se han expedido en la línea de elaboración
     *
     * @return string Descripcion de los lotes
     */
    public function getLotesExpedidos() {

        $expedicion = new Expediciones();
        $lotes = $expedicion->getLotes("ManufacCab", $this->IDLinea);
        unset($expedicion);

        return $lotes;
    }

    /**
     * Devuelve separados por guión la descripción de los lotes
     * que se han recibido en la línea de elaboración
     *
     * @return string Descripcion de los lotes
     */
    public function getLotesRecibidos() {

        $recepcion = new Recepciones();
        $lotes = $recepcion->getLotes("ManufacCab", $this->IDLinea);
        unset($recepcion);

        return $lotes;
    }

    public function getUnidadMedidaArticulo() {
        return $this->IDArticulo->getUnidadMedida($this->UnidadMedida);
    }

    private function TotalizaLinea() {

        switch ($this->BaseAplicacion) {
            case '0': // Calculo sobre las unidades enviadas
                $manufac = new ManufacCab($this->IDManufac);
                $unidades = $manufac->getKilosOrigen();
                unset($manufac);
                $this->setImporte($unidades * $this->Precio);
                break;

            case '1': // Calculo sobre las unidades obtenidas
                $this->setImporte($this->Unidades * $this->Precio);
                break;
        }
    }

}

?>