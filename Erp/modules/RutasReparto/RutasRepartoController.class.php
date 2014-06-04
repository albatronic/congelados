<?php

/**
 * CONTROLLER FOR RutasReparto
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 14.05.2012 17:19:59

 * Extiende a la clase controller
 */
class RutasRepartoController extends Controller {

    protected $entity = "RutasReparto";
    protected $parentEntity = "";

    public function __construct($request) {

        $dias = new DiasSemana();
        $this->values['dias'] = $dias->fetchAll();

        parent::__construct($request);
    }

    public function listAction() {
        $tabla = $this->form->getDataBaseName() . "." . $this->form->getTable();
        $filtro = $tabla . ".IDSucursal='" . $_SESSION['suc'] . "'";

        return parent::listAction($filtro);
    }

    /**
     * Genera un listado en PDF con todos clientes del repartidor
     * separados por dia (el rutero)
     */
    public function listadoAction() {

        if ($this->values['permisos']['L']) {
            $idRuta = $this->request['RutasReparto']['IDRuta'];
            $ruta = new RutasReparto($idRuta);
            $opciones = array('title' => 'Ruta de reparto ' . $ruta->getDescripcion());
            unset($ruta);

            //CREAR EL DOCUMENTO-------------------------------------------------------------
            $pdf = new RutasPDF('L', 'mm', 'A4', $opciones);
            //$pdf->SetTopMargin($MEDIDAS['MargenSup']);
            //$pdf->SetLeftMargin($MEDIDAS['MargenIzq']);
            $pdf->SetAuthor("Informatica ALBATRONIC, SL");
            $pdf->SetTitle('Rutas de Reparto');
            $pdf->AliasNbPages();
            $pdf->SetFillColor(210);
            $pdf->AddPage();
            $pdf->SetAutoPageBreak(1, 15);

            $lis = new RutasRepartoDetalle();
            $rows = $lis->cargaCondicion("Id", "IDRuta='{$idRuta}'", "Dia,OrdenDirec,IDZona ASC");
            unset($lis);

            //CUERPO-------------------------------------------------------------------------
            $diaAnt = '';
            $pdf->SetFont('Courier', '', 8);
            foreach ($rows as $key => $value) {
                $datos = new RutasRepartoDetalle($value['Id']);
                if ($diaAnt != $datos->getDia()->getIDTipo()) {
                    $pdf->SetFont('Courier', 'B', 10);
                    $pdf->Cell(0, 10, $datos->getDia() . ": " . $datos->getIDRepartidor()->getNombre(), 0, 0, 'L', 1);
                    $pdf->Ln();
                    $pdf->SetFont('Courier', '', 8);
                }
                $diaAnt = $datos->getDia()->getIDTipo();
                $pdf->SetFont('Courier', 'B', 8);
                $pdf->Cell(20, 4, $pdf->DecodificaTexto($datos->getIDDirec()->getIDZona()->getZona(), 11), 0, 0);
                $pdf->SetFont('Courier', '', 8);
                $pdf->Cell(115, 4, $pdf->DecodificaTexto($datos->getIDDirec()->getNombre() . " - " . $datos->getIDDirec()->getIDCliente()->getNombreComercial(), 115), 0, 0);
                $pdf->Cell(70, 4, $pdf->DecodificaTexto($datos->getIDDirec()->getDireccion(), 40), 0, 0);
                $pdf->Cell(30, 4, $pdf->DecodificaTexto($datos->getIDDirec()->getPoblacion(), 17), 0, 0);
                $pdf->Cell(35, 4, $pdf->DecodificaTexto($datos->getIDDirec()->getTelefono() . " " . $datos->getIDDirec()->getHorario(), 30), 0, 0);
                $pdf->Ln();
            }
            unset($datos);

            $archivo = "docs/docs" . $_SESSION['emp'] . "/pdfs/" . md5(date('d-m-Y H:i:s')) . ".pdf";
            $pdf->Output($archivo, 'F');

            unset($datos);
            unset($pdf);

            $this->values['archivo'] = $archivo;
            return array('template' => '_global/listadoPdf.html.twig', 'values' => $this->values,);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

}

class rutasPDF extends FPDF {

    //Cabecera de página
    function Header() {

        $empresa = new Empresas($_SESSION['emp']);
        $sucursal = new Sucursales($_SESSION['suc']);

        $this->Image($empresa->getLogo(), 10, 8, 23);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, $empresa->getRazonSocial(), 0, 1, "R");
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 5, $sucursal->getNombre(), 0, 1, "R");
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 5, $this->opciones['title'], 0, 1, "C");

        $this->Ln(5);
        $this->SetFont('Courier', 'B', 8);
        //PINTAR LOS TITULOS DE LAS COLUMNAS
        $this->Cell(20, 4, "Zona", 0, 0);
        $this->Cell(115, 4, "Cliente", 0, 0);
        $this->Cell(70, 4, "Dirección", 0, 0);
        $this->Cell(30, 4, "Población", 0, 0);
        $this->Cell(35, 4, "Teléfono/Horario", 0, 0);
        $this->Ln();
    }

}

?>