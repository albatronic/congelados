<?php

/**
 * GENERA LOS APUNTES CONTABLES Y SUBCUENTAS PARA CONTAPLUS.
 * 
 * EN BASE A LAS FACTURAS EMITIDAS, RECIBIDAS, COBROS Y PAGOS
 * 
 * UTILIZA LA ESTRUCTURA DE SUBCUENTAS DEL RELEASE V8
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 10-may-2012
 *
 */
class EnlaceContaController extends Controller {

    protected $entity = "EnlaceConta";
    protected $parentEntity = "";
    protected $fpSubcuentas;
    protected $fpDiario;
    protected $arraySubcuentas; //Array de subcuentas
    protected $desdeFecha;
    protected $hastaFecha;
    protected $nAsiento = 0;
    protected $DIGCC;
    protected $fileDiario;
    protected $fileSubcuentas;
    protected $idTraspaso;
    protected $nEmitidas = 0;
    protected $nRecibidas = 0;
    protected $nCobros = 0;
    protected $nPagos = 0;
    protected $nSubcuentas = 0;
    protected $errores = array();

    public function __construct($request) {

        $empresa = new Empresas($_SESSION['emp']);
        $this->DIGCC = $empresa->getDigitosCuentaContable();
        unset($empresa);

        $this->idTraspaso = date('Ymd_His');
        $this->fileDiario = $_SERVER['DOCUMENT_ROOT'] . $_SESSION['appPath'] . "/docs/docs{$_SESSION['emp']}/interfaces/contaplus/{$this->idTraspaso}_diario.txt";
        $this->fileSubcuentas = $_SERVER['DOCUMENT_ROOT'] . $_SESSION['appPath'] . "/docs/docs{$_SESSION['emp']}/interfaces/contaplus/{$this->idTraspaso}_subcuentas.txt";

        parent::__construct($request);
    }

    public function indexAction() {

        $log = new LogTraspasoConta();
        $traspasos = $log->getListTraspasos();
        unset($log);

        $this->values['traspasos'] = $traspasos;
        $this->values['estadosRecibos'] = new EstadosRecibos();

        return parent::indexAction();
    }

    /**
     * Quita del registro de traspasos una entrada
     * @return
     */
    public function BorrarLogAction() {

        $idLog = $this->request['2'];
        $log = new LogTraspasoConta();
        $log->delete($idLog);
        $log->save();
        unset($log);

        return $this->indexAction();
    }

    public function TrasvaseAction() {

        // Por si el proceso tarda mucho...
        set_time_limit(0);

        $this->arraySubcuentas = array();

        $this->fpDiario = fopen($this->fileDiario, "w");

        if ($this->valida()) {
            if (isset($this->request['Emitidas']))
                $this->FacturasEmitidas($this->request['IDSucursal']);

            if (isset($this->request['Recibidas']))
                $this->FacturasRecibidas($this->request['IDSucursal']);

            if (isset($this->request['Pagos']))
                $this->Pagos($this->request['IDSucursal'], $this->request['IDEstadoPagos']);

            if (isset($this->request['Cobros']))
                $this->Cobros($this->request['IDSucursal'], $this->request['IDEstadoCobros']);

            $this->nSubcuentas = count($this->arraySubcuentas);
            if ($this->nSubcuentas)
                $this->GuardaSubcuentas();

            fclose($this->fpDiario);

            // Registrar el traspaso en el log de traspasos
            $this->RegistroLog();

            $this->values['files'] = array(
                'diario' => "docs/docs{$_SESSION['emp']}/interfaces/contaplus/" . basename($this->fileDiario),
                'subctas' => "docs/docs{$_SESSION['emp']}/interfaces/contaplus/" . basename($this->fileSubcuentas),
            );
        } else
            $this->values['errores'] = $this->errores;

        return $this->indexAction();
    }

    /**
     * Travasa las facturas emitidas
     */
    private function FacturasEmitidas($idSucursal) {

        $filtro = "";
        if ($idSucursal != "")
            $filtro = "IDSucursal='{$idSucursal}' and";

        $filtro = $filtro . " Fecha>='{$this->desdeFecha}' and Fecha<='{$this->hastaFecha}'";

        $factura = new FemitidasCab();
        $facturas = $factura->cargaCondicion("*", $filtro, "Fecha ASC");
        unset($factura);

        foreach ($facturas as $factura) {
            $this->nAsiento++;
            $this->nEmitidas++;

            $asiento = array();

            $cliente = new Clientes($factura['IDCliente']);

            $fecha = str_replace("-", "", $factura['Fecha']);

            // Apunte de Ventas
            $asiento[] = $this->ApunteVentas($this->nAsiento, $fecha, $factura, $cliente);

            // Apunte(s) de IVA
            $apuntes = $this->ApunteIvaEmitidas($this->nAsiento, $fecha, $factura, $cliente);
            foreach ($apuntes as $apunte)
                $asiento[] = $apunte;

            // Apunte de Cliente
            $asiento[] = $this->ApunteCliente($this->nAsiento, $fecha, $factura, $cliente);

            // Escribir en el fichero el asiento
            foreach ($asiento as $apunte)
                fwrite($this->fpDiario, $apunte);

            // Guardar la subcuenta del cliente
            if (!isset($this->arraySubcuentas[$factura['IDCliente']]))
                $this->arraySubcuentas[$factura['IDCliente']] = $this->SubcuentaCliente($cliente);
        }
    }

    /**
     * Trasvasa las facturas recibidas
     */
    private function FacturasRecibidas($idSucursal) {

        $filtro = "";
        if ($idSucursal != "")
            $filtro = "IDSucursal='{$idSucursal}' and";

        $filtro = $filtro . " Fecha>='{$this->desdeFecha}' and Fecha<='{$this->hastaFecha}'";

        $factura = new FrecibidasCab();
        $facturas = $factura->cargaCondicion("*", $filtro, "Fecha ASC");
        unset($factura);

        foreach ($facturas as $factura) {
            $this->nAsiento++;
            $this->nRecibidas++;

            $asiento = array();

            $proveedor = new Proveedores($factura['IDProveedor']);

            $fecha = str_replace("-", "", $factura['Fecha']);

            // Apunte de Compras
            $asiento[] = $this->ApunteCompras($this->nAsiento, $fecha, $factura, $proveedor);

            // Apunte(s) de IVA
            $apuntes = $this->ApunteIvaRecibidas($this->nAsiento, $fecha, $factura, $proveedor);
            foreach ($apuntes as $apunte)
                $asiento[] = $apunte;

            // Apunte de Proveedor
            $asiento[] = $this->ApunteProveedor($this->nAsiento, $fecha, $factura, $proveedor);

            // Escribir en el fichero el asiento
            foreach ($asiento as $apunte)
                fwrite($this->fpDiario, $apunte);

            // Guardar la subcuenta del proveedor
            if (!isset($this->arraySubcuentas[$factura['IDProveedor']]))
                $this->arraySubcuentas[$factura['IDProveedor']] = $this->SubcuentaProveedor($proveedor);
        }
    }

    /**
     * Travasa los Pagos
     */
    private function Pagos($idSucursal, $idEstado) {
        
    }

    /**
     * Trasvasa los cobros
     */
    private function Cobros($idSucursal, $idEstado) {

        $filtro = "";
        if ($idSucursal != "")
            $filtro = "IDSucursal='{$idSucursal}' and";
        if ($idEstado != "")
            $filtro .= " IDEstado='{$idEstado}' and";

        $filtro .= " Vencimiento>='{$this->desdeFecha}' and Vencimiento<='{$this->hastaFecha}'";

        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "
            SELECT IDCliente, Vencimiento, IDRemesa, CContable as CuentaPago, sum(Importe) as Importe , count(IDRecibo) as NRecibos, IDEstado
            FROM recibos_clientes
            WHERE {$filtro}
            GROUP BY IDCliente, IDRemesa, CuentaPago
            ORDER BY Vencimiento ASC, IDRemesa ASC;";
            $em->query($query);
            $recibos = $em->fetchResult();
            $em->desConecta();

            foreach ($recibos as $recibo) {
                $this->nAsiento++;
                $this->nCobros++;

                $asiento = array();

                $cliente = new Clientes($recibo['IDCliente']);

                $fecha = str_replace("-", "", $recibo['Vencimiento']);

                // Apunte de Cobro
                $asiento[] = $this->ApunteCobro($this->nAsiento, $fecha, $recibo, $cliente);

                // Apunte(s) de detalle cobros
                $apuntes = $this->ApunteDetalleCobro($this->nAsiento, $fecha, $recibo);
                foreach ($apuntes as $apunte)
                    $asiento[] = $apunte;

                // Escribir en el fichero el asiento
                foreach ($asiento as $apunte)
                    fwrite($this->fpDiario, $apunte);

                // Guardar la subcuenta del cliente
                if (!isset($this->arraySubcuentas[$recibo['IDCliente']]))
                    $this->arraySubcuentas[$recibo['IDCliente']] = $this->SubcuentaCliente($cliente);
            }
        }
        unset($em);
    }

    /**
     * Devuelve un objeto Apunte,
     * con el apunte correspondiente al cobro de un asiento de COBRO
     * 
     * @param integer $nAsiento
     * @param date $fecha
     * @param array $recibo
     * @param Clientes $cliente
     * @return ContaPlusDiario 
     */
    private function ApunteCobro($nAsiento, $fecha, array $recibo, Clientes $cliente) {

        $apunte = new ContaPlusDiarioV10($nAsiento, $fecha);

        $apunte->setSubCta($recibo['CuentaPago']);
        $apunte->setContra($cliente->getCContable());
        $apunte->setConcepto("Cob Ftra Cliente " . $cliente->getCContable());
        $apunte->setDocumento("N.Ftras " . $recibo['NRecibos']);
        $apunte->setEuroDebe($recibo['Importe']);

        return $apunte;
    }

    private function ApunteDetalleCobro($nAsiento, $fecha, array $cabecera) {

        $filtro = "IDCliente='{$cabecera['IDCliente']}' and Vencimiento='{$cabecera['Vencimiento']}' and IDRemesa='{$cabecera['IDRemesa']}' and IDEstado='{$cabecera['IDEstado']}'";

        $recibo = new RecibosClientes();
        $recibos = $recibo->cargaCondicion("*", $filtro);
        unset($recibo);

        $cliente = new Clientes($cabecera['IDCliente']);

        foreach ($recibos as $recibo) {
            
            $recibo = new RecibosClientes($recibo['IDRecibo']);
            
            $apunte = new ContaPlusDiarioV10($nAsiento, $fecha);

            $apunte->setSubCta($cliente->getCContable());
            $apunte->setContra($recibo->getCContable());
            $apunte->setConcepto("Cob Ftra Cliente " . $cliente->getRazonSocial());
            $apunte->setDocumento($recibo->getIDFactura()->getNumeroFactura());
            $apunte->setEuroHaber($recibo->getImporte());
            $apuntes[] = $apunte;
        }

        unset($apunte);
        unset($recibo);
        
        return $apuntes;
    }

    /**
     * Recibe un objeto cliente y devuelve un objeto subcuenta
     * con los datos del cliente
     *
     * @param Clientes Objeto Cliente
     * @return ContaPlusSubcta Objeto Subcuenta
     */
    private function SubcuentaCliente(Clientes $cliente) {

        $subCta = new ContaPlusSubctaV10();

        $subCta->setCodigo($cliente->getCContable());
        $subCta->setTitulo($cliente->getRazonSocial());
        $subCta->setNif($cliente->getCif());
        $subCta->setDomicilio($cliente->getDireccion());
        $subCta->setPoblacion($cliente->getPoblacion());
        $subCta->setProvincia($cliente->getIDProvincia()->getProvincia());
        $subCta->setCodPostal($cliente->getCodigoPostal());
        $subCta->setCodPais($cliente->getIDPais()->getIso2());

        return $subCta;
    }

    /**
     * Recibe un objeto proveedor y devuelve un objeto subcuenta
     * con los datos del proveedor
     *
     * @param Proveedores Objeto Proveedor
     * @return ContaPlusSubcta Objeto Subcuenta
     */
    private function SubcuentaProveedor(Proveedores $proveedor) {

        $subCta = new ContaPlusSubctaV10();

        $subCta->setCodigo($proveedor->getCContable());
        $subCta->setTitulo($proveedor->getRazonSocial());
        $subCta->setNif($proveedor->getCif());
        $subCta->setDomicilio($proveedor->getDireccion());
        $subCta->setPoblacion($proveedor->getPoblacion());
        $subCta->setProvincia($proveedor->getIDProvincia()->getProvincia());
        $subCta->setCodPostal($proveedor->getCodigoPostal());
        $subCta->setCodPais($proveedor->getIDPais()->getIso2());

        return $subCta;
    }

    /**
     * Crea el archivo con las subcuentas involucradas en el traspaso
     * 
     * @return boolean TRUE si se creó con éxito
     */
    private function GuardaSubcuentas() {

        $ok = true;

        $this->fpSubcuentas = fopen($this->fileSubcuentas, "w");

        if ($this->fpSubcuentas) {
            foreach ($this->arraySubcuentas as $subcuenta)
                fwrite($this->fpSubcuentas, $subcuenta);
            fclose($this->fpSubcuentas);
        } else
            $ok = false;

        return $ok;
    }

    /**
     * Devuelve un objeto Apunte,
     * con el apunte correspondiente a la venta de un asiento de VENTAS
     * 
     * @param integer $nAsiento
     * @param date $fecha
     * @param array $factura
     * @param Clientes $cliente
     * @return ContaPlusDiario 
     */
    private function ApunteVentas($nAsiento, $fecha, array $factura, Clientes $cliente) {

        $apunte = new ContaPlusDiarioV10($nAsiento, $fecha);

        $apunte->setSubCta($factura['CuentaVentas']);
        $apunte->setContra($cliente->getCContable());
        $apunte->setConcepto("Ntra. Factura " . $factura['NumeroFactura']);
        $apunte->setDocumento($factura['NumeroFactura']);
        $apunte->setEuroHaber($factura['TotalBases']);
        $apunte->setL340('F');

        return $apunte;
    }

    /**
     * Devuelve un objeto Apunte,
     * con el apunte correspondiente al cliente de un asiento de VENTAS
     *
     * @param integer $nAsiento
     * @param date $fecha
     * @param array $factura
     * @param Clientes $cliente
     * @return ContaPlusDiario
     */
    private function ApunteCliente($nAsiento, $fecha, array $factura, Clientes $cliente) {

        $apunte = new ContaPlusDiarioV10($nAsiento, $fecha);

        $apunte->setSubCta($cliente->getCContable());
        $apunte->setContra($factura['CuentaVentas']);
        $apunte->setConcepto("Ntra. Factura " . $factura['NumeroFactura']);
        $apunte->setDocumento($factura['NumeroFactura']);
        $apunte->setEuroDebe($factura['Total']);
        $apunte->setL340('F');

        return $apunte;
    }

    /**
     * Devuelve un array de objetos Apunte,
     * con los apuntes de iva y recargo de equivalencia
     *
     * @param integer $nAsiento
     * @param date $fecha
     * @param array $factura
     * @param Clientes $cliente
     * @return ContaPlusDiario
     */
    private function ApunteIvaEmitidas($nAsiento, $fecha, array $factura, Clientes $cliente) {

        $apuntes = array();

        $j = 0;
        while ($j < 3) {
            $j++;
            if ($factura['BaseImponible' . $j] <> 0) {

                if ($factura['TotalRecargo'] <> 0) {
                    $sufijo = $this->SufijoRecargo($factura['Iva' . $j], $factura['Recargo' . $j]);
                } else {
                    $sufijo = $this->SufijoIva($factura['Iva' . $j]);
                }

                $subcuenta = str_pad('477', $this->DIGCC - 4, '0') . $sufijo;
                $apunte = new ContaPlusDiarioV10($nAsiento, $fecha);
                $apunte->setSubCta($subcuenta);
                $apunte->setContra($cliente->getCContable());
                $apunte->setConcepto("Ntra. Factura " . $factura['NumeroFactura']);
                $apunte->setFactura(substr($factura['NumeroFactura'], 1, strlen($factura['NumeroFactura']) - 1));
                $apunte->setSerie(substr($factura['NumeroFactura'], 0, 1));
                $apunte->setIVA($factura['Iva' . $j]);
                $apunte->setRecequiv($factura['Recargo' . $j]);
                $apunte->setDocumento($factura['NumeroFactura']);
                $apunte->setEuroHaber($factura['CuotaIva' . $j]);
                $apunte->setBaseEuro($factura['BaseImponible' . $j]);
                $apunte->setFecha_OP($fecha);
                $apunte->setFecha_EX($fecha);
                $apunte->setTerIdNif('1');
                $apunte->setTerNif($cliente->getCif());
                $apunte->setTerNom($cliente->getRazonSocial());
                $apunte->setTipoFac('E');
                $apunte->setTipoIVA('G');
                $apunte->setL340('T');
                $apuntes[] = $apunte;

                if ($factura['CuotaRecargo' . $j] <> 0) {
                    $subcuenta = str_pad('475', $this->DIGCC - 4, '0') . $sufijo;
                    $apunte = new ContaPlusDiarioV10($nAsiento, $fecha);
                    $apunte->setSubCta($subcuenta);
                    $apunte->setContra($cliente->getCContable());
                    $apunte->setConcepto("Ntra. Factura " . $factura['NumeroFactura']);
                    $apunte->setFactura(substr($factura['NumeroFactura'], 1, strlen($factura['NumeroFactura']) - 1));
                    $apunte->setSerie(substr($factura['NumeroFactura'], 0, 1));
                    $apunte->setRecequiv($factura['Recargo' . $j]);
                    $apunte->setDocumento($factura['NumeroFactura']);
                    $apunte->setEuroHaber($factura['CuotaRecargo' . $j]);
                    $apunte->setBaseEuro($factura['BaseImponible' . $j]);
                    $apunte->setFecha_OP($fecha);
                    $apunte->setFecha_EX($fecha);
                    $apunte->setTerIdNif('1');
                    $apunte->setTerNif($cliente->getCif());
                    $apunte->setTerNom($cliente->getRazonSocial());
                    $apunte->setTipoFac('E');
                    $apunte->setTipoIVA('G');
                    $apunte->setL340('T');
                    $apuntes[] = $apunte;
                }
            }
        }

        return $apuntes;
    }

    /**
     * Devuelve un objeto Apunte,
     * con el apunte correspondiente a la compra de un asiento de COMPRAS
     *
     * @param integer $nAsiento
     * @param date $fecha
     * @param array $factura
     * @param Proveedores $proveedor
     * @return ContaPlusDiario
     */
    private function ApunteCompras($nAsiento, $fecha, array $factura, Proveedores $proveedor) {

        $apunte = new ContaPlusDiarioV10($nAsiento, $fecha);

        $apunte->setSubCta($factura['CuentaCompras']);
        $apunte->setContra($proveedor->getCContable());
        $apunte->setConcepto("COMPRA DE MERCADERIAS");
        $apunte->setDocumento($factura['SuFactura']);
        $apunte->setEuroDebe($factura['TotalBases']);

        return $apunte;
    }

    /**
     * Devuelve un objeto Apunte,
     * con el apunte correspondiente al proveedor de un asiento de COMPRAS
     *
     * @param integer $nAsiento
     * @param date $fecha
     * @param array $factura
     * @param Proveedores $proveedor
     * @return ContaPlusDiario
     */
    private function ApunteProveedor($nAsiento, $fecha, array $factura, Proveedores $proveedor) {

        $apunte = new ContaPlusDiarioV10($nAsiento, $fecha);

        $apunte->setSubCta($proveedor->getCContable());
        $apunte->setContra($factura['CuentaCompras']);
        $apunte->setConcepto("COMPRA DE MERCADERIAS");
        $apunte->setDocumento($factura['SuFactura']);
        $apunte->setEuroHaber($factura['Total']);

        return $apunte;
    }

    /**
     * Devuelve un array de objetos Apunte,
     * con los apuntes de iva y recargo de equivalencia de COMPRAS
     *
     * @param integer $nAsiento
     * @param date $fecha
     * @param array $factura
     * @param Proveedores $proveedor
     * @return ContaPlusDiario
     */
    private function ApunteIvaRecibidas($nAsiento, $fecha, array $factura, Proveedores $proveedor) {

        $apuntes = array();

        $j = 0;
        while ($j < 3) {
            $j++;
            if ($factura['BaseImponible' . $j] <> 0) {

                if ($factura['TotalRecargo'] <> 0) {
                    $sufijo = $this->SufijoRecargo($factura['Iva' . $j], $factura['Recargo' . $j]);
                } else {
                    $sufijo = $this->SufijoIva($factura['Iva' . $j]);
                }

                $subcuenta = str_pad('472', $this->DIGCC - 4, '0') . $sufijo;

                $apunte = new ContaPlusDiarioV10($nAsiento, $fecha);
                $apunte->setSubCta($subcuenta);
                $apunte->setContra($proveedor->getCContable());
                $apunte->setConcepto("COMPRA DE MERCADERIAS");
                $apunte->setFactura($factura['SuFactura']);
                $apunte->setIVA($factura['Iva' . $j]);
                $apunte->setDocumento($factura['SuFactura']);
                $apunte->setEuroDebe($factura['CuotaIva' . $j]);
                $apunte->setBaseEuro($factura['BaseImponible' . $j]);
                $apunte->setFecha_OP($fecha);
                $apunte->setFecha_EX($fecha);
                $apunte->setTerIdNif('1');
                $apunte->setTerNif($proveedor->getCif());
                $apunte->setTerNom($proveedor->getRazonSocial());
                $apunte->setTipoFac('R');
                $apunte->setTipoIVA('O');
                $apuntes[] = $apunte;

                if ($factura['CuotaRecargo' . $j] <> 0) {
                    $sufijo = $this->SufijoRecargo($factura['Recargo' . $j], $factura['Iva' . $j]);
                    $subcuenta = str_pad('472', $this->DIGCC - 4, '0') . $sufijo;

                    $apunte = new ContaPlusDiarioV10($nAsiento, $fecha);
                    $apunte->setSubCta($subcuenta);
                    $apunte->setContra($proveedor->getCContable());
                    $apunte->setConcepto("Su Factura " . $factura['SuFactura']);
                    $apunte->setFactura($factura['SuFactura']);
                    $apunte->setRecequiv($factura['Recargo' . $j]);
                    $apunte->setDocumento($factura['NumeroFactura']);
                    $apunte->setEuroDebe($factura['CuotaRecargo' . $j]);
                    $apunte->setBaseEuro($factura['BaseImponible' . $j]);
                    $apunte->setFecha_OP($fecha);
                    $apunte->setFecha_EX($fecha);
                    $apunte->setTerIdNif('1');
                    $apunte->setTerNif($proveedor->getCif());
                    $apunte->setTerNom($proveedor->getRazonSocial());
                    $apunte->setTipoFac('R');
                    $apunte->setTipoIVA('O');
                    $apuntes[] = $apunte;
                }
            }
        }

        return $apuntes;
    }

    /**
     * Rellena un string con ceros por la izquierda
     * 
     * @param string $iva El string a rellenar
     * @return string String relleno de ceros por la izquierda
     */
    private function SufijoIva($texto) {

        // Relleno con ceros por la izquierda y quito el punto decimal
        $s = str_pad($texto, 5, "0", STR_PAD_LEFT);
        $s = "00" . substr($s, 0, 2);

        return($s);
    }

    /**
     * Rellena el string $s con ceros por la izquierda
     */
    private function SufijoRecargo($iva, $recargo) {

        // Relleno con ceros por la izquierda
        $iva = str_pad($iva, 5, "0", STR_PAD_LEFT);

        // Tomo los dos dígitos a la izquierda del punto decimal
        $izq = substr($iva, 0, 2);

        // Tomo los dos dígitos a la derecha del punto decimal
        $dch = substr($iva, 3, 2);

        if ($izq != "00")
            $iva = $izq;
        else
            $iva = $dch;

        // Relleno con ceros por la izquierda
        $recargo = str_pad($recargo, 5, "0", STR_PAD_LEFT);

        // Tomo los dos dígitos a la izquierda del punto decimal
        $izq = substr($recargo, 0, 2);

        // Tomo los dos dígitos a la derecha del punto decimal
        $dch = substr($recargo, 3, 2);

        if ($izq != "00")
            $recargo = $izq;
        else
            $recargo = "0" . substr($dch, 0, 1);

        return $recargo . $iva;
    }

    /**
     * Registrar el traspaso efectuado en el registro log de traspasos
     */
    private function RegistroLog() {

        $log = new LogTraspasoConta();
        $log->setIDTraspaso($this->idTraspaso);
        $log->setDia(date('d-m-Y'));
        $log->setHora(date('H:i:s'));
        $log->setIDUsuario($_SESSION['USER']['user']['id']);
        $log->setIDSucursal($this->request['IDSucursal']);
        $log->setDesdeFecha($this->request['DesdeFecha']);
        $log->setHastaFecha($this->request['HastaFecha']);
        $log->setArchivoDiario("docs/docs{$_SESSION['emp']}/interfaces/contaplus/" . basename($this->fileDiario));
        $log->setArchivoSubcuentas("docs/docs{$_SESSION['emp']}/interfaces/contaplus/" . basename($this->fileSubcuentas));
        $log->setEmitidas($this->nEmitidas);
        $log->setRecibidas($this->nRecibidas);
        $log->setCobros($this->nCobros);
        $log->setPagos($this->nPagos);
        $log->setAsientos($this->nAsiento);
        $log->setSubcuentas($this->nSubcuentas);
        $log->add();
        $log->save();
        unset($log);
    }

    /**
     * Realiza validaciones antes del traspaso
     * 
     * @return boolean TRUE se todo es correcto
     */
    private function valida() {

        if (trim($this->request['DesdeFecha']) == '')
            $this->errores[] = "Debe indicar una fecha de inicio";
        if (trim($this->request['HastaFecha']) == '')
            $this->errores[] = "Debe indicar una fecha de fin";
        if (count($this->errores) == 0) {
            $fecha = new Fecha($this->request['DesdeFecha']);
            $this->desdeFecha = $fecha->getaaaammdd();
            $fecha = new Fecha($this->request['HastaFecha']);
            $this->hastaFecha = $fecha->getaaaammdd();
            unset($fecha);
            if ($this->desdeFecha > $this->hastaFecha)
                $this->errores[] = "La fecha de fin debe ser igual o superior a la de inicio";
        }

        return (count($this->errores) == 0);
    }

}

?>
