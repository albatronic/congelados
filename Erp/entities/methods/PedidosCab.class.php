<?php

/**
 * Description of PedidosCab
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class PedidosCab extends PedidosCabEntity {

    public function __toString() {
        return $this->getIDPedido();
    }

    public function getNumeroDocumento() {
        return $this->IDPedido;
    }

    /**
     * Carga de datos en las variables de la clase
     */
    protected function load() {
        if ($this->IDPedido == '') {
            //Si el nº de pedido está vacio (se ha instanciado un objeto vacio),
            //asigno valores por defecto (agente,sucursal,almacen).
            $this->setIDAgente($_SESSION['USER']['user']['id']);

            $agente = new Agentes();

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

    /**
     * Guarda la informacion (update)
     */
    public function save() {
        $this->recalcula();
        parent::save();
    }

    /**
     * Borra un pedido y sus líneas
     * Siempre que esté en estado 0 (pte de recepcion)
     *
     * @return boolean
     */
    public function erase() {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = "DELETE FROM pedidos_cab WHERE `IDPedido`='{$this->IDPedido}' AND IDEstado='0'";
            if ($this->_em->query($query)) {
                //Borrar líneas de pedidos
                $query = "DELETE FROM pedidos_lineas where `IDPedido`='{$this->IDPedido}'";
                if (!$this->_em->query($query))
                    $this->_errores = $this->_em->getError();
            } else
                $this->_errores = $this->_em->getError();
            $this->_em->desConecta();
        }
        unset($this->_em);

        return (count($this->_errores) == 0);
    }

    /**
     * Recalcula los importes del pedido en base a sus lineas
     * Actualiza las propiedades de totales pero no salva los datos.
     * IMPORTANTE: Para que los calculos tomen efecto hay que llamar al método save()
     */
    public function recalcula() {

        //Fuerzo el almacen al de la cabecera del pedido
        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "UPDATE pedidos_lineas SET `IDAlmacen`='{$this->IDAlmacen}' WHERE `IDPedido` = '{$this->IDPedido}'";
            $this->_em->query($query);
        }
        $this->_em->desConecta();


        //Si el proveedor no está sujeto a iva
        //pongo el iva a cero en las líneas para evitar que por cambio
        //de proveedor se aplique indebidamente
        $proveedor = new Proveedores($this->IDProveedor);
        if ($proveedor->getIva()->getIDTipo() == '0') {
            $this->conecta();
            if (is_resource($this->_dbLink)) {
                $query = "UPDATE pedidos_lineas SET `Iva`='0', `Recargo`='0' WHERE `IDPedido`= '{$this->IDPedido}'";
                $this->_em->query($query);
            }
            $this->_em->desConecta();
        }
        unset($proveedor);

        //SI TIENE DESCUENTO, CALCULO EL PORCENTAJE QUE SUPONE RESPECTO AL IMPORTE BRUTO
        //PARA REPERCUTUIRLO PORCENTUALMENTE A CADA BASE
        $pordcto = 0;
        if ($this->getDescuento() != 0)
            $pordcto = round(100 * ($this->getDescuento() / $this->getImporte()), 2);

        //Calcular los totales, desglosados por tipo de iva.
        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "select sum(importe) as Bruto from pedidos_lineas where (IDPedido='{$this->IDPedido}')";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $bruto = $rows[0]['Bruto'];

            $query = "select Iva, Recargo, sum(Importe) as Importe from pedidos_lineas where (IDPedido='{$this->IDPedido}') group by Iva, Recargo order by Iva";
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

            $total = $totbases + $totiva + $totrec;

            //Calcular el peso, volumen y n. de bultos de los productos inventariables
            //$query = "select sum(articulos.Peso*pedidos_lineas.Unidades) as Peso, sum(articulos.volumen*pedidos_lineas.Unidades) as Volumen, sum(Unidades) as Bultos from articulos,pedidos_lineas where (pedidos_lineas.IDArticulo=articulos.IDArticulo) and (articulos.Inventario='1') and (pedidos_lineas.IDPedido='" . $this->getIDPedido() . "')";
            //$this->_em->query($query);
            //$rows = $this->_em->fetchResult();

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
            $this->setTotal($total);
            //$this->setPeso($rows[0]['Peso']);
            //$this->setVolumen($rows[0]['Volumen']);
            //$this->setBultos($rows[0]['Bultos']);
        }
    }

    /**
     * Confirma el pedido, que consiste actualizar las existencias a ENTRANDO.
     * Pasando del estado PTE. CONFIRMAR (0) a CONFIRMADO (1).
     * Para poder confirmar es obligatorio indicar una fecha prevista de entrega
     * Solo se tienen en cuenta los artículos inventariables.
     * Anota en el registro de existencias las cantidades pendientes de entrada
     */
    public function confirma() {

        if ($this->getFechaEntrega() == '00/00/0000')
            $this->_errores[] = "Antes de confirmar el pedido debe indicar una fecha prevista de entrega";
        elseif ($this->FechaEntrega < $this->Fecha)
            $this->_errores[] = "La fecha prevista de entrega debe ser igual o mayor a la fecha del pedido";

        // Si no está confirmado
        if (($this->getIDEstado()->getIDTipo() == 0) and (count($this->_errores) == 0)) {

            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "SELECT t1.IDArticulo, t1.IDAlmacen, sum(t1.Unidades) as Entrando, t1.UnidadMedida
                        FROM pedidos_lineas as t1, articulos as t2
                        WHERE t1.IDPedido='{$this->IDPedido}'
                            AND t1.IDEstado='0'
                            AND t1.IDArticulo=t2.IDArticulo
                            AND t2.Inventario='1'
                        GROUP BY t1.IDArticulo,t1.IDAlmacen, t1.UnidadMedida";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();

            // Poner previsión de entrada
            $exi = new Existencias();
            foreach ($rows as $row)
                $exi->hazEntrando($row['IDAlmacen'], $row['IDArticulo'], $row['Entrando'], $row['UnidadMedida'], $this->Deposito);
            unset($exi);

            // Marcar como Entrando las líneas de pedido
            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "update pedidos_lineas set IDEstado='1' where IDPedido='{$this->IDPedido}' and IDEstado='0'";
            $em->query($query);
            $em->desConecta();
            unset($em);

            // Confirmar la cabecera del pedido
            $this->setIDEstado(1);
            $this->save();
        }
    }

    /**
     * Anula la confirmación del pedido
     * Pasando del estado CONFIRMADO (1) a PTE. CONFIRMAR (0)
     * Solo se tienen en cuenta los artículos inventariables.
     * Anula del registro de existencias las cantidades pendientes de entrada
     */
    public function anulaConfirmacion() {
        // Si está confirmado
        if ($this->getIDEstado()->getIDTipo() == 1) {

            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "SELECT t1.IDArticulo, t1.IDAlmacen, sum(t1.Unidades) as Entrando, t1.UnidadMedida
                        FROM pedidos_lineas as t1, articulos as t2
                        WHERE t1.IDPedido='{$this->IDPedido}'
                            AND t1.IDEstado='1'
                            AND t1.IDArticulo=t2.IDArticulo
                            AND t2.Inventario='1'
                        GROUP BY t1.IDArticulo, t1.IDAlmacen, t1.UnidadMedida";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();

            // Quitar previsión de entrada
            $exi = new Existencias();
            foreach ($rows as $row)
                $exi->quitaEntrando($row['IDAlmacen'], $row['IDArticulo'], $row['Entrando'], $row['UnidadMedida'], $this->Deposito);
            unset($exi);

            // Marcar como NO CONFIRMADAS las líneas de pedido y
            // quitar la eventual asignación de lotes y UnidadesRecbidas
            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "update pedidos_lineas set IDEstado='0' where IDPedido='{$this->IDPedido}' and IDEstado='1'";
            $em->query($query);

            // Borrar las eventuales lineas de recepción
            $query = "delete from {$em->getDataBase()}.recepciones where Entidad='PedidosCab' and IDEntidad='{$this->IDPedido}'";
            $em->query($query);
            $em->desConecta();

            // Anular la reserva en la cabecera del pedido
            // y quitar la fecha prevista de entrega y las posibles incidencias
            $this->setIDEstado(0);
            $this->setFechaEntrega('00/00/0000');
            $this->setIncidencias('');
            $this->save();
        }
    }

    /**
     * Recepciona el pedido en base a las lineas de recepción de cada linea de pedido.
     *
     * Actualiza los precios (Pvd,Pmc,Margen,Pvp) del artículo
     *
     * Pasa el pedido al estado RECEPCIONADO (2)
     *
     * @param string $incidencias Posibles incidencias en la recepcion
     * @return boolean
     */
    public function recepciona($incidencias) {

        if ($this->getIDEstado()->getIDTipo() == '1') {
            // Recorrer cada linea del pedido
            $lineaPedido = new PedidosLineas();
            $rows = $lineaPedido->cargaCondicion("IDLinea", "IDPedido='{$this->IDPedido}' and IDEstado='1'", "IDLinea ASC");
            foreach ($rows as $row) {
                $lineaPedido = new PedidosLineas($row['IDLinea']);
                if (!$lineaPedido->recepciona()) {
                    $this->_errores = $lineaPedido->getErrores();
                    break;
                }
            }
            unset($lineaPedido);

            if (count($this->_errores) == 0) {
                //Marcar el pedido como recepcionado, poner la fecha de entrada y guardar las eventuales incidencias
                $usuario = new Agentes($_SESSION['USER']['user']['id']);
                unset($usuario);
                $this->setIDEstado('2');
                $this->setFechaEntrada('');
                $this->setIncidencias($incidencias);
                $this->save();
            }
        }

        return (count($this->_errores) == 0);
    }

    /**
     * Facturar el pedido
     * Según la forma de pago, se actualiza la cartera de efectos.
     * Recibe el objeto contador que se utilizará para obtener el nº de factura.
     *
     * @param Contadores $contador El objeto contador
     * @param date $fecha La fecha de la factura (opcional, toma la del sistema)
     * @param string $suFactura El numero de factura del proveedor (opcional)
     * @return int El id de la factura generada
     */
    public function facturar(Contadores $contador, $fecha='', $suFactura='') {

        if ($fecha == '')
            $fecha = date('d-m-Y');

        $idFactura = 0;

        if ( ($this->getIDEstado()->getIDTipo() == 2) and ($this->getIDFactura()->getIDFactura() == 0) ) {
            // Buscar la cuenta contable de compras para la sucursal
            $sucursal = new Sucursales($this->IDSucursal);
            $ctaCompras = $sucursal->getCtaContableCompras();
            unset($sucursal);

            // Lee contador
            $idContador = $contador->getIDContador();
            $numeroFactura = $contador->asignaContador();
            unset($contador);

            // Crear la cabecera de la factura
            $factura = new FrecibidasCab();
            $factura->setIDSucursal($this->IDSucursal);
            $factura->setIDContador($idContador);
            $factura->setNumeroFactura($numeroFactura);
            $factura->setSuFactura($suFactura);
            $factura->setFecha($fecha);
            $factura->setIDProveedor($this->IDProveedor);
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
            $factura->setTotal($this->Total);
            $factura->setReferencia($this->Referencia);
            $factura->setCuentaCompras($ctaCompras);
            $factura->setClave(md5($numeroFactura));
            $factura->setIDFP($this->IDFP);

            $idFactura = $factura->create();

            if ($idFactura != 0) {
                // Crear las lineas de factura
                $linPedido = new PedidosLineas();
                $rows = $linPedido->cargaCondicion("*", "IDPedido='{$this->IDPedido}'", "IDLinea ASC");
                unset($linPedido);
                foreach ($rows as $row) {
                    $linFactura = new FrecibidasLineas();
                    $linFactura->setIDFactura($idFactura);
                    $linFactura->setIDArticulo($row['IDArticulo']);
                    $linFactura->setDescripcion($row['Descripcion']);
                    $linFactura->setUnidades($row['UnidadesPtesFacturar']);
                    $linFactura->setPrecio($row['Precio']);
                    $linFactura->setDescuento($row['Descuento']);
                    $linFactura->setIDPedido($row['IDPedido']);
                    $linFactura->setIDLineaPedido($row['IDLinea']);
                    $linFactura->setIva($row['Iva']);

                    if ($linFactura->create()) {
                        // Pongo el estado de la linea de pedido a "Facturado"
                        $lineaPedido = new PedidosLineas($row['IDLinea']);
                        $lineaPedido->setIDEstado(3);
                        $lineaPedido->setUnidadesPtesFacturar(0);
                        $lineaPedido->save();
                        unset($lineaPedido);
                    }
                    unset($linFactura);
                }
            }

            if ($idFactura != 0) {
                // Recalcula la factura: 
                // puede que las cantidades facturas sean distintas a las pedidas
                $factura->recalcula();

                // Crear vencimientos
                $factura->creaVctos();

                // Anotar en caja si procede
                $factura->anotaEnCaja();

                // Actualiza la cabecera del pedido
                $this->setIDFactura($idFactura);
                $this->setIDEstado(3);
                $this->save();
            }
            unset($factura);
        }
        return $idFactura;
    }

    /**
     * Hace una copia del pedido.
     * Genera otro pedido en base al actual.
     * IMPORTANTE: SE TOMAN LOS PRECIOS ACTUALES DE LOS ARTICULOS.
     *
     * @return integer El id del pedido generado
     */
    public function duplica() {

        $idOrigen = $this->IDPedido;

        // Crear la cabecera del pedido
        $destino = $this;
        $destino->setIDPedido('');
        $destino->setIDAgente($_SESSION['USER']['user']['id']);
        $destino->setIDEstado(0);
        $destino->setIDFactura(0);
        $destino->setFecha(date('d-m-Y'));
        $destino->setFechaEntrega('00-00-0000');
        $destino->setFechaEntrada('00-00-0000');
        $destino->setSuPedido('');
        $destino->setReferencia('');
        $destino->setObservaciones('Duplicado del pedido n. ' . $idOrigen);
        $destino->setIncidencias('');
        $destino->setClave('');
        $idDestino = $destino->create();

        // Crear las líneas de pedido
        $linea = new PedidosLineas();
        $rows = $linea->cargaCondicion("IDLinea", "IDPedido='{$idOrigen}'", "IDLinea ASC");
        unset($linea);
        foreach ($rows as $row) {
            $lineaDestino = new PedidosLineas($row['IDLinea']);
            $lineaDestino->setIDPedido($idDestino);
            $lineaDestino->setIDAgente($_SESSION['USER']['user']['id']);
            $lineaDestino->setIDEstado(0);
            $lineaDestino->valida(); // Toma los precios vigentes (tarifa, promociones, etc)
            $lineaDestino->create();
        }
        unset($lineaDestino);

        return $idDestino;
    }

    /**
     * Devuelve una array con los id's de pedidos que están
     * pendientes de facturar (IDEstado=2) del proveedor $idProveedor
     * en el período $desdeFecha - $hastaFecha
     *
     * @param integer $idSucursal El id de sucursal (opcional)
     * @param integer $idProveedor El id de proveedor
     * @param string $desdeFecha Fecha en formato dd/mm/aaaa
     * @param string $hastaFecha Fecha en formato dd/mm/aaaa
     * @return array Array con los ids de pedido
     */
    public function getPendientesFacturar($idSucursal, $idProveedor, $desdeFecha, $hastaFecha) {

        $fecha = new Fecha($desdeFecha);
        $desdeFecha = $fecha->getaaaammdd();
        $fecha = new Fecha($hastaFecha);
        $hastaFecha = $fecha->getaaaammdd();
        unset($fecha);

        if ($idSucursal == '')
            $filtroSucursal = "(1)"; else
            $filtroSucursal = "(IDSucursal = '{$idSucursal}')";
        $filtro = $filtroSucursal . " and
                  (IDProveedor='{$idProveedor}') and
                  (Fecha>='{$desdeFecha}') and
                  (Fecha<='{$hastaFecha}') and
                  (IDEstado=2)";

        $pedido = new PedidosCab();
        $rows = $pedido->cargaCondicion("IDPedido", $filtro, "FechaEntrada,IDPedido ASC");
        unset($pedido);

        return $rows;
    }
}

?>
