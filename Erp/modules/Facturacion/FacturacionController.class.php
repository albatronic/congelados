<?php

/**
 * Description of FacturacionController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 08-mar-2012
 *
 */
class FacturacionController extends Controller {

    protected $entity = "Facturacion";
    protected $parentEntity = "";

    public function __construct($request) {

        $usuario = new Agentes($_SESSION['USER']['user']['id']);
        $this->values['sucursales'] = $usuario->getSucursales();

        $this->values['filtro'] = $this->request['filtro'];
        if ($this->values['filtro']['desdeFecha'] == '')
            $this->values['filtro']['desdeFecha'] = '01/01/' . date('Y');
        if ($this->values['filtro']['hastaFecha'] == '')
            $this->values['filtro']['hastaFecha'] = '31/12/' . date('Y');

        parent::__construct($request);
    }

    public function listAction() {

        $filtro = $this->request['filtro'];

        $totales = array('albaranesSeleccionados' => 0, 'facturable' => 0, 'seleccion' => 0);

        $albaran = new AlbaranesCab();
        $rows = $albaran->getPendientesFacturar($filtro['idSucursal'],$filtro['idCliente'], $filtro['desdeFecha'], $filtro['hastaFecha']);
        unset($albaran);
        foreach ($rows as $row) {
            $albaran = new AlbaranesCab($row['IDAlbaran']);
            $datos[] = $albaran;
            $totales['facturable'] += $albaran->getTotal();
            if ($albaran->getFlagFacturar()->getIDTipo()) {
                $totales['albaranesSeleccionados'] ++;
                $totales['seleccion'] += $albaran->getTotal();
            }
            unset($albaran);
        }

        $this->values['filtro'] = $this->request['filtro'];
        $this->values['listado'] = $datos;
        $this->values['totales'] = $totales;
        $this->values['cliente'] = new Clientes($filtro['idCliente']);
        $this->values['contador'] = new Contadores();
        $template = "Facturacion/list.html.twig";

        return array('template' => $template, 'values' => $this->values);
    }

    /**
     * Cambia la forma de pago el flag de facturar del albaran
     * a peticion del usuario
     *
     * @return <type>
     */
    public function cambiaAction() {

        $estado = ($this->request['sino'] == 'on') ? '1' : '0';

        $albaran = new AlbaranesCab($this->request['idAlbaran']);
        $albaran->setFlagFacturar($estado);
        $albaran->setIDFP($this->request['idFormaPago']);
        $albaran->save();
        unset($albaran);

        return $this->listAction();
    }

    /**
     * Proceso de facturacion.
     * Puede ser facturación separada: Una factura por cada albarán
     * o facturación agrupada: Una factura con todos los albaranes, con las siguientes salvedades:
     *
     * Se agrupan las facturas en base a la forma de pago, comercial y al flag "Facturación agrupada"
     * de la ficha del cliente.
     *
     * @return <type>
     */
    public function facturarAction() {

        $facturados = array();

        $filtro = $this->request['filtro'];

        $fecha = new Fecha($filtro['desdeFecha']);
        $desdeFecha = $fecha->getaaaammdd();
        $fecha = new Fecha($filtro['hastaFecha']);
        $hastaFecha = $fecha->getaaaammdd();
        unset($fecha);

        switch ($this->request['tipo']) {
            case '0':
                // Facturación individual. Se genera una factura por cada albarán
                $filter = "IDFactura='0' and IDEstado='2' and IDCliente='{$filtro['idCliente']}' and FechaEntrega>='{$desdeFecha}' and FechaEntrega<'{$hastaFecha}' and FlagFacturar='1'";

                $albaran = new AlbaranesCab();
                $rows = $albaran->cargaCondicion("IDAlbaran", $filter, "FechaEntrega ASC");
                foreach ($rows as $row) {
                    $albaran = new AlbaranesCab($row['IDAlbaran']);
                    $facturados[] = $albaran->facturar(new Contadores($this->request['idContador']), $this->request['fecha']);
                }
                unset($albaran);
                break;

            case '1':
                // Agrupada. Se agrupan los albaranes por forma de pago, comercial
                $filter = "c.IDFactura='0' and c.IDEstado='2' and c.IDCliente='{$filtro['idCliente']}' and c.FechaEntrega>='{$desdeFecha}' and c.FechaEntrega<'{$hastaFecha}' and c.FlagFacturar='1'";

                //COMPRUEBO QUE NO HAYA MAS DE TRES TIPOS DE IVA ENTRE TODOS LOS ALBARANES A FACTURAR
                $em = new EntityManager("datos" . $_SESSION['emp']);
                if (!$em->getDbLink()) {
                    $this->values['errores'] = $em->getError();
                    return $this->listAction();
                }

                $query = "select l.Iva from {$em->getDataBase()}.albaranes_lineas as l, {$em->getDataBase()}.albaranes_cab as c
                        where {$filter} and c.IDAlbaran=l.IDAlbaran
                        group by l.Iva";
                $em->query($query);
                $rows = $em->fetchResult();
                $em->desConecta();
                if (count($rows) > 3) {
                    $this->values['alertas'] = "Hay más de tres tipos de iva distintos. No se puede agrupar";
                    return $this->listAction();
                }

                $contador = new Contadores($this->request['idContador']);

                // Buscar la cuenta contable de ventas para la sucursal
                $sucursal = new Sucursales($filtro['idSucursal']);
                $ctaVentas = $sucursal->getCtaContableVentas();
                unset($sucursal);

                $cliente = new Clientes($filtro['idCliente']);
                $agruparDireccionEntrega = ($cliente->getFacturacionAgrupada()->getIDTipo() == '1');
                unset($cliente);

                $query = ($agruparDireccionEntrega) ?
                    "select c.IDFP,c.IDComercial, sum(c.Importe) as Importe, sum(Descuento) as Descuento from albaranes_cab c where $filter GROUP BY c.IDFP, c.IDComercial;" :
                    "select c.IDFP,c.IDComercial, c.IDDirec, sum(c.Importe) as Importe, sum(Descuento) as Descuento from albaranes_cab c where $filter GROUP BY c.IDFP, c.IDComercial, c.IDDirec;";
                unset($cliente);

                //AGRUPO LOS ALBARANES POR FORMA DE PAGO, COMERCIAL Y (si procede) DIRECCION DE ENTREGA.
                $em = new EntityManager("datos" . $_SESSION['emp']);
                $em->query($query);
                $rows = $em->fetchResult();
                $em->desConecta();
                foreach ($rows as $row) {
                    $numeroFactura = $contador->asignaContador();
                    $factura = new FemitidasCab();
                    $factura->setIDSucursal($filtro['idSucursal']);
                    $factura->setIDContador($this->request['idContador']);
                    $factura->setNumeroFactura($numeroFactura);
                    $factura->setIDAgente($_SESSION['USER']['user']['id']);
                    $factura->setIDComercial($row['IDComercial']);
                    $factura->setFecha($this->request['fecha']);
                    $factura->setIDCliente($filtro['idCliente']);
                    $factura->setCuentaVentas($ctaVentas);
                    $factura->setClave(md5($numeroFactura));
                    $factura->setDescuento($row['Descuento']);
                    $factura->setImporte($row['Importe']);
                    $factura->setIDFP($row['IDFP']);

                    $idFactura = $factura->create();
                    // Guardo en un array los id's de facturas generadas
                    $facturados[] = $idFactura;

                    if ($idFactura != 0) {                       
                        // Crear las lineas de factura
                        // No incluyo las lineas de albaran cuyas unidades sean 0
                        $em = new EntityManager("datos" . $_SESSION['emp']);
                        $query =  ($agruparDireccionEntrega) ?
                            "select l.* from albaranes_lineas l, albaranes_cab c where (c.IDAlbaran=l.IDAlbaran) and (c.IDFP='{$row['IDFP']}') and (l.Unidades<>0) and {$filter}" :
                            "select l.* from albaranes_lineas l, albaranes_cab c where (c.IDAlbaran=l.IDAlbaran) and (c.IDFP='{$row['IDFP']}') and (c.IDDirec='{$row['IDDirec']}') and (l.Unidades<>0) and {$filter}";
                        $em->query($query);
                        $lineas = $em->fetchResult();
                        $em->desConecta();

                        foreach ($lineas as $linea) {
                            $linFactura = new FemitidasLineas();
                            $linFactura->setIDFactura($idFactura);
                            $linFactura->setIDArticulo($linea['IDArticulo']);
                            $linFactura->setDescripcion($linea['Descripcion']);
                            $linFactura->setUnidades($linea['Unidades']);
                            $linFactura->setPrecio($linea['Precio']);
                            $linFactura->setDescuento($linea['Descuento']);
                            $linFactura->setImporte($linea['Importe']);
                            $linFactura->setImporteCosto($linea['ImporteCosto']);
                            $linFactura->setIDAlbaran($linea['IDAlbaran']);
                            $linFactura->setIDLineaAlbaran($linea['IDLinea']);
                            $linFactura->setIva($linea['Iva']);
                            $linFactura->setRecargo($linea['Recargo']);
                            $linFactura->setIDVenta($linea['IDVenta']);
                            $linFactura->setComisionAgente($linea['ComisionAgente']);
                            $linFactura->setComisionMontador($linea['ComisionMontador']);
                            $linFactura->setComisionar($linea['Comisionar']);
                            $linFactura->setIDAgente($_SESSION['USER']['user']['id']);
                            $linFactura->setIDComercial($linea['IDComercial']);
                            $linFactura->setIDPromocion($linea['IDPromocion']);
                            $linFactura->setAltoAl($linea['AltoAl']);
                            $linFactura->setAnchoAl($linea['AnchoAl']);
                            $linFactura->setMtsAl($linea['MtsAl']);
                            $linFactura->setAltoFa($linea['AltoFa']);
                            $linFactura->setAnchoFa($linea['AnchoFa']);
                            $linFactura->setMtsFa($linea['MtsFa']);

                            if ($linFactura->create()) {
                                // Pongo el estado de la linea de albaran a "Facturado"
                                $lineaAlbaran = new AlbaranesLineas($linea['IDLinea']);
                                $lineaAlbaran->setIDEstado(3);
                                $lineaAlbaran->save();
                            }
                            unset($linFactura);
                        }

                        // Totalizar la factura
                        $factura->recalcula();

                        // Crear vencimientos
                        $factura->creaVctos();

                        // Anotar en caja sin procede
                        $factura->anotaEnCaja();

                        // Actualiza las cabecera del grupo de albaranes
                        $em = new EntityManager("datos" . $_SESSION['emp']);
                        $query =  ($agruparDireccionEntrega) ?
                            "update albaranes_cab c set c.IDFactura='{$idFactura}', c.IDEstado='3' where (c.IDFP='{$row['IDFP']}') and ({$filter})" :
                            "update albaranes_cab c set c.IDFactura='{$idFactura}', c.IDEstado='3' where (c.IDFP='{$row['IDFP']}') and (c.IDDirec='{$row['IDDirec']}') and ({$filter})";
                        $em->query($query);
                        $em->desConecta();

                        unset($factura);
                    } else {
                        $this->values['errores'] = $factura->getErrores();
                    }
                }
                break;
        }


        if (($this->request['imprimir'] == 'on') and (count($facturados) > 0)) {
            $this->values['archivo'] = $this->generaPdf('FemitidasCab', $facturados);
        }

        return $this->listAction();
    }

    /**
     * Genera un array con la informacion necesaria para imprimir el documento
     * Recibe un array con los ids de factura
     *
     * @param array $idsDocumento Array con los ids de facturas
     * @return array Array con dos elementos: master es un objeto factura y detail es un array de objetos lineas de factura
     */
    protected function getDatosDocumento($idsDocumento) {

        $master = array();
        $detail = array();

        // Recorro el array de las facturas a imprimir
        foreach ($idsDocumento as $key => $idDocumento) {
            // Instancio la cabecera de la factura
            $master[$key] = new FemitidasCab($idDocumento);

            // LLeno el array con objetos de lineas de factura
            $lineas = array();
            $facturaLineas = new FemitidasLineas();
            $rows = $facturaLineas->cargaCondicion('IDLinea', "IDFactura='{$idDocumento}' and Unidades<>0", "IDLinea ASC");
            foreach ($rows as $row) {
                $lineas[] = new FemitidasLineas($row['IDLinea']);
            }
            $detail[$key] = $lineas;
        }

        $datos = array(
            'master' => $master,
            'detail' => $detail,
        );
        return $datos;
    }

}

?>
