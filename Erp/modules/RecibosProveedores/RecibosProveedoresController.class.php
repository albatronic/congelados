<?php

/**
 * CONTROLLER FOR RecibosProveedores
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 18.03.2012 00:45:18

 * Extiende a la clase controller
 */
class RecibosProveedoresController extends Controller {

    protected $entity = "RecibosProveedores";
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
     * Genera una listado por pantalla en base al filtro.
     * Puede recibir un filtro adicional
     *
     * @param string $aditionalFilter
     * @return array con el template y valores a renderizar
     */
    public function listAction($aditionalFilter = '') {

        if ($this->values['permisos']['C']) {
            $this->values['listado'] = $this->listado->getAll($aditionalFilter);
            $this->values['filtroRemesa'] = ($this->values['listado']['filter']['valuesSelected'][7]);
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

            foreach ($this->request['RecibosProveedores'] as $recibo) {
                $objeto = new RecibosProveedores($recibo['IDRecibo']);
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
                $factura = new FrecibidasCab($idFactura);
                $totalFactura = $factura->getTotal();
                $sumaRecibos = $factura->getSumaRecibos();
                if ($totalFactura != $sumaRecibos)
                    $this->values['errores'][] = "Descuadre en factura " . $factura->getNumeroFactura() . " -> Total Factura: " . $totalFactura . " Suma Recibos " . $sumaRecibos;
            }
            unset($factura);
            return $this->listAction();
        } else {
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
                $recibo = new RecibosProveedores($this->request['idReciboDesdoblar']);
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
     * Actualiza su estado y la fecha de vencimiento
     * Además genera apunte de caja si procede
     * 
     * @return array
     */
    public function PagarAction() {
        if ($this->values['permisos']['A']) {

            $formaPago = new FormasPago($this->request['idFP']);
            $anotarEnCaja = ($formaPago->getAnotarEnCaja()->getIDTipo() == '1');

            $caja = new CajaArqueos();

            foreach ($this->request['RecibosProveedores'] as $recibo) {
                $objeto = new RecibosProveedores($recibo['IDRecibo']);
                $objeto->setVencimiento($this->request['fechaPago']);
                $objeto->setCContable($formaPago->getCContable());                
                $objeto->setIDEstado($formaPago->getEstadoRecibo()->getIDTipo());
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