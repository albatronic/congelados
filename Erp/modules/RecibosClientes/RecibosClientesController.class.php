<?php

/**
 * CONTROLLER FOR RecibosClientes
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 07.06.2011 00:45:18

 * Extiende a la clase controller
 */
class RecibosClientesController extends Controller {

    protected $entity = "RecibosClientes";
    protected $parentEntity = "";

    public function __construct($request) {

        $formasPago = new FormasPago();
        $this->values['formasPago'] = $formasPago;

        $acceso = new ControlAcceso('CajaArqueos');
        $this->values['permisosCajas'] = $acceso->getPermisos();
        unset($acceso);

        parent::__construct($request);
    }

    /**
     * Genera un listado en formato PDF en base a los parametros obtenidos
     * del fichero listados.yml de cada controlador y los datos filtrados
     * segun el request
     * @return array Template y valores
     */
    public function listadoAction1($aditionalFilter = '') {

        $dataBase = $this->form->getDataBaseName();

        $idComercial = $this->request['filter']['valuesSelected']['5'];
        
        if ($this->values['permisos']['L']) {
            // Lee la configuracion del listado
            $formato = new Form($this->entity, 'listados.yml');
            $parametros = $formato->getFormatoListado($this->request['formatoListado']);
            unset($formato);

            if ($idComercial) {
                $this->listado->makeQuery($aditionalFilter);
                $this->listado->arrayQuery['FROM'] = str_replace(", {$dataBase}.clientes", "", $this->listado->arrayQuery['FROM']);
                $this->listado->arrayQuery['FROM']  .= ", {$dataBase}.clientes";
                $this->listado->arrayQuery['WHERE'] .= "AND {$dataBase}.recibos_clientes.IDCliente={$dataBase}.clientes.IDCliente ";
                $this->listado->arrayQuery['WHERE'] .= "AND {$dataBase}.clientes.IDComercial='{$idComercial}'";
                $this->listado->buildQuery();
            }

            $this->values['archivo'] = $this->listado->getPdf($parametros, $aditionalFilter);
            $this->values['filtroRemesa'] = ($this->values['listado']['filter']['valuesSelected'][11]);            
            $template = '_global/listadoPdf.html.twig';
        } else {
            $template = "_global/forbiden.html.twig";
        }

        return array('template' => $template, 'values' => $this->values);
    }
    /**
     * Genera una listado por pantalla en base al filtro.
     * Puede recibir un filtro adicional
     *
     * @param string $aditionalFilter
     * @return array con el template y valores a renderizar
     */
    public function listAction1($aditionalFilter = '') {

        $dataBase = $this->form->getDataBaseName();

        $idComercial = $this->request['filter']['valuesSelected']['5'];

        if ($this->values['permisos']['C']) {

            if ($idComercial) {
                $this->listado->makeQuery($aditionalFilter);
                $this->listado->arrayQuery['FROM'] = str_replace(", {$dataBase}.clientes", "", $this->listado->arrayQuery['FROM']);
                $this->listado->arrayQuery['FROM']  .= ", {$dataBase}.clientes";
                $this->listado->arrayQuery['WHERE'] .= "AND {$dataBase}.recibos_clientes.IDCliente={$dataBase}.clientes.IDCliente ";
                $this->listado->arrayQuery['WHERE'] .= "AND {$dataBase}.clientes.IDComercial='{$idComercial}'";
                $this->listado->buildQuery();
            }

            $this->values['listado'] = $this->listado->getAll($aditionalFilter);
            $this->values['filtroRemesa'] = ($this->values['listado']['filter']['valuesSelected'][11]);
            $template = $this->entity . '/list.html.twig';
        } else {
            $template = "_global/forbiden.html.twig";
        }

        return array('template' => $template, 'values' => $this->values);
    }

    /**
     * Guarda todos los recibos que le vienen en el request y
     * comprueba el cuadre entre el importe total de todos los
     * recibos de cada factura y el importe total de la factura
     *
     * @return array
     */
    public function guardarAction() {

        if ($this->values['permisos']['A']) {
            $arrayFacturas = array();

            foreach ($this->request['RecibosClientes'] as $recibo) {
                $objeto = new RecibosClientes($recibo['IDRecibo']);
                $objeto->setVencimiento($recibo['Vencimiento']);
                $objeto->setImporte($recibo['Importe']);
                $objeto->setCBanco($recibo['CBanco']);
                $objeto->setConcepto($recibo['Concepto']);
                $objeto->setIDRemesa($recibo['IDRemesa']);
                $objeto->setIDEstado($recibo['IDEstado']);
                $objeto->save();

                // Guardo temporalmente todas las facturas afectadas
                $arrayFacturas[] = $objeto->getIDFactura()->getIDFactura();
            }
            unset($objeto);

            // Comprobar cuadre recibos-factura
            $arrayFacturas = array_unique($arrayFacturas);
            foreach ($arrayFacturas as $idFactura) {
                $factura = new FemitidasCab($idFactura);
                $totalFactura = $factura->getTotal();
                $sumaRecibos = $factura->getSumaRecibos();
                if ($totalFactura != $sumaRecibos)
                    $this->values['errores'][] = "Descuadre en factura " . $factura->getNumeroFactura() . " -> Total Factura: " . $totalFactura . " Suma Recibos " . $sumaRecibos;
            }
            unset($factura);

            return $this->listAction();
        }
        else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Genera un recibo en base a otro.
     * Se utiliza para fraccionar un recibo cuando se
     * producen pagos parciales del mismo
     *
     * @return array
     */
    public function DesdoblarAction() {

        if ($this->values['permisos']['I']) {
            if ($this->request['idReciboDesdoblar']) {
                $recibo = new RecibosClientes($this->request['idReciboDesdoblar']);
                $reciboNuevo = $recibo;
                $reciboNuevo->setIDRecibo('');
                $reciboNuevo->setRecibo('9999');
                $reciboNuevo->create();
                unset($recibo);
                unset($reciboNuevo);
            }
            return $this->listAction();
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Cambia de estado al conjunto de recibos recibidos
     *
     * Actualiza su estado, la fecha de vencimiento y la cuenta contable
     * Además genera apunte de caja si procede
     *
     * @return array
     */
    public function CobrarAction() {
        if ($this->values['permisos']['A']) {

            $formaPago = new FormasPago($this->request['idFP']);
            $anotarEnCaja = ($formaPago->getAnotarEnCaja()->getIDTipo() == '1');

            $caja = new CajaArqueos();

            foreach ($this->request['RecibosClientes'] as $recibo) {
                $objeto = new RecibosClientes($recibo['IDRecibo']);
                $objeto->setVencimiento($this->request['fechaCobro']);
                $objeto->setIDEstado($formaPago->getEstadoRecibo()->getIDTipo());
                $objeto->setCContable($formaPago->getCContable());
                if (($objeto->save()) and ($anotarEnCaja)) {
                    $caja->anotaEnCaja($objeto, $this->request['idFP']);
                }
            }
            unset($objeto);
            unset($formaPago);
            unset($caja);

            return $this->listAction();
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

}

?>