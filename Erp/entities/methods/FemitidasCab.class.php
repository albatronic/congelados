<?php

/**
 * Description of FemitidasCab
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class FemitidasCab extends FemitidasCabEntity {

    public function __toString() {
        if ($this->NumeroFactura)
            return $this->getNumeroFactura();
        else
            return "";
    }

    /**
     * Borra una factura emitida, sus líneas, los posibles recibos
     * y marca el/los albaranes y lineas de albaranres asociados como no facturados
     * Siempre que no esté traspasa a contabilidad (Asiento=0)
     *
     * @return boolean
     */
    public function erase() {

        if ($this->Asiento == 0) {
            if ($this->borraVctos()) {
                if ($this->borraLineas()) {
                    // Actualiza la cabecera del albaran
                    $this->conecta();
                    if (is_resource($this->_dbLink)) {
                        $query = "UPDATE albaranes_cab SET IDFactura='0', IDEstado='2' WHERE IDFactura='{$this->IDFactura}'";
                        if (!$this->_em->query($query))
                            $this->_errores[] = $this->_em->getError();
                    }
                    // Borrar la cabecera de la factura
                    parent::erase();
                }
            }
        } else
            $this->_errores[] = "No se puede borrar. Está traspasada a contabilidad";

        return (count($this->_errores) == 0);
    }

    /**
     * Devuelve un array de objetos recibos de la factura
     * que están en el estado $idEstado. Por defecto todos
     * 
     * @param integer $idEstado El estado de los recibos (opcional)
     * @return array Objetos recibos de la factura
     */
    public function getRecibos($idEstado = '') {
        $recibos = array();

        $filtro = "IDFactura='{$this->IDFactura}'";
        if ($idEstado != '')
            $filtro .= " AND IDEstado='{$idEstado}'";

        $recibo = new RecibosClientes();
        $rows = $recibo->cargaCondicion("IDRecibo", $filtro, "Vencimiento ASC");
        foreach ($rows as $row) {
            $recibos[] = new RecibosClientes($row['IDRecibo']);
        }
        unset($recibo);

        return $recibos;
    }

    /**
     * Devuelve la suma de los importes de todos los recibos
     * de la factura en curso. Si no se indica estado, suma todos
     *
     * @param integer $idEstado El estado de los recibos (opcional)
     * @return real La suma de los importes de los recibos
     */
    public function getSumaRecibos($idEstado = '') {

        $suma = 0.0;

        $filtro = "IDFactura='{$this->IDFactura}'";
        if ($idEstado != '')
            $filtro .= " AND IDEstado='{$idEstado}'";

        $recibo = new RecibosClientes();
        $rows = $recibo->cargaCondicion("Sum(Importe) as Suma", $filtro);
        $suma = $rows[0]['Suma'];

        return $suma;
    }

    /**
     * Recalcula los importes de la factura en base a sus lineas
     * Se utiliza durante el proceso de facturacion agrupada
     */
    public function recalcula() {

        //Si el cliente no está sujeto a iva
        //pongo el iva a cero en las líneas para evitar que por cambio
        //de cliente se aplique indebidamente
        $cliente = new Clientes($this->IDCliente);
        if ($cliente->getIva()->getIDTipo() == '0') {
            $this->conecta();
            if (is_resource($this->_dbLink)) {
                $query = "UPDATE {$this->_dataBaseName}.femitidas_lineas SET `Iva`='0', `Recargo`='0' WHERE `IDFactura`= '{$this->IDFactura}'";
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
                $query = "update femitidas_lineas set `Recargo`='0' where `IDFactura` = '{$this->IDFactura}'";
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
            $query = "select sum(importe) as Bruto,sum(ImporteCosto) as Costo from {$this->_dataBaseName}.femitidas_lineas where (IDFactura='" . $this->IDFactura . "')";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $bruto = $rows[0]['Bruto'];

            $query = "select Iva,Recargo, sum(Importe) as Importe from {$this->_dataBaseName}.femitidas_lineas where (IDFactura='" . $this->IDFactura . "') group by Iva,Recargo order by Iva";
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
            $query = "select sum(articulos.Peso*femitidas_lineas.{$columna}) as Peso,
                        sum(articulos.Volumen*femitidas_lineas.{$columna}) as Volumen,
                        sum(Unidades) as Bultos from articulos,femitidas_lineas
                      where (femitidas_lineas.IDArticulo=articulos.IDArticulo)
                        and (articulos.Inventario='1')
                        and (femitidas_lineas.IDFactura='{$this->IDFactura}')";
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

            $this->save();
        }
    }

    /**
     * Crea los recibos de la factura en curso en base a la forma de pago.
     * Si el n. de vctos de la forma de pago es 0, no se crea ningún vencimiento.
     */
    public function creaVctos() {

        if ($this->Total == 0)
            return;

        $factura = new FemitidasCab($this->IDFactura);

        //SI LA FACTURA ES SIN IVA, EN CASO DE CREAR RECIBOS SE MARCARÁN
        //CON N. DE ASIENTO 999999 PARA QUE NO SE TRASPASEN A CONTABILIDAD.
        $tieneiva = (($factura->getIva1() + $factura->getIva2() + $factura->getIva3()) != 0);
        $asiento = ($tieneiva) ? 0 : 999999;

        $formaPago = $factura->getIDFP();
        $nvctos = $formaPago->getNumeroVctos();
        $fecha = new Fecha($factura->getFecha());
        $mes = $fecha->getmm();
        $dia = $fecha->getdd();
        $anio = $fecha->getaaaa();
        unset($fecha);

        if ($factura->getIDCliente()->getDiaDePago() > 0) {
            //SI TIENE UN DIA DE PAGO ESTABLECIDO, LOS VENCIMIENTOS SE CALCULAN
            //A PARTIR DE ESE DIA PERO DEL MES SIGUIENTE
            $mes = $mes + 1;
            if ($mes > 12) {
                $mes = 1;
                $anio = $anio + 1;
            }
            $dia = $factura->getIDCliente()->getDiaDePago();
        }

        if ($nvctos > 0) {
            $importe = ROUND($factura->getTotal() / $nvctos, 2);
            $diferencia = $factura->getTotal() - $importe * $nvctos;
            $i = 0;
            while ($i < $nvctos) {
                $i = $i + 1;
                if ($i == 1) {
                    $intervalo = $formaPago->getDiaPrimerVcto();
                    $importeRecibo = $importe + $diferencia;
                } else {
                    $intervalo = $intervalo + $formaPago->getDiasIntervalo();
                    $importeRecibo = $importe;
                }
                $numRecibo = str_pad($i, 2, "0", STR_PAD_LEFT) . str_pad($nvctos, 2, "0", STR_PAD_LEFT);
                $fVcto = date('Y-m-d', mktime(0, 0, 0, $mes, $dia + $intervalo, $anio));

                $recibo = new RecibosClientes();
                $recibo->setRecibo($numRecibo);
                $recibo->setIDSucursal($factura->getIDSucursal()->getIDSucursal());
                $recibo->setIDFactura($factura->getIDFactura());
                $recibo->setIDCliente($factura->getIDCliente()->getIDCliente());
                $recibo->setIDComercial($factura->getIDComercial()->getIDAgente());                
                $recibo->setFecha($factura->getFecha());
                $recibo->setVencimiento($fVcto);
                $recibo->setImporte($importeRecibo);
                $recibo->setCBanco($factura->getIDCliente()->getCtaCorriente());
                $recibo->setConcepto("Cobro Factura");
                $recibo->setAsiento($asiento);
                $recibo->setIDEstado($formaPago->getEstadoRecibo()->getIDTipo());
                // Si el recibo se crea cobrado, se pone la fecha de vencimiento en la remesa
                if ($formaPago->getEstadoRecibo()->getIDTipo() == '6')
                    $recibo->setIDRemesa(str_replace("-","", $fVcto));
                else
                    $recibo->setIDRemesa('');
                $recibo->setRemesar(1);
                $recibo->setCContable($formaPago->getCContable());
                $recibo->create();
                unset($recibo);
            }
        }
        unset($factura);
        unset($formaPago);
    }

    /**
     * Borrar los vencimientos de la factura
     * siempre y cuando no este traspasado a contabilidad (Asiento=0)
     *
     * Por lo tanto, borra los que no están traspasados; y los que si lo están
     * los deja.
     *
     * @return boolean
     */
    public function borraVctos() {
        $ok = false;

        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "delete from recibos_clientes where IDFactura='{$this->IDFactura}' and Asiento='0'";
            if (!$this->_em->query($query))
                $this->_errores[] = $this->_em->getError();
            else
                $ok = true;
            $this->_em->desConecta();
        }

        return $ok;
    }

    /**
     * Borrar las lineas de la factura y
     * pone en estado de no facturado (2) las lineas de albaran
     * de las que provienen
     *
     * @return boolean
     */
    private function borraLineas() {
        $ok = true;

        $linea = new FemitidasLineas();
        $rows = $linea->cargaCondicion("IDLinea", "IDFactura='{$this->IDFactura}'");
        unset($linea);
        foreach ($rows as $lineaFactura) {
            $lineaFactura = new FemitidasLineas($lineaFactura['IDLinea']);
            // Cambia estado linea albaran
            $lineaAlbaran = $lineaFactura->getIDLineaAlbaran();
            $lineaAlbaran->setIDEstado(2);
            $lineaAlbaran->save();
            // Borrar linea factura
            $lineaFactura->erase();
        }
        return $ok;
    }

    /**
     * Realiza el apunte de caja si procede según la forma de pago
     */
    public function anotaEnCaja() {

        if ($this->getIDFP()->getAnotarEnCaja()->getIDTipo() == '1') {
            $arqueo = new CajaArqueos();
            $arqueo->anotaEnCaja($this);
        }
    }

    /**
     * Calcula el beneficio de una factura
     *
     * Devuelve un array con el precio de venta, el costo (bases imponibles)
     * y el beneficio de la factura
     *
     * array (
     *  'Venta'     => Importe total de la factura base imponible,
     *  'Costo'     => Importe total del costo de la factura base imponible
     *  'Beneficio' => Venta - Costo
     * )
     *
     * @param integer Id de la factura
     * @return array
     */
    public function getBeneficio($idFactura = '') {
        if ($idFactura == '')
            $idFactura = $this->getIDFactura();

        $beneficio = array();

        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "select sum(ImporteCosto) as Costo from {$this->_dataBaseName}.femitidas_lineas where IDFactura='{$idFactura}';";
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

}

?>
