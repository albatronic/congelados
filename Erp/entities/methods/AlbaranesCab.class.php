<?php

/**
 * Description of AlbaranesCab
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class AlbaranesCab extends AlbaranesCabEntity {

    public function __toString() {
        return $this->getIDAlbaran();
    }

    public function getNumeroDocumento() {
        return $this->NumeroAlbaran;
    }

    protected function load() {
        if ($this->IDAlbaran == '') {
            //Si el nº de albaran está vacio (se ha instanciado un objeto vacio),
            //asigno valores por defecto (agente,comercial,sucursal,almacen y cliente).
            $this->setIDAgente($_SESSION['USER']['user']['id']);

            $agente = new Agentes($_SESSION['USER']['user']['id']);
            $esComercial = ($agente->getEsComercial());
            if ($esComercial) {
                $idComercial = $_SESSION['USER']['user']['id'];
                $this->setIDComercial($idComercial);
            }

            $rows = $agente->getSucursales($_SESSION['emp']);
            $idSucursal = $rows[0]['Id'];
            $this->setIDSucursal($idSucursal);

            $rows = $agente->getAlmacenes($_SESSION['emp']);
            $idAlmacen = $rows[0]['Id'];
            $this->setIDAlmacen($idAlmacen);

            unset($agente);
        }

        parent::load();
    }

    public function create() {

        // Calcular el Numero de albaran en base al contador
        $contador = new Contadores($this->IDContador);
        $this->setNumeroAlbaran($contador->asignaContador());
        $this->setClave(md5($this->NumeroAlbaran));
        unset($contador);

        // Poner la ruta de reparto, día de reparto y el repartidor
        $rutas = new RutasRepartoDetalle();
        $reparto = $rutas->getRutaDiaRepartidor($this->IDDirec, $this->Fecha);
        unset($rutas);
        $this->setIDRutaReparto($reparto['IDRuta']);
        $this->setDiaReparto($reparto['Dia']);
        $this->setIDRepartidor($reparto['IDRepartidor']);

        return parent::create();
    }

    /**
     * Guarda la informacion (update)
     */
    public function save() {

        // Solo se puede guardar se está pendiente de confirmar
        $albaranDB = new AlbaranesCab($this->IDAlbaran);

        // Si no esta facturado, se recalcula antes de guardar
        // Se puede recalcular estando en fase de expedición.
        if ($albaranDB->getIDEstado()->getIDTipo() < 3) {
            if ($albaranDB->getIDEstado()->getIDTipo() == '2')
                $this->setIDEstado(2);
            $this->recalcula();
            $ok = parent::save();
        }

        unset($albaranDB);
        return $ok;
    }

    /**
     * Borra un albaran y sus líneas
     * Siempre que esté en estado 0 (elaboracion) y no esté facturado
     *
     * @return boolean
     */
    public function erase() {

        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = "DELETE FROM {$this->_dataBaseName}.albaranes_cab WHERE `IDAlbaran`='{$this->IDAlbaran}' AND IDEstado='0' and IDFactura='0'";
            if ($this->_em->query($query)) {
                //Borrar líneas de albaranes
                $query = "DELETE FROM {$this->_dataBaseName}.albaranes_lineas where `IDAlbaran`='{$this->IDAlbaran}'";
                if (!$this->_em->query($query))
                    $this->_errores = $this->_em->getError();
            }
            else
                $this->_errores = $this->_em->getError();
            $this->_em->desConecta();
        }
        unset($this->_em);

        return (count($this->_errores) == 0);
    }

    /**
     * Recalcula los importes del albaran en base a sus lineas
     * Actualiza las propiedades de totales pero no salva los datos.
     * IMPORTANTE: Para que los calculos tomen efecto hay que llamar al método save()
     */
    public function recalcula() {

        //Fuerzo el almacen y el comercial de las líneas de albaranes al de la cabecera del albaran
        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "UPDATE {$this->_dataBaseName}.albaranes_lineas SET `IDComercial`='{$this->IDComercial}', `IDAlmacen`='{$this->IDAlmacen}' WHERE `IDAlbaran` = '{$this->IDAlbaran}'";
            $this->_em->query($query);
        }
        $this->_em->desConecta();

        //Si la version es CRISTAL calculo el eventual recargo energetico
        if ($_SESSION['ver'] == 1)
            $this->calculaRecargoEnergetico();

        //Si el cliente no está sujeto a iva
        //pongo el iva a cero en las líneas para evitar que por cambio
        //de cliente se aplique indebidamente
        $cliente = new Clientes($this->IDCliente);
        if ($cliente->getIva()->getIDTipo() == '0') {
            $this->conecta();
            if (is_resource($this->_dbLink)) {
                $query = "UPDATE {$this->_dataBaseName}.albaranes_lineas SET `Iva`='0', `Recargo`='0' WHERE `IDAlbaran`= '{$this->getIDAlbaran()}'";
                $this->_em->query($query);
            }
            $this->_em->desConecta();
        }
        //Si el cliente no está sujeto a recargo de equivalencia
        //lo pongo a cero en las líneas para evitar que por cambio
        //de cliente se aplique indebidamente
        elseif ($cliente->getRecargoEqu()->getIDTipo() == '0') {
            $this->conecta();
            if (is_resource($this->_dbLink)) {
                $query = "update albaranes_lineas set `Recargo`='0' where `IDAlbaran` = '{$this->getIDAlbaran()}'";
                $this->_em->query($query);
            }
            $this->_em->desConecta();
        }
        unset($cliente);

        //SI TIENE DESCUENTO, CALCULO EL PORCENTAJE QUE SUPONE RESPECTO AL IMPORTE BRUTO
        //PARA REPERCUTUIRLO PORCENTUALMENTE A CADA BASE
        $pordcto = 0;
        if ($this->getDescuento() != 0)
            $pordcto = round(100 * ($this->getDescuento() / $this->getImporte()), 2);

        //Calcular los totales, desglosados por tipo de iva.
        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "select sum(Importe) as Bruto,sum(ImporteCosto) as Costo from {$this->_dataBaseName}.albaranes_lineas where (IDAlbaran='" . $this->getIDAlbaran() . "')";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $bruto = $rows[0]['Bruto'];

            $query = "select Iva,Recargo, sum(Importe) as Importe from {$this->_dataBaseName}.albaranes_lineas where (IDAlbaran='" . $this->getIDAlbaran() . "') group by Iva,Recargo order by Iva";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $totbases = 0;
            $totiva = 0;
            $totrec = 0;
            $bases = array();

            foreach ($rows as $key => $row) {
                $importe = $row['Importe'] * (1 - $pordcto / 100);
                $cuotaiva = round($importe * $row['Iva'] / 100, 2);
                $cuotarecargo = round($importe * $row['Recargo'] / 100, 2);
                $totbases += $importe;
                $totiva += $cuotaiva;
                $totrec += $cuotarecargo;

                $bases[$key] = array(
                    'b' => $importe,
                    'i' => $row['Iva'],
                    'ci' => $cuotaiva,
                    'r' => $row['Recargo'],
                    'cr' => $cuotarecargo
                );
            }

            $subtotal = $totbases + $totiva + $totrec;

            // Calcular el recargo financiero según la forma de pago
            $formaPago = new FormasPago($this->IDFP);
            $recFinanciero = $formaPago->getRecargoFinanciero();
            $cuotaRecFinanciero = $subtotal * $recFinanciero / 100;
            unset($formaPago);

            $total = $subtotal + $cuotaRecFinanciero;

            //Calcular el peso, volumen y n. de bultos de los productos inventariables
            switch ($_SESSION['ver']) {
                case '0': //Estandar
                    $columna = "Unidades";
                    break;
                case '1': //Cristal
                    $columna = "MtsAl";
                    break;
            }
            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "select sum(articulos.Peso*albaranes_lineas.{$columna}) as Peso,
                        sum(articulos.Volumen*albaranes_lineas.{$columna}) as Volumen,
                        sum(Unidades) as Bultos from articulos,albaranes_lineas
                      where (albaranes_lineas.IDArticulo=articulos.IDArticulo)
                        and (articulos.Inventario='1')
                        and (albaranes_lineas.IDAlbaran='{$this->IDAlbaran}')";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();

            $this->setImporte($bruto);
            $this->setBaseImponible1($bases[0]['b']);
            $this->setIva1($bases[0]['i']);
            $this->setCuotaIva1($bases[0]['ci']);
            $this->setRecargo1($bases[0]['r']);
            $this->setCuotaRecargo1($bases[0]['cr']);
            $this->setBaseImponible2($bases[1]['b']);
            $this->setIva2($bases[1]['i']);
            $this->setCuotaIva2($bases[1]['ci']);
            $this->setRecargo2($bases[1]['r']);
            $this->setCuotaRecargo2($bases[1]['cr']);
            $this->setBaseImponible3($bases[2]['b']);
            $this->setIva3($bases[2]['i']);
            $this->setCuotaIva3($bases[2]['ci']);
            $this->setRecargo3($bases[2]['r']);
            $this->setCuotaRecargo3($bases[2]['cr']);
            $this->setTotalBases($totbases);
            $this->setTotalIva($totiva);
            $this->setTotalRecargo($totrec);
            $this->setRecargoFinanciero($recFinanciero);
            $this->setCuotaRecargoFinanciero($cuotaRecFinanciero);
            $this->setTotal($total);
            $this->setPeso($rows[0]['Peso']);
            $this->setVolumen($rows[0]['Volumen']);
            $this->setBultos($rows[0]['Bultos']);
        }
    }

    /**
     * Confirma el albaran si no se ha superado el riesgo, que consiste en:
     *
     *  1.- Reservar mercancía en el registro de existencias sin indicar lote ni ubiación
     *      solo para aquellos artículos que estén sujetos a inventario
     *  2.- Poner la cabecera de albarán y sus líneas en estado CONFIRMADO (1)
     *
     */
    public function confirma() {

        // Si no está confirmado
        if ($this->getIDEstado()->getIDTipo() == 0) {

            // Comprobar el riesgo
            $cliente = new Clientes($this->IDCliente);
            $riesgo = $cliente->getRiesgo();
            $superadoRiesgo = ( ($riesgo['RI'] > 0) and ($riesgo['RE']['Importe'] >= $riesgo['RI']));
            unset($cliente);
            if (!$superadoRiesgo) {

                $em = new EntityManager("datos" . $_SESSION['emp']);
                $query = "SELECT t1.IDArticulo, t1.IDAlmacen, t1.Unidades, t1.UnidadMedida
                        FROM {$this->_dataBaseName}.albaranes_lineas as t1, articulos as t2
                        WHERE t1.IDAlbaran='{$this->IDAlbaran}'
                            AND t1.IDEstado='0'
                            AND t1.IDArticulo=t2.IDArticulo
                            AND t2.Inventario='1'";
                $em->query($query);
                $rows = $em->fetchResult();
                $em->desConecta();

                $exi = new Existencias();
                foreach ($rows as $row)
                    $exi->HazReserva($row['IDAlmacen'], $row['IDArticulo'], $row['Unidades'], $row['UnidadMedida']);
                unset($exi);

                // Marcar como Reservadas las líneas de albarán
                $em = new EntityManager("datos" . $_SESSION['emp']);
                $query = "update {$this->_dataBaseName}.albaranes_lineas set IDEstado='1' where IDAlbaran='{$this->IDAlbaran}' and IDEstado='0'";
                $em->query($query);
                $em->desConecta();
                unset($em);

                // Confirmar la cabecera del albaran
                $this->setIDEstado(1);
                $this->save();
            } else {
                $this->_errores[] = "No se puede confirmar se ha superado el riesgo";
                $this->_errores[] = "Albaranes Ptes Facturar " . $riesgo['AL']['Albaranes'] . " por " . $riesgo['AL']['Importe'] . "€";
                $this->_errores[] = "Recibos Ptes Cobro " . $riesgo['RE']['Recibos'] . " por " . $riesgo['RE']['Importe'] . "€";
                $this->_errores[] = "Límite de Riesgo " . $riesgo['RI'] . "€";
            }
        }
    }

    /**
     * Anula la confirmacion del albaran, que consiste en:
     *
     *  1.- Quitar la reserva de mercancia en el registro de existencias sin indicar lote ni ubicacion
     *      solo para aquellos artículos que estén sujetos a inventario
     *  2.- Poner la cabecera de albaran y sus lineas en estado PTE. DE CONFIRMAR (0)
     *
     */
    public function anulaConfirmacion() {
        // Si está confirmado
        if ($this->getIDEstado()->getIDTipo() == 1) {

            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "SELECT t1.IDArticulo, t1.IDAlmacen, t1.Unidades, t1.UnidadMedida
                        FROM {$this->_dataBaseName}.albaranes_lineas as t1, articulos as t2
                        WHERE t1.IDAlbaran='{$this->IDAlbaran}'
                            AND t1.IDEstado='1'
                            AND t1.IDArticulo=t2.IDArticulo
                            AND t2.Inventario='1'";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();

            // Quitar las reservas
            $exi = new Existencias();
            foreach ($rows as $row)
                $exi->quitaReserva($row['IDAlmacen'], $row['IDArticulo'], $row['Unidades'], $row['UnidadMedida']);
            unset($exi);

            // Poner en estado de PTE DE CONFIRMAR las líneas de albarán
            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "update {$this->_dataBaseName}.albaranes_lineas set IDEstado='0' where IDAlbaran='{$this->IDAlbaran}' and IDEstado='1'";
            $em->query($query);

            // Borrar las eventuales lineas de expedicion
            $query = "delete from {$em->getDataBase()}.expediciones where Entidad='AlbaranesCab' and IDEntidad='{$this->IDAlbaran}'";
            $em->query($query);
            $em->desConecta();
            unset($em);

            // Anular la reserva en la cabecera del albaran
            $this->setIDEstado(0);
            $this->save();
        }
    }

    /**
     * Expide el albarán en base a las líneas de expedición de cada línea de albarán
     *
     * Pasa el albarán al estado EXPEDIDO (2)
     *
     * @return boolean
     */
    public function expide() {

        if ($this->getIDEstado()->getIDTipo() == 1) {
            // Recorrer cada linea del albarán
            $lineaAlbaran = new AlbaranesLineas();
            $rows = $lineaAlbaran->cargaCondicion("IDLinea", "IDAlbaran='{$this->IDAlbaran}' and IDEstado='1'", "IDLinea ASC");
            foreach ($rows as $row) {
                $lineaAlbaran = new AlbaranesLineas($row['IDLinea']);
                if (!$lineaAlbaran->expide()) {
                    $this->_errores = $lineaAlbaran->getErrores();
                    break;
                }
            }
            unset($lineaAlbaran);

            if (count($this->_errores) == 0) {
                //Marcar el albarán como expedido y establecer la fecha de entrega
                $this->setIDEstado('2');
                $this->setFechaEntrega('');
                $this->save();
            }
        }

        return (count($this->_errores) == 0);
    }

    /**
     * Hace una copia del albarán.
     * Genera otro albarán en base al actual.
     * IMPORTANTE: SE TOMAN LOS PRECIOS ACTUALES DE LOS ARTICULOS.
     *
     * @return integer El id del albarán generado
     */
    public function duplica() {

        $idOrigen = $this->IDAlbaran;

        // Crear la cabecera del albaran
        $destino = $this;
        $destino->setIDAlbaran('');
        $destino->setIDAgente($_SESSION['USER']['user']['id']);
        $destino->setIDEstado(0);
        $destino->setFecha(date('d-m-Y'));
        $destino->setFechaEntrega('00-00-0000');
        $destino->setIDFactura(0);
        $destino->setClave('');
        $idDestino = $destino->create();

        // Crear las líneas de albaran
        if ($idDestino) {
            $linea = new AlbaranesLineas();
            $rows = $linea->cargaCondicion("IDLinea", "IDAlbaran='{$idOrigen}'", "IDLinea ASC");
            unset($linea);
            foreach ($rows as $row) {
                $lineaDestino = new AlbaranesLineas($row['IDLinea']);
                $lineaDestino->setIDAlbaran($idDestino);
                $lineaDestino->setIDAgente($_SESSION['USER']['user']['id']);
                $lineaDestino->setIDEstado(0);
                $lineaDestino->valida(); // Toma los precios vigentes (tarifa, promociones, etc)
                $lineaDestino->create();
            }
            unset($lineaDestino);
        } else {
            $this->_errores[] = "Hubo un error al duplicar el albaran. Revise los contadores.";
        }

        return $idDestino;
    }

    /**
     * Facturar el albaran.
     * Según la forma de pago, se actualiza la cartera de efectos.
     * Recibe el objeto contador que se utilizará para obtener el nº de factura.
     *
     * @param Contadores $contador El objeto contador
     * @param date $fecha La fecha de la factura (opcional, toma la del sistema)
     * @return int El id de la factura generada
     */
    public function facturar(Contadores $contador, $fecha = '') {

        if ($fecha == '')
            $fecha = date('d-m-Y');

        $idFactura = 0;

        if (($this->getIDEstado()->getIDTipo() == 2) and ($this->getIDFactura()->getIDFactura() == 0)) {
            // Buscar la cuenta contable de ventas para la sucursal
            $sucursal = new Sucursales($this->IDSucursal);
            $ctaVentas = $sucursal->getCtaContableVentas();
            unset($sucursal);

            // Lee contador
            $idContador = $contador->getIDContador();
            $numeroFactura = $contador->asignaContador();
            unset($contador);

            // Crear la cabecera de la factura
            $factura = new FemitidasCab();
            $factura->setIDSucursal($this->IDSucursal);
            $factura->setIDContador($idContador);
            $factura->setNumeroFactura($numeroFactura);
            $factura->setIDAgente($_SESSION['USER']['user']['id']);
            $factura->setIDComercial($this->IDComercial);
            $factura->setFecha($fecha);
            $factura->setIDCliente($this->IDCliente);
            $factura->setImporte($this->Importe);
            $factura->setDescuento($this->Descuento);
            $factura->setBaseImponible1($this->BaseImponible1);
            $factura->setIva1($this->Iva1);
            $factura->setCuotaIva1($this->CuotaIva1);
            $factura->setRecargo1($this->Recargo1);
            $factura->setCuotaRecargo1($this->CuotaRecargo1);
            $factura->setBaseImponible2($this->BaseImponible2);
            $factura->setIva2($this->Iva2);
            $factura->setCuotaIva2($this->CuotaIva2);
            $factura->setRecargo2($this->Recargo2);
            $factura->setCuotaRecargo2($this->CuotaRecargo2);
            $factura->setBaseImponible3($this->BaseImponible3);
            $factura->setIva3($this->Iva3);
            $factura->setCuotaIva3($this->CuotaIva3);
            $factura->setRecargo3($this->Recargo3);
            $factura->setCuotaRecargo3($this->CuotaRecargo3);
            $factura->setTotalBases($this->TotalBases);
            $factura->setTotalIva($this->TotalIva);
            $factura->setTotalRecargo($this->TotalRecargo);
            $factura->setRecargoFinanciero($this->RecargoFinanciero);
            $factura->setCuotaRecargoFinanciero($this->CuotaRecargoFinanciero);
            $factura->setTotal($this->Total);
            $factura->setCuentaVentas($ctaVentas);
            $factura->setPeso($this->Peso);
            $factura->setVolumen($this->Volumen);
            $factura->setBultos($this->Bultos);
            $factura->setExpedicion($this->Expedicion);
            $factura->setIDAgencia($this->IDAgencia);
            $factura->setClave(md5($numeroFactura));
            $factura->setIDFP($this->IDFP);

            $idFactura = $factura->create();

            if ($idFactura != 0) {
                // Crear las lineas de factura
                $linAlbaran = new AlbaranesLineas();
                $rows = $linAlbaran->cargaCondicion("*", "IDAlbaran='{$this->IDAlbaran}'", "IDLinea ASC");
                unset($linAlbaran);
                foreach ($rows as $row) {
                    $linFactura = new FemitidasLineas();
                    $linFactura->setIDFactura($idFactura);
                    $linFactura->setIDArticulo($row['IDArticulo']);
                    $linFactura->setDescripcion($row['Descripcion']);
                    $linFactura->setUnidades($row['Unidades']);
                    $linFactura->setPrecio($row['Precio']);
                    $linFactura->setDescuento($row['Descuento']);
                    $linFactura->setImporte($row['Importe']);
                    $linFactura->setImporteCosto($row['ImporteCosto']);
                    $linFactura->setIDAlbaran($row['IDAlbaran']);
                    $linFactura->setIDLineaAlbaran($row['IDLinea']);
                    $linFactura->setIva($row['Iva']);
                    $linFactura->setRecargo($row['Recargo']);
                    $linFactura->setIDVenta($row['IDVenta']);
                    $linFactura->setComisionAgente($row['ComisionAgente']);
                    $linFactura->setComisionMontador($row['ComisionMontador']);
                    $linFactura->setComisionar($row['Comisionar']);
                    $linFactura->setIDAgente($_SESSION['USER']['user']['id']);
                    $linFactura->setIDComercial($row['IDComercial']);
                    $linFactura->setIDPromocion($row['IDPromocion']);
                    $linFactura->setAltoAl($row['AltoAl']);
                    $linFactura->setAnchoAl($row['AnchoAl']);
                    $linFactura->setMtsAl($row['MtsAl']);
                    $linFactura->setAltoFa($row['AltoFa']);
                    $linFactura->setAnchoFa($row['AnchoFa']);
                    $linFactura->setMtsFa($row['MtsFa']);

                    if ($linFactura->create()) {
                        // Pongo el estado de la línea de albarán a "Facturado"
                        $lineaAlbaran = new AlbaranesLineas($row['IDLinea']);
                        $lineaAlbaran->setIDEstado(3);
                        $lineaAlbaran->save();
                        unset($lineaAlbaran);
                    }
                    unset($linFactura);
                }
            }

            if ($idFactura != 0) {
                // Crear vencimientos
                $factura->creaVctos();

                // Anotar en caja sin procede
                $factura->anotaEnCaja();

                // Actualiza la cabecera del albarán
                $this->setIDFactura($idFactura);
                $this->setIDEstado(3);
                $this->save();
            }
            unset($factura);
        }
        return $idFactura;
    }

    /**
     * CÁLCULO DEL RECARGO ENERGÉTICO.
     * Si hay artículos que están sujetos a recargo energético, hay que calcular
     * el importe del impuesto e incluir una línea automática con dicho valor.
     * Si la línea ya está incluida, se actualiza.
     *
     * El articulo que se factura para recargo energetico viene definido en el parametro REART
     * Y el precio kilo en el parametro REIMP
     */
    public function calculaRecargoEnergetico() {

        $parametro = new Parametros();
        $reArticulo = $parametro->find('Codigo', 'REART');
        unset($parametro);

        // Ver si existe el articulo que se ha definido en parametros
        // para facturar el recargo energetico
        $articulo = new Articulos();
        $articulo = $articulo->find('Codigo', $reArticulo->getValor());

        if ($articulo->getIDArticulo()) {
            // Se ha definido el parametro con el codigo del articulo
            // para aplicar el recargo energetico. Ahora hay que ver si en el albaran
            // en curso hay articulos sujetos a recargo energético.
            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "select t1.*,t2.Peso from albaranes_lineas as t1, articulos as t2 where t1.IDAlbaran='{$this->IDAlbaran}' and t1.IDArticulo=t2.IDArticulo and t2.RecargoEnergetico='1'";
            $em->query($query);
            $rows = $em->fetchResult();
            if ($rows) {
                //Hay articulos sujetos a recargo energetico
                $reKilos = 0;
                foreach ($rows as $row) {
                    $reKilos += $row['MtsFa'] * $row['Peso'];
                }

                $reImporte = $reKilos * $articulo->getPrecioVenta();
                //Ver si ya está la linea de albaran creada.
                $query = "select IDLinea from albaranes_lineas where IDAlbaran='{$this->IDAlbaran}' and IDArticulo='{$articulo->getIDArticulo()}'";
                $em->query($query);
                $rows = $em->fetchResult();
                if ($rows) {
                    // Ya estaba, actualizar
                    $query = "update albaranes_lineas set Unidades='{$reKilos}',Precio='{$articulo->getPrecioVenta()}',Importe='{$reImporte}' where IDLinea='{$rows[0]['IDLinea']}'";
                    $em->query($query);
                } else {
                    // No está, crear
                    $lineaAlbaran = new AlbaranesLineas();
                    $lineaAlbaran->setIDAlbaran($this->IDAlbaran);
                    $lineaAlbaran->setIDArticulo($articulo->getIDArticulo());
                    $lineaAlbaran->setDescripcion($articulo->getDescripcion());
                    $lineaAlbaran->setUnidades($reKilos);
                    $lineaAlbaran->setPrecio($articulo->getPrecioVenta());
                    $lineaAlbaran->setImporte($reImporte);
                    $lineaAlbaran->setIDAgente($_SESSION['USER']['user']['id']);
                    $lineaAlbaran->setIDComercial($this->IDComercial);
                    $lineaAlbaran->setIDAlmacen($this->IDAlmacen);
                    $lineaAlbaran->setIva($articulo->getIDIva()->getIva());
                    $lineaAlbaran->create();
                    unset($lineaAlbaran);
                }
            } else {
                // No hay articulos con recargo energetico.
                // Borro el eventual cargo de recargo energetico.
                $query = "delete from albaranes_lineas where IDAlbaran='{$this->IDAlbaran}' and IDArticulo='{$articulo->getIDArticulo()}'";
                $em->query($query);
            }
            $em->desConecta();
            unset($em);
        }
    }

    /**
     * Calcula el beneficio de un albaran
     *
     * Devuelve un array con el precio de venta, el costo (bases imponibles)
     * y el beneficio del albaran
     *
     * array (
     *  'Venta'     => Importe total del albaran base imponible,
     *  'Costo'     => Importe total del costo del albarán base imponible
     *  'Beneficio' => Venta - Costo
     * )
     *
     * @param integer Id del albaran
     * @return array
     */
    public function getBeneficio($idAlbaran = '') {
        if ($idAlbaran == '')
            $idAlbaran = $this->getIDAlbaran();

        $beneficio = array();

        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "select sum(ImporteCosto) as Costo from {$this->_dataBaseName}.albaranes_lineas where IDAlbaran='{$idAlbaran}';";
            if ($this->_em->query($query)) {
                $rows = $this->_em->fetchResult();
                $beneficio = $rows[0];
            } else {
                $this->_errores[] = $this->_em->getError();
            }
            $this->_em->desConecta();
        } else {
            $this->_errores[] = $this->_em->getError();
        }

        $beneficio['Venta'] = $this->TotalBases;
        $beneficio['Beneficio'] = $beneficio['Venta'] - $beneficio['Costo'];

        return $beneficio;
    }

    /**
     * Comprueba el riesgo del cliente
     */
    protected function validaLogico() {
        $cliente = new Clientes($this->IDCliente);
        $riesgo = $cliente->getRiesgo();
        unset($cliente);
        if ($riesgo['RE']['Recibos'] > 0) {
            $this->_alertas[] = "Albaranes Ptes Facturar " . $riesgo['AL']['Albaranes'] . " por " . $riesgo['AL']['Importe'] . "€";
            $this->_alertas[] = "Recibos Ptes Cobro " . $riesgo['RE']['Recibos'] . " por " . $riesgo['RE']['Importe'] . "€";
            $this->_alertas[] = "Límite de Riesgo " . $riesgo['RI'] . "€";
        }
    }

    /**
     * Devuelve la fecha del albarán en formato aaaa-mm-dd
     * @return string La fecha en formato aaaa-mm-dd
     */
    public function getDate() {
        return $this->Fecha;
    }

    /**
     * Devuelve la fecha de entregra del albarán en formato aaaa-mm-dd
     * @return string La fecha de entrega en formato aaaa-mm-dd
     */
    public function getDateEntrega() {
        return $this->FechaEntrega;
    }

    /**
     * Devuelve una array con los id's de albaranes que están
     * pendientes de facturar (IDEstado=2) del clientes $idCliente
     * en el período $desdeFecha - $hastaFecha
     *
     * @param integer $idSucursal EL id de sucursal (opcional)
     * @param integer $idCliente El id de cliente
     * @param string $desdeFecha Fecha en formato dd/mm/aaaa
     * @param string $hastaFecha Fecha en formato dd/mm/aaaa
     * @return array Array con los id de albarán
     */
    public function getPendientesFacturar($idSucursal, $idCliente, $desdeFecha, $hastaFecha) {

        $fecha = new Fecha($desdeFecha);
        $desdeFecha = $fecha->getaaaammdd();
        $fecha = new Fecha($hastaFecha);
        $hastaFecha = $fecha->getaaaammdd();
        unset($fecha);

        if ($idSucursal != '')
            $filtroSucursal = "(1)";
        else
            $filtroSucursal = "(IDSucursal = '{$idSucursal}')";
        $filtro = $filtroSucursal . " and
                  (IDCliente='{$idCliente}') and
                  (FechaEntrega>='{$desdeFecha}') and
                  (FechaEntrega<='{$hastaFecha}') and
                  (IDEstado=2)";

        $albaran = new AlbaranesCab();
        $rows = $albaran->cargaCondicion("IDAlbaran", $filtro, "FechaEntrega,IDAlbaran ASC");
        unset($albaran);

        return $rows;
    }

    public function getVencimiento() {

        $fecha = $this->getFecha();
        $f = new Fecha($fecha);
        $dias = $this->getIDFP()->getDiaPrimerVcto();

        $nuevaFecha = date('d-m-Y', strtotime("+{$dias} day", strtotime($f->getaaaammdd())));

        return $nuevaFecha;
    }

}

?>