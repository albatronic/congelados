<?php

/**
 * CONTROLLER FOR CajaArqueos
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 15.04.2012 00:47:17

 * Extiende a la clase controller
 */
class CajaArqueosController extends Controller {

    protected $entity = "CajaArqueos";
    protected $parentEntity = "";
    protected $errorTraspaso = array();

    public function __construct($request) {

        if ($request['filter']['valuesSelected']['0'] == '')
            $idSucursal = $_SESSION['suc'];
        else
            $idSucursal = $request['filter']['valuesSelected']['0'];

        $tpv = new Tpvs();
        $tpvs = $tpv->fetchAll($idSucursal);
        $tpvs[] = array('Id' => '%', 'Value' => '** Todos **');
        $this->values['tpvs'] = $tpvs;
        unset($tpv);

        // Sucursales destino para el traspaso
        $sucursal = new Sucursales();
        $sucursales = $sucursal->fetchAll($_SESSION['emp']);
        unset($sucursal);
        $this->values['sucursalesDestino'] = $sucursales;

        // Cerrar las eventuales cajas abiertas de dias anterios
        $arqueo = new CajaArqueos();
        $arqueosAbiertos = $arqueo->getArqueosAbiertos('%','%');

        foreach ($arqueosAbiertos as $arqueoAbierto) {
            $arqueo = new CajaArqueos($arqueoAbierto['IDArqueo']);
            if ($arqueo->cierra())
                $this->values['alertas'][] = "Se ha cerrado el arquero '{$arqueo->getIDTpv()->getNombre()}' del día {$arqueo->getDia()}";
        }
        unset($arqueo);

        parent::__construct($request);
    }

    /**
     * Cerrar la caja
     */
    public function CerrarAction() {

        if ($this->request["METHOD"] == "POST") {
            if ($this->values['permisos']['A']) {
                $datos = new CajaArqueos($this->request[$this->entity][$this->form->getPrimaryKey()]);
                $datos->cierra();
                $this->values['errores'] = $datos->getErrores();
                $this->values['alertas'] = $datos->getAlertas();
                $this->values['datos'] = $datos;
                return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
            } else {
                return array('template' => '_global/forbiden.html.twig');
            }
        } else
            return array('template' => $this->entity . '/index.html.twig');
    }

    /**
     * Realiza un traspaso de importes entre la caja actual y la de destino
     *
     * Las cajas pueden ser de distintas sucursales
     *
     * Realiza las siguientes validaciones antes de traspasar:
     *
     *   - Se haya indicado tpv destino y que sea distinto al de origen
     *   - Que los importes a traspasar no superen la cantidad origen
     *   - Que el arqueo del tpv origen no esté cerrado
     *   - Que el arqueo del tpv destino para la fecha actual no esté cerrado
     *
     */
    public function TraspasarAction() {

        if ($this->validaTraspaso()) {

            $arqueoOrigen = new CajaArqueos($this->request['CajaArqueos']['IDArqueo']);

            // Comprobar que la caja destino esté abierta, si no, abrirla
            $arqueoDestino = new CajaArqueos();
            $arqueoDestino->setIDSucursal($this->request['IDSucursalDestino']);
            $arqueoDestino->setIDTpv($this->request['IDTpvDestino']);
            $arqueoDestino->setDia(date('Y-m-d'));
            $idArqueoDestino = $arqueoDestino->estaAbierta();

            if (!$idArqueoDestino)
                $idArqueoDestino = $arqueoDestino->apertura();

            foreach ($this->request['traspaso'] as $idFormaPago => $importes)
                if ($importes['importeDestino'] > 0) {
                    // Sacar de la caja origen
                    $apunteSalida = new CajaLineas();
                    $apunteSalida->setIDArqueo($this->request['CajaArqueos']['IDArqueo']);
                    $apunteSalida->setFecha(date('Y-m-d H:i:s'));
                    $apunteSalida->setConcepto("Salida por Traspaso a " . $arqueoDestino->getIDSucursal() . " " . $arqueoDestino->getIDTpv());
                    $apunteSalida->setIDFP($idFormaPago);
                    $apunteSalida->setOrigen('6'); // Traspasos
                    $apunteSalida->setImporte(-1 * $importes['importeDestino']);
                    $apunteSalida->setIDAgente($_SESSION['USER']['user']['id']);
                    $apunteSalida->create();
                    $this->_errores = $apunteSalida->getErrores();

                    // Meter en la caja destino
                    $apunteEntrada = new CajaLineas();
                    $apunteEntrada->setIDArqueo($idArqueoDestino);
                    $apunteEntrada->setFecha(date('Y-m-d H:i:s'));
                    $apunteEntrada->setConcepto("Entrada por Traspaso de " . $arqueoOrigen->getIDSucursal() . " " . $arqueoOrigen->getIDTpv());
                    $apunteEntrada->setIDFP($idFormaPago);
                    $apunteEntrada->setOrigen('6'); // Traspasos
                    $apunteEntrada->setImporte($importes['importeDestino']);
                    $apunteEntrada->setIDAgente($_SESSION['USER']['user']['id']);
                    $apunteEntrada->create();
                    $this->_errores = $apunteEntrada->getErrores();
                }
        } else {

            $this->values['errores'] = $this->errorTraspaso;
        }

        unset($arqueoDestino);
        unset($arqueoOrigen);
        unset($apunteEntrada);
        unset($apunteSalida);

        $datos = new CajaArqueos($this->request[$this->entity]['IDArqueo']);
        $this->values['datos'] = $datos;
        unset($datos);

        return array('template' => $this->entity . "/edit.html.twig", 'values' => $this->values);
    }

    /**
     * Devuelve un template con los movimientos ocurridos en cualquier caja
     * y periodo de fechas respecto a la entidad $entidad de valor $idEntidad
     *
     * @param string $entidad La entidad
     * @param integer $idEntidad El id de entidad
     * @return array Array template, values
     */
    public function listMvtosEntidadAction($entidad='', $idEntidad='') {

        if ($entidad == '')
            $entidad = $this->request[2];
        if ($idEntidad == '')
            $idEntidad = $this->request[3];


        $caja = new CajaLineas();
        $rows = $caja->cargaCondicion("IDApunte", "Entidad='{$entidad}' AND IDEntidad='{$idEntidad}'", "Fecha,IDApunte DESC");
        unset($caja);

        foreach ($rows as $row)
            $apuntes[] = new CajaLineas($row['IDApunte']);

        $this->values['listado'] = $apuntes;

        return array('template' => $this->entity . "/mvtosEntidad.html.twig", 'values' => $this->values);
    }

    /**
     * Genera un array con la informacion necesaria para imprimir el documento
     * Recibe un array con los ids de arqueos de caja
     *
     * @param array $idsDocumento Array con los ids de arqueos
     * @return array Array con dos elementos: master es un objeto CajaArqueos y detail es un array de objetos lineas de arqueos
     */
    protected function getDatosDocumento(array $idsDocumento) {

        $master = array();
        $detail = array();

        // Recorro el array de los albaranes a imprimir
        foreach ($idsDocumento as $key => $idDocumento) {
            // Instancio la cabecera del albaran
            $master[$key] = new CajaArqueos($idDocumento);

            // LLeno el array con objetos de lineas de arqueo
            $lineas = array();
            $cajaLineas = new CajaLineas();
            $rows = $cajaLineas->cargaCondicion('IDApunte', "IDArqueo='{$idDocumento}'", "IDAgente,IDApunte ASC");
            foreach ($rows as $row) {
                $lineas[] = new CajaLineas($row['IDApunte']);
            }
            $detail[$key] = $lineas;
        }

        return array(
            'master' => $master,
            'detail' => $detail,
        );
    }

    /**
     * Realiza las siguientes validaciones antes de traspasar:
     *
     *   - Se haya indicado tpv destino y que sea distinto al de origen
     *   - Que los importes a traspasar no superen la cantidad origen
     *   - Que el arqueo del tpv origen no esté cerrado
     *   - Que el arqueo del tpv destino para la fecha actual no esté cerrado
     *
     *
     * @return boolean
     */
    private function validaTraspaso() {


        if ($this->request['IDTpvDestino'] == '')
            $this->errorTraspaso[] = "Debe indicar un TPV destino";

        if ($this->request['IDTpvDestino'] == $this->request['IDTpvOrigen'])
            $this->errorTraspaso[] = "El TPV destino no puede ser igual al de origen";

        foreach ($this->request['traspaso'] as $value)
            if ($value['importeDestino'] > $value['importeOrigen'])
                $this->errorTraspaso[] = "El importe a traspasar supera el importe origen";


        // Comprobar que la caja origen no esté cerrada
        $arqueoOrigen = new CajaArqueos($this->request['IDArqueo']);
        if ($arqueoOrigen->estaCerrada())
            $this->errorTraspaso[] = "La caja de origen está cerrada. No se puede traspasar";

        // Comprobar que la caja destino no esté cerrada
        $arqueoDestino = new CajaArqueos();
        $arqueoDestino->setIDSucursal($this->request['IDSucursalDestino']);
        $arqueoDestino->setIDTpv($this->request['IDTpvDestino']);
        $arqueoDestino->setDia(date('Y-m-d'));
        if ($arqueoDestino->estaCerrada())
            $this->errorTraspaso[] = "La caja de destino está cerrada. No se puede traspasar";

        return (count($this->errorTraspaso) == 0);
    }

}

?>