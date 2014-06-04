<?php

/**
 * CONTROLLER FOR Rutas
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 28.07.2011 12:01:02

 * Extiende a la clase controller
 */
class RutasVentasController extends Controller {

    protected $entity = "RutasVentas";
    protected $parentEntity = "";

    public function __construct($request) {

        $comercial = new Agentes();
        $this->values['comerciales'] = $comercial->getComerciales();
        $this->values['comerciales'][] = array('Id' => '', 'Value' => ':: Indique un comercial');
        unset($comercial);

        parent::__construct($request);
    }

    /**
     * Muestra el template de edicion de rutas pasandole
     * el ID de comercial
     * @return array
     */
    public function editAction() {
        if ($this->request['IDComercial'] != '') {

            if ($this->values['permisos']['C']) {
                $this->values['linkBy']['id'] = 'IDComercial';
                $this->values['linkBy']['value'] = $this->request['IDComercial'];

                $dias = new DiasSemana();
                $this->values['dias'] = $dias->fetchAll();

                $template = $this->entity . '/edit.html.twig';
            } else
                $template = "_global/forbiden.html.twig";
        } else
            $template = $this->entity . '/index.html.twig';

        return array('template' => $template, 'values' => $this->values);
    }

    /**
     * Genera un listado en PDF con todos clientes del comercial
     * separados por dia (el rutero)
     */
    public function listadoAction() {

        if ($this->values['permisos']['L']) {
            $idComercial = $this->request['IDComercial'];
            $comercial = new Agentes($idComercial);
            $opciones = array('title' => 'Rutas de ventas del comercial ' . $comercial->getNombre());
            unset($comercial);

            //CREAR EL DOCUMENTO-------------------------------------------------------------
            $pdf = new RutasPDF('L', 'mm', 'A4', $opciones);
            //$pdf->SetTopMargin($MEDIDAS['MargenSup']);
            //$pdf->SetLeftMargin($MEDIDAS['MargenIzq']);
            $pdf->SetAuthor("Informatica ALBATRONIC, SL");
            $pdf->SetTitle('Rutas de Ventas');
            $pdf->AliasNbPages();
            $pdf->SetFillColor(210);
            $pdf->AddPage();
            $pdf->SetAutoPageBreak(1, 15);

            $lis = new $this->entity();
            $rows = $lis->cargaCondicion("Id", "IDComercial='{$idComercial}'", "Dia,OrdenCliente,IDZona ASC");
            unset($lis);

            //CUERPO-------------------------------------------------------------------------
            $diaAnt = '';
            $pdf->SetFont('Courier', '', 8);
            foreach ($rows as $value) {
                $datos = new $this->entity($value['Id']);
                if ($diaAnt != $datos->getDia()) {
                    $pdf->SetFont('Courier', 'B', 10);
                    $dia = new DiasSemana($datos->getDia());
                    $pdf->Cell(0, 10, $pdf->DecodificaTexto($dia->getDescripcion()));
                    unset($dia);
                    $pdf->Ln();
                    $pdf->SetFont('Courier', '', 8);
                }
                $diaAnt = $datos->getDia();
                $pdf->SetFont('Courier', 'B', 8);
                $pdf->Cell(30, 4, $pdf->DecodificaTexto($datos->getIDCliente()->getIDZona()->getZona(), 30), 0, 0);
                $pdf->SetFont('Courier', '', 8);
                $pdf->Cell(110, 4, $pdf->DecodificaTexto($datos->getIDCliente()->getRazonSocial() . " - " . $datos->getIDCliente()->getNombreComercial(), 110), 0, 0);
                $pdf->Cell(80, 4, $pdf->DecodificaTexto($datos->getIDCliente()->getDireccion(), 45), 0, 0);
                $pdf->Cell(20, 4, $pdf->DecodificaTexto($datos->getIDCliente()->getPoblacion(), 20), 0, 0);
                $pdf->Cell(30, 4, trim($datos->getIDCliente()->getTelefono() . " " . $datos->getIDCliente()->getMovil()), 0, 0);
                $pdf->Ln();
            }
            unset($datos);

            $archivo = "docs/docs" . $_SESSION['emp'] . "/pdfs/" . md5(date('d-m-Y H:i:s')) . ".pdf";
            $pdf->Output($archivo, 'F');
            unset($pdf);

            $this->values['archivo'] = $archivo;
            return array('template' => '_global/listadoPdf.html.twig', 'values' => $this->values,);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Genera un listado en PDF con todos clientes del comercial
     * separados por dia (el rutero)
     */
    public function HojaLlamadasAction() {

        if ($this->values['permisos']['L']) {
            $idComercial = $this->request[2];
            $comercial = new Agentes($idComercial);
            $dia = $this->request[3];
            $dias = new DiasSemana($dia);
            $opciones = array('title' => 'Hoja de llamadas para el ' .strtoupper($dias->getDescripcion()) . ' del comercial ' . strtoupper($comercial->getNombre()));
            unset($comercial);
            unset($dias);

            //CREAR EL DOCUMENTO-------------------------------------------------------------
            $pdf = new HojaLlamadasPDF('L', 'mm', 'A4', $opciones);
            //$pdf->SetTopMargin($MEDIDAS['MargenSup']);
            //$pdf->SetLeftMargin($MEDIDAS['MargenIzq']);
            $pdf->SetAuthor("Informatica ALBATRONIC, SL");
            $pdf->SetTitle('Hoja de llamadas Ventas');
            $pdf->AliasNbPages();
            $pdf->SetFillColor(210);
            $pdf->AddPage();
            $pdf->SetAutoPageBreak(1, 15);

            $lis = new $this->entity();
            $rows = $lis->cargaCondicion("Id", "IDComercial='{$idComercial}' and Dia='{$dia}'", "OrdenCliente,IDZona ASC");
            unset($lis);

            //CUERPO-------------------------------------------------------------------------
            $pdf->SetFont('Courier', '', 8);
            foreach ($rows as $value) {
                $datos = new $this->entity($value['Id']);
                
                $pdf->Cell(10, 6, "", 0, 0);
                $pdf->Cell(100, 6, $pdf->DecodificaTexto($datos->getIDCliente()->getRazonSocial() . " - " . $datos->getIDCliente()->getNombreComercial(), 59), 0, 0);
                $pdf->Cell(20, 6, $pdf->DecodificaTexto($datos->getIDCliente()->getPoblacion(), 11), 0, 0);
                $pdf->Cell(45, 6, trim($datos->getIDCliente()->getTelefono() . " " . $datos->getIDCliente()->getMovil()), 0, 0);
                $pdf->Cell(10, 6, $datos->getIDCliente()->getLimiteRiesgo(), 0, 0,"R");
                $pdf->Ln();
                
                $dEntrega = new ClientesDentrega();
                $direcciones = $dEntrega->cargaCondicion("IDDirec","IDCliente='{$datos->getIDCliente()->getIDCliente()}' and IDZona='{$datos->getIDZona()->getIDZona()}'");
                foreach ($direcciones as $direccion) {
                    $dEntrega = new ClientesDentrega($direccion['IDDirec']);
                    $pdf->Cell(15, 4, $dEntrega->getHorarioLlamadas(),0,0);
                    $pdf->Cell(0, 4, $dEntrega->getDireccion(),0,1);
                }
                unset($dEntrega);
                
                $pdf->Cell(0,3,  str_repeat("-",159),0,1);
            }
            unset($datos);

            $archivo = "docs/docs" . $_SESSION['emp'] . "/pdfs/" . md5(date('d-m-Y H:i:s')) . ".pdf";
            $pdf->Output($archivo, 'F');
            unset($pdf);

            $this->values['archivo'] = $archivo;
            return array('template' => '_global/listadoPdf.html.twig', 'values' => $this->values,);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }    
}

class RutasPDF extends FPDF {

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
        $this->Cell(30, 4, "Zona", 0, 0);
        $this->Cell(110, 4, "Cliente", 0, 0);
        $this->Cell(80, 4, "Direccion", 0, 0);
        $this->Cell(20, 4, "Poblacion", 0, 0);
        $this->Cell(30, 4, "Telefonos", 0, 0);
        $this->Ln();
    }

}
class HojaLlamadasPDF extends FPDF {

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
        $this->Cell(10, 4, "Hora", 0, 0, "C");        
        $this->Cell(100, 4, "Cliente", 0, 0, "C");
        $this->Cell(20, 4, "Poblacion", 0, 0, "C");
        $this->Cell(45, 4, "Telefonos", 0, 0, "C");
        $this->Cell(10, 4, "Riesgo", 0, 0, "C"); 
        $this->Cell(0, 4, "Comentarios", 0, 0, "C");
        $this->Ln();
    }

}
?>