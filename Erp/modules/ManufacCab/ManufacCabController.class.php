<?php

/**
 * CONTROLLER FOR ManufacCab
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 22.03.2012 19:21:54

 * Extiende a la clase controller
 */
class ManufacCabController extends Controller {

    protected $entity = "ManufacCab";
    protected $parentEntity = "";

    /**
     * Marca el parte de elaboración como Confirmado (estado 1)
     * Pone sus líneas como Reservadas (estado 1)
     * Actualiza existencias haciendo la reserva
     * @return array Template y values
     */
    public function confirmarAction() {
        if ($this->values['permisos']['A']) {

            // Se puede confirmar si está en estado de PTE. CONFIRMAR (0)
            $datos = new ManufacCab($this->request['ManufacCab']['IDManufac']);
            if ($datos->getIDEstado()->getIDTipo() == '0') {
                $datos->confirma();
                $this->values['errores'] = $datos->getErrores();
                $this->values['alertas'] = $datos->getAlertas();
                $datos = new ManufacCab($this->request['ManufacCab']['IDManufac']);
            }

            $this->values['datos'] = $datos;
            unset($datos);
            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Quita la marca de confirmación pasando del estado 1 al estado 0 (Pte. Confirmar)
     * Pone sus líneas en el estado 0 (Pte. Confirmar)
     * Actualiza existencias quitando la reserva
     * @return array Template y values
     */
    public function anularAction() {
        if ($this->values['permisos']['A']) {

            $datos = new ManufacCab($this->request['ManufacCab']['IDManufac']);
            if ($datos->getIDEstado()->getIDTipo() == '1') {
                $datos->anulaConfirmacion();
                $this->values['errores'] = $datos->getErrores();
                $this->values['alertas'] = $datos->getAlertas();
                $datos = new ManufacCab($this->request['ManufacCab']['IDManufac']);
            }

            $this->values['datos'] = $datos;
            unset($datos);
            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Recalcula los precios de costo y de venta de los articulos
     * obtenidos en la elaboración, prorrateando el costo total de
     * la elaboración entre las UMAs obtenidas de cada producto
     *
     * Este proceso se hace con las líneas de tipo 1 que estén recepcionadas (Estado=3)
     * y en base a la política de recálculo de precios definida con el parámetro ACTU_PRECIOS
     */
    public function CotizarAction() {
        if ($this->values['permisos']['A']) {

            $idManufac = $this->request['ManufacCab']['IDManufac'];
            $datos = new ManufacCab($idManufac);
            if ($datos->getKilosDestino() != 0) {
                $totalCoste = $datos->getTotalCoste();
                $totalUMAObtenidos = $datos->getKilosDestino();

                $costePorUMA = $totalCoste / $datos->getKilosDestino();

                //Recorrer las lineas de elaboracion tipo 1
                $lineas = new ManufacLineas();
                $rows = $lineas->cargaCondicion("*", "IDManufac='{$idManufac}' and Tipo='1' and IDEstado='3'");
                unset($lineas);

                foreach ($rows as $row) {
                    $articulo = new Articulos($row['IDArticulo']);
                    if ($articulo->actualizaPrecios($row['Unidades'], $costePorUMA))
                        $this->values['alertas'][] = "Se actualizó: " . $articulo->getCodigo() . " " . $articulo->getDescripcion();
                    else
                        $this->values['alertas'][] = "NO Se actualizó: " . $articulo->getCodigo() . " " . $articulo->getDescripcion();
                }
                unset($articulo);
            }

            $this->values['datos'] = $datos;
            $this->values['errores'] = $datos->getErrores();
            unset($datos);

            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Hace una copia del parte.
     * Genera otro parte en base al actual.
     *
     * @return array Template y values
     */
    public function duplicarAction() {
        if ($this->values['permisos']['I']) {

            $datos = new ManufacCab($this->request['ManufacCab']['IDManufac']);
            $idManufacNuevo = $datos->duplica();
            if ($idManufacNuevo)
                $this->values['datos'] = new ManufacCab($idManufacNuevo);
            else
                $this->values['datos'] = new ManufacCab($this->request['ManufacCab']['IDManufac']);

            $this->values['errores'] = $datos->getErrores();
            $this->values['alertas'] = $datos->getAlertas();
            unset($datos);

            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Envia por email el parte de elaboracion en formato PDF
     * @return <type>
     */
    public function enviarAction() {

        if ($this->request['accion'] == 'Enviar') {

            $para = $this->request['Para'];
            $de = $this->request['De'];
            $deNombre = $this->request['DeNombre'];
            $asunto = $this->request['Asunto'];
            $mensaje = $this->request['Mensaje'];
            $adjuntos = array($this->request['Adjunto'],);

            $envio = new Mail();
            $this->values['resultadoEnvio'] = $envio->send($para, $de, $deNombre, $asunto, $mensaje, $adjuntos);
            unset($envio);
        } else {
            $usuario = new Agentes($_SESSION['USER']['user']['id']);

            $datos = new ManufacCab($this->request['ManufacCab']['IDManufac']);
            $formatos = DocumentoPdf::getFormatos('parteElaboracion');
            $formato = $this->request['Formato'];
            if ($formato == '')
                $formato = 0;

            $this->values['archivo'] = $this->generaPdf('parteElaboracion', array('0' => $datos->getIDManufac()), $formato);
            $this->values['email'] = array(
                'Para' => $datos->getIDElaborador()->getEMail(),
                'De' => $usuario->getEMail(),
                'DeNombre' => $usuario->getNombre(),
                'Cc' => '',
                'Asunto' => 'Parte de Elaboración n. ' . $datos->getIDManufac(),
                'Formatos' => $formatos,
                'Formato' => $formato,
                'Mensaje' => 'Le adjunto el parte de elaboración ' . $datos->getIDManufac() . "\n\nUn saludo.",
                'idManufac' => $datos->getIDManufac(),
            );
            unset($usuario);
            unset($datos);
        }

        return parent::enviarAction();
    }

    /**
     * Genera un array con la informacion necesaria para imprimir el documento
     * Recibe un array con los ids de albaran
     * No muestra las lineas cuyas unidades son cero.
     *
     * @param array $idsDocumento Array con los ids de albaranes
     * @return array Array con dos elementos: master es un objeto albaran y detail es un array de objetos lineas de albaran
     */
    protected function getDatosDocumento(array $idsDocumento) {

        $master = array();
        $detail = array();

        // Recorro el array de los partes a imprimir
        foreach ($idsDocumento as $key => $idDocumento) {
            // Instancio la cabecera del albaran
            $master[$key] = new ManufacCab($idDocumento);

            // LLeno el array con objetos de lineas de partes de elaboracion
            $lineas = array();
            $manufacLineas = new ManufacLineas();
            $rows = $manufacLineas->cargaCondicion('IDLinea', "IDManufac='{$idDocumento}'", "Tipo,IDLinea ASC");
            foreach ($rows as $row) {
                $lineas[] = new ManufacLineas($row['IDLinea']);
            }
            $detail[$key] = $lineas;
        }

        return array(
            'master' => $master,
            'detail' => $detail,
        );
    }

}

?>