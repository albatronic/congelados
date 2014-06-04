<?php

/**
 * CONTROLLER FOR ExpedirCab
 *
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 26.07.2011 00:45:13

 * Extiende a la clase controller
 */
class ExpedirCabController extends Controller {

    protected $entity = "ExpedirCab";
    protected $parentEntity = "";

    public function __construct($request) {

        $dia = new DiasSemana();
        $this->values['dias'] = $dia->fetchAll();
        unset($dia);

        // Orígenes de expedición dependiendo del rol
        $usuario = new Agentes($_SESSION['USER']['user']['id']);
        if ($usuario->getEsRepartidor()) {
            $this->values['tipos'] = array(
                array('Id' => 'AlbaranesCab', 'Value' => 'Albaranes de Venta'),
            );
        } else {
            $this->values['tipos'] = array(
                array('Id' => 'AlbaranesCab', 'Value' => 'Albaranes de Venta'),
                array('Id' => 'ManufacCab', 'Value' => 'Elaboraciones'),
                array('Id' => 'TraspasosCab', 'Value' => 'Traspasos'),
            );
        }
        unset($usuario);

        // Periodos de expedicion
        $this->values['periodos'] = array(
            array('Id' => '7', 'Value' => 'Semanal'),
            array('Id' => '15', 'Value' => 'Quincenal'),
            array('Id' => '31', 'Value' => 'Mensual'),
        );

        // Estados
        $this->values['estados'] = array(
            array('Id' => '1', 'Value' => 'Confirmado'),
            array('Id' => '2', 'Value' => 'Expedido'),
        );

        parent::__construct($request);
    }

    /**
     * Generar un listado con los objetos origen de expedición no expedidos
     *
     * Si el origen es albarán, la expedición se hace en base a la ruta de
     * reparto del repartidor y dia indicado y el listado se genera en orden
     * inverso al de la ruta para facilitar la descarga de la mercancia.
     *
     * @param integer $idAlmacen El id de almacen
     * @param string $idTipo El tipo de entidad
     * @return array
     */
    public function listAction($idAlmacen = "", $idTipo = "") {

        if ($this->values['permisos']['C']) {

            if ($idAlmacen != "")
                $this->request['idAlmacen'] = $idAlmacen;
            if ($idTipo != "")
                $this->request['idTipo'] = $idTipo;

            switch ($this->request['idTipo']) {
                case 'AlbaranesCab':
                    $this->values['idAlmacen'] = $this->request['idAlmacen'];
                    $template = $this->entity . '/listAlbaranes.html.twig';
                    break;
                case 'ManufacCab':
                    $this->values['listado']['data'] = $this->getElaboraciones();
                    $template = $this->entity . '/listManufac.html.twig';
                    break;
                case 'TraspasosCab':
                    $this->values['listado']['data'] = $this->getTraspasos();
                    $template = $this->entity . '/listTraspasos.html.twig';
                    break;
            }

            $this->values['listado']['filter'] = array(
                'idAlmacen' => $this->request['idAlmacen'],
                'idTipo' => $this->request['idTipo'],
                'idPeriodo' => $this->request['idPeriodo'],
                'idEstado' => $this->request['idEstado'],
            );
        } else {
            $template = "_global/forbiden.html.twig";
        }

        return array('template' => $template, 'values' => $this->values);
    }

    /**
     * Cambia el repartidor asociado al albarán
     *
     * @return void
     */
    public function CambiaRepartidorAction() {

        if ($this->values['permisos']['A']) {

            switch ($this->request['idTipo']) {
                case 'AlbaranesCab':
                    if ($this->request['idRepartidor']) {
                        $datos = new AlbaranesCab($this->request['idAlbaran']);
                        $datos->setIDRepartidor($this->request['idRepartidor']);
                        $datos->save();
                    }
                    break;
            }
        }

        return $this->listAlbaranesAction($this->request['idAlmacen'], $this->request['idDia']);
    }

    /**
     * Devuelve los albaranes a expedir del almacen $idAlmacen y día $idDia
     * agrupados por ruta y en orden inverso
     *
     * @param integer $idAlmacen El id del almacen a mostrar
     * @param integer $idDia El dia a mostrar
     * @param integer $idPeriodo El periodo a mostrar
     * @param integer $idEstado El estado a mostrar
     * @return array Array template,values
     */
    public function listAlbaranesAction($idAlmacen = '', $idDia = '', $idPeriodo = '', $idEstado = '') {

        if ($idAlmacen == '')
            $idAlmacen = $this->request[2];
        if ($idDia == '')
            $idDia = $this->request[3];
        if ($idPeriodo == '')
            $idPeriodo = $this->request[4];
        if ($idEstado == '')
            $idEstado = $this->request[5];

        $this->values['data'] = $this->getAlbaranes($idAlmacen, $idDia, $idPeriodo, $idEstado);
        $this->values['idDia'] = $idDia;
        $this->values['idPeriodo'] = $idPeriodo;
        $this->values['idEstado'] = $idEstado;

        return array('template' => $this->entity . '/detalleAlbaranes.html.twig', 'values' => $this->values);
    }

    public function imprimirAlbaranesAction($idAlmacen = '', $idDia = '', $idPeriodo = '', $idEstado = '') {

        if ($idAlmacen == '')
            $idAlmacen = $this->request[2];
        if ($idDia == '')
            $idDia = $this->request[3];
        if ($idPeriodo == '')
            $idPeriodo = $this->request[4];
        if ($idEstado == '')
            $idEstado = $this->request[5];

        $this->values['data'] = $this->getAlbaranes($idAlmacen, $idDia, $idPeriodo, $idEstado);
        $this->values['idDia'] = $idDia;

        return array('template' => $this->entity . '/detalleAlbaranes.html.twig', 'values' => $this->values);
    }

    /**
     * Genera un listado en formato PDF en base a los parametros obtenidos
     * del fichero listados.yml de cada controlador y los datos filtrados
     * segun el request
     * @return array Template y valores
     */
    public function listadoAction($idAlmacen = '', $idDia = '', $idPeriodo = '', $idEstado = '', $idRuta = '') {

        if ($idAlmacen == '')
            $idAlmacen = $this->request[2];
        if ($idDia == '')
            $idDia = $this->request[3];
        if ($idPeriodo == '')
            $idPeriodo = $this->request[4];
        if ($idEstado == '')
            $idEstado = $this->request[5];
        if ($idRuta == '')
            $idRuta = $this->request[6];

        $fecha = new Fecha();
        $fechaDesde = $fecha->sumaDias(-1 * $idPeriodo);
        unset($fecha);

        $almacen = new Almacenes($idAlmacen);
        $dia = new DiasSemana($idDia);
        $ruta = new RutasReparto($idRuta);

        $opciones = array(
            'almacen' => $almacen->getNombre(),
            'dia' => $dia->getDescripcion(),
            'ruta' => $ruta->getDescripcion(),
        );
        unset($almacen);
        unset($dia);
        unset($ruta);

        $em = new EntityManager("datos" . $_SESSION['emp']);

        if ($em->getDbLink()) {
            $query = "SELECT a.IDAlbaran
                            FROM albaranes_cab as a, rutas_reparto_detalle as r
                            WHERE a.Fecha>'{$fechaDesde}'
                            AND a.IDEstado='{$idEstado}'
                            AND a.IDAlmacen='{$idAlmacen}'
                            AND a.IDSucursal='{$_SESSION['suc']}'
                            AND a.DiaReparto='{$idDia}'
                            AND a.IDDirec=r.IDDirec
                            AND r.IDRuta = '{$idRuta}'
                            ORDER BY r.OrdenDirec DESC;";

            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();
        }

        $pdf = new listadoOrdenRepartoPDF("L", 'mm', "A4", $opciones);
        $pdf->SetTopMargin(15);
        $pdf->SetLeftMargin(10);
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $repartidor = "";
        foreach ($rows as $orden => $row) {
            $albaran = new AlbaranesCab($row['IDAlbaran']);
            $cliente = $albaran->getIDCliente();

            if ($repartidor != $albaran->getIDRepartidor()) {
                $repartidor = $albaran->getIDRepartidor();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 4, $repartidor, 0, 1);
                $pdf->SetFont('Arial', '', 9);
            }
            $pdf->Cell(15, 4, "", 0, 0, "R");
            $pdf->Cell(15, 4, "", 0, 0, "C");
            $pdf->Cell(150, 4, $cliente->getNombreComercial() . " " . $cliente->getRazonSocial(), 0, 0, "L");
            $pdf->Cell(50, 4, $albaran->getIDComercial()->getNombre(), 0, 0, "L");
            $pdf->Ln();
            $pdf->Cell(30, 4, "", 0, 0, "C");
            $pdf->Cell(150, 4, $albaran->getIDDirec()->getDireccion() . " - " . $albaran->getIDDirec()->getPoblacion(), 0, 0, "L");
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Line($pdf->GetX(),$pdf->GetY(),290,$pdf->GetY());
        }
        unset($cliente);
        unset($albaran);

        $archivo = Archivo::getTemporalFileName();
        $pdf->Output($archivo, 'F');
        unset($pdf);

        $this->values['archivo'] = $archivo;
        $template = '_global/listadoPdf.html.twig';

        return array('template' => $template, 'values' => $this->values);
    }

    /**
     * Devuelve un array de objetos AlbaranesCab con los albaranes
     * que están en estado Confirmado (1) y/o Expedido (2) y que pertenecen al
     * almacén $idAlmacen, sucursal en curso y dia de reparto $idDia seleccionados,
     * agrupados por ruta y ordenados de forma inversa al orden de la ruta de reparto.
     *
     * @param integer $idAlmacen El id del almacen a mostrar
     * @param integer $idDia El dia a mostrar
     * @param integer $idPeriodo El periodo a mostrar
     * @param integer $idEstado El estado a mostrar
     * @return array Array de objetos AlbaranesCab
     */
    private function getAlbaranes($idAlmacen, $idDia, $idPeriodo, $idEstado) {

        $data = array();

        $fecha = new Fecha();
        $fechaDesde = $fecha->sumaDias(-1 * $idPeriodo);
        unset($fecha);

        $em = new EntityManager("datos" . $_SESSION['emp']);

        if ($em->getDbLink()) {
            $query = "SELECT DISTINCT a.IDAlbaran
                            FROM albaranes_cab as a, rutas_reparto_detalle as r
                            WHERE a.Fecha>'{$fechaDesde}'
                            AND a.IDEstado='{$idEstado}'
                            AND a.IDAlmacen='{$idAlmacen}'
                            AND a.IDSucursal='{$_SESSION['suc']}'
                            AND a.DiaReparto='{$idDia}'
                            AND a.IDDirec=r.IDDirec
                            ORDER BY r.IDRuta ASC, a.IDEstado ASC, r.OrdenDirec DESC;";

            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();
        }

        foreach ($rows as $row) {
            $data[] = new AlbaranesCab($row['IDAlbaran']);
        }

        return $data;
    }

    /**
     * Devuelve un array de objetos ManufacCab con las elaboraciones
     * que están en estado Confirmado (1) y cuyo almacén origen es
     * el seleccionado
     *
     * @return array Array de objetos ManufacCab
     */
    private function getElaboraciones() {

        $data = array();

        $manufac = new ManufacCab();
        $filtro = "IDEstado='1' and IDAlmacenOrigen='{$this->request['idAlmacen']}'";
        $rows = $manufac->cargaCondicion('IDManufac', $filtro, 'FechaOrden ASC');
        unset($manufac);

        foreach ($rows as $row) {
            $data[] = new ManufacCab($row['IDManufac']);
        }

        return $data;
    }

    /**
     * Devuelve un array de objetos TraspasosCab con los traspasos
     * que están en estado Confirmado (1) y cuyo almacén origen es
     * el seleccionado
     *
     * @return array Array de objetos TraspasosCab
     */
    private function getTraspasos() {

        $data = array();

        $traspaso = new TraspasosCab();
        $filtro = "IDEstado='1' and IDAlmacenOrigen='{$this->request['idAlmacen']}'";
        $rows = $traspaso->cargaCondicion('IDTraspaso', $filtro, 'FechaOrden ASC');
        unset($traspaso);

        foreach ($rows as $row) {
            $data[] = new TraspasosCab($row['IDTraspaso']);
        }

        return $data;
    }

}

class listadoOrdenRepartoPDF extends FPDF {

    //Cabecera de página
    function Header() {
        $empresa = new Empresas($_SESSION['emp']);
        $sucursal = new Sucursales($_SESSION['suc']);

        $this->Image($empresa->getLogo(), 10, 8, 23);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, $empresa->getRazonSocial(), 0, 1, "R");
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 5, $sucursal->getNombre(), 0, 1, "R");
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, "ORDEN DE REPARTO ". $this->opciones['almacen'] . " " . $this->opciones['dia']. " Ruta: " . $this->opciones['ruta'], 0, 1, "C");
        $this->SetFont('Arial', 'B', 9);

        $this->Cell(15, 4, "Orden", 1, 0, "C");
        $this->Cell(15, 4, "Hora", 1, 0, "C");
        $this->Cell(150, 4, "Cliente / Direccion", 1, 0, "C");
        $this->Cell(50, 4, "Comercial", 1, 0, "C");
        $this->Cell(50, 4, "Observaciones", 1, 0, "C");
        $this->Ln();
        $this->SetFont('Arial', '', 8);
    }

    function Footer() {
        $this->SetY(-20);
        $this->Cell(23, 5, "Hora de salida: ", 0, 0);
        $this->Cell(20, 5, "", 1, 0);
        $this->Cell(26, 5, " Hora de llegada: ", 0, 0);
        $this->Cell(20, 5, "", 1, 0);
        $this->Cell(25, 5, " Kms recorridos: ", 0, 0);
        $this->Cell(20, 5, "", 1, 0);
        parent::Footer();
    }

}

?>
