<?php

/**
 * Description of PstoLineas
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 23-nov-2011
 *
 */
class PstoLineas extends PstoLineasEntity {

    public function __toString() {
        return $this->getIDLinea();
    }

    /**
     * Guarda la informacion (update)
     */
    public function save() {

        parent::save();

        //RECALCULAR EL PRESUPUESTO
        $this->getIDPsto()->save();
    }

    /**
     * Crea un registro (insert)
     */
    public function create() {

        $lastId = parent::create();
        if ($lastId != NULL) {
            //RECALCULAR EL PRESUPUESTO
            $this->getIDPsto()->save();
        }
        return $lastId;
    }

    /**
     * Borra una linea de presupuesto, después recalculo el presupuesto
     */
    public function erase() {

        if ($this->IDEstado == 0) {
            parent::erase();

            // Recalculo el presupuesto
            $this->getIDPsto()->save();
        }
    }

    /**
     * Validaciones antes de actualizar o crear
     */
    public function valida() {
        unset($this->_errores);

        if ($this->IDArticulo == '') {
            $this->_errores[] = "Debe indicar un código de artículo";
            return ( count($this->_errores) == 0 );
        }

        //Para tener disponibles los datos de la
        //cabecera del albaran
        $psto = new PstoCab($this->IDPsto);

        // Compruebo la existencia del artículo y que pertenezca a
        // la sucursal de la cabecera del albaran.
        $articulo = new Articulos($this->IDArticulo);
        if ($articulo->getStatus() == 0) {
            $this->_errores[] = "El artículo indicado no existe o no pertenece a esta sucursal";
            unset($articulo);
        }


        // Si existe el articulo ...
        if (count($this->_errores) == 0) {

            // Si es version CRISTAL, comprueba multiplos y calcula medidas
            if ($_SESSION['ver'] == 1)
                $this->checkMultiplos($articulo);

            $this->checkPackingVentas($articulo);

            // Si la descripcion está vacia, pongo la del artículo
            // Si trae algo, la respeto.
            if ($this->Descripcion == '')
                $this->setDescripcion($articulo->getDescripcion());

            // Si hay promo, ver si se aplica en base a la cantidad mínima.
            // Si es aplicable, prevalece sobre el precio y dcto indicado y tambien
            // sobre la tarifa del cliente.
            $precios = $articulo->cotizar($psto);
            if ($precios['Promocion']) {
                $alerta = "Promocion hasta el " . $precios['Promocion']->getFinPromocion() . " y compra mínima " . $precios['Promocion']->getCantidadMinima();
                if ($precios['Promocion']->getIDFP()->getIDFP())
                    $alerta .= " y forma de pago " . $precios['Promocion']->getIDFP();
                $this->_alertas[] = $alerta;
            }

            // Si hay promo, se aplica si se iguala o supera la cantidad mínima y si
            // no se restringe la forma de pago y en caso contrario la forma de pago
            // fijada para la promocion es igual a la del presupuesto
            if (($precios['Promocion']) and
                    ($this->getUnidades() >= $precios['Promocion']->getCantidadMinima()) and
                    ((!$precios['Promocion']->getIDFP()->getIDFP()) or ($psto->getIDFP()->getIDFP() == $precios['Promocion']->getIDFP()->getIDFP()))) {
                $this->IDPromocion = $precios['Promocion']->getIDPromocion();
                $this->Precio = $precios['Promo']['Precio'];
                $this->Descuento = $precios['Promo']['Descuento'];
            } else {
                // Si no hay promo, se aplica la tarifa, o se respeta el precio y descuento
                // indicado por el usuario si tiene permiso dependiendo de si su rol está
                // incluido en el parámetro ROLCP
                $this->IDPromocion = 0;
                if (($this->Precio == '') or (!$_SESSION['USER']['user']['cambioPrecios']))
                    $this->setPrecio($precios['Tarifa']['Precio']);
                if (($this->Descuento == '') or (!$_SESSION['USER']['user']['cambioPrecios']))
                    $this->setDescuento($precios['Tarifa']['Descuento']);
            }

            // Totalizar la linea
            $this->totalizaLinea($articulo);

            //Comprobar que no se venda por debajo del costo
            if ($this->Importe < $this->ImporteCosto)
                $this->_alertas[] = "Venta negativa";

            // Poner el mismo almacen y comercial de la cabecera del albarán
            $this->setIDAlmacen($psto->getIDAlmacen()->getIDAlmacen());
            $this->setIDComercial($psto->getIDComercial()->getIDAgente());
            $this->setIDAgente($_SESSION['USER']['user']['id']);

            // Si el cliente no está sujeto a Iva, pongo a 0 el iva y el recargo
            if ($psto->getIDCliente()->getIva()->getIDTipo() == '0') {
                $this->setIva('0');
                $this->setRecargo('0');
            } else {
                // Si no se ha indicando iva, pongo el iva y recargo asociado al artículo,
                if ($this->Iva == '') {
                    $this->setIva($articulo->getIDIva()->getIva());
                    if ($psto->getIDCliente()->getRecargoEqu()->getIDTipo() == '1')
                        $this->setRecargo($articulo->getIDIva()->getRecargo());
                    else
                        $this->setRecargo('0');
                }
            }

            // Si el artículo es inventariable:
            // Comprobar existencias sin tener en cuenta lote ni ubicación
            // de almacen. Solo se buscan existencias en el almacen indicado
            // en la línea del albarán
            if ($articulo->getInventario()->getIDTipo() == '1') {
                $existencias = new Existencias();
                $stock = $existencias->getStock($this->IDArticulo, $this->IDAlmacen);

                if ($stock['DI'] < $this->getUnidades()) {
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
        unset($psto);
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
            $this->_errores[] = "No se puede borrar la línea. Está confirmada";
        }
        return (count($this->_errores) == 0);
    }

    /**
     * Totaliza la linea de albarán
     * Para la version estandar se toman las unidades indicadas en la linea de albaran
     * Para la version cristal se toman los metros cuadrados de factura
     *
     * @param Articulos $articulo El objeto articulo
     */
    private function totalizaLinea(Articulos $articulo) {

        switch ($_SESSION['ver']) {
            case '0': // Version estandar
                $unidades = $this->Unidades;
                break;
            case '1': // Version cristal
                $unidades = $this->MtsFa;
                break;
        }

        $this->setImporte($unidades * $this->Precio * (1 - $this->Descuento / 100));
        $this->setImporteCosto($unidades * $articulo->getPrecioCosto('UMV'));
    }

    /**
     * Comprueba la unidad minima de venta
     * Para la version estandar se toman las unidades indicadas en la linea de albaran
     * Para la version cristal se toman los metros cuadrados de factura
     *
     * @param Articulos $articulo El objeto articulo
     */
    private function checkPackingVentas(Articulos $articulo) {

        switch ($_SESSION['ver']) {
            case '0': // Version estandar
                $unidades = $this->Unidades;
                break;
            case '1': // Version cristal
                $unidades = $this->MtsFa;
                break;
        }

        $packing = $articulo->getPackingVentas();
        if ($unidades < $packing) {
            $this->setUnidades($packing);
            $this->_alertas[] = "Unidad Mínima de Venta " . $packing;
        } elseif ($unidades > $packing) {
            // Compruebo multiplo, redondeo al múltiplo inmediatamente superior
            $v = explode(".", $unidades / $packing);
            $resultado = $v[0];
            if ($v[1])
                $resultado++;
            $this->setUnidades($resultado * $packing);
            $this->_alertas[] = "Unidad Mínima de Venta " . $packing;
        }
    }

    private function checkMultiplos(Articulos $articulo) {

        //Calcular los metros de almacén
        if (($this->AltoAl + $this->AnchoAl) == 0)
            $this->setMtsAl($this->Unidades);
        else
            $this->setMtsAl($this->Unidades * $this->AltoAl * $this->AnchoAl);

        //Comprobar las unidades mínimas de venta para la factura
        ($articulo->getMinimoVentaAlto() > $this->AltoAl) ? $this->setAltoFa($articulo->getMinimoVentaAlto()) : $this->setAltoFa($this->AltoAl);
        ($articulo->getMinimoVentaAncho() > $this->AnchoAl) ? $this->setAnchoFa($articulo->getMinimoVentaAncho()) : $this->setAnchoFa($this->AnchoAl);


        //Comprobar los múltiplos
        if ($articulo->getMultiploAlto() > 0) {
            $v = explode(".", $this->AltoFa / $articulo->getMultiploAlto());
            $resultado = $v[0];
            if ($v[1])
                $resultado++;
            $this->setAltoFa($resultado * $articulo->getMultiploAlto());
        }

        if ($articulo->getMultiploAncho() > 0) {
            $v = explode(".", $this->AnchoFa / $articulo->getMultiploAncho());
            $resultado = $v[0];
            if ($v[1])
                $resultado++;
            $this->setAnchoFa($resultado * $articulo->getMultiploAncho());
        }

        //Calcular los metros en factura teniendo en cuenta los metros mínimos de venta
        $this->setMtsFa($this->AltoFa * $this->AnchoFa);
        if ($this->MtsFa == 0)
            $this->MtsFa = 1;
        if ($this->MtsFa < $articulo->getMinimoVenta())
            $this->setMtsfa($articulo->getMinimoVenta());
        $this->setMtsFa($this->Unidades * $this->MtsFa);
    }

}

?>
