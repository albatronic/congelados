<?php

/**
 * CONTROLLER FOR AlbaranesCab
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 07.06.2011 00:45:13

 * Extiende a la clase controller
 */
class AlbaranesCabController extends Controller {

    protected $entity = "AlbaranesCab";
    protected $parentEntity = "";

    /**
     * Generar el listado de albaranes apoyándose en el método padre
     * Si el usuario es comercial muestra solo los
     * suyos, si no es comercial muestra todos.
     * @return array
     */
    public function listAction($aditionalFilter='') {
        $tabla = $this->form->getDataBaseName() . "." . $this->form->getTable();
        $usuario = new Agentes($_SESSION['USER']['user']['id']);
        if ($usuario->getEsComercial())
            $filtro = $tabla . ".IDComercial='" . $usuario->getIDAgente() . "'";
        return parent::listAction($filtro);
    }

    /**
     * Marca el albarán como confirmado (estado 1)
     * Pone sus líneas como Reservadas (estado 1)
     * Actualiza existencias haciendo la reserva
     * @return array Template y values
     */
    public function confirmarAction() {
        if ($this->values['permisos']['A']) {

            // Se puede confirmar si está en estado de elaboración (0)
            $datos = new AlbaranesCab($this->request['AlbaranesCab']['IDAlbaran']);
            if ($datos->getIDEstado()->getIDTipo() == '0') {
                $datos->confirma();
                $this->values['errores'] = $datos->getErrores();
                $this->values['alertas'] = $datos->getAlertas();
                $datos = new AlbaranesCab($this->request['AlbaranesCab']['IDAlbaran']);
            } else
                $this->values['errores'][] = "El albarán ya ha sido confirmado";

            $this->values['datos'] = $datos;
            unset($datos);
            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Quita la marca de confirmación pasando el estado 1 al estado 0 (elaboracion)
     * Pone sus líneas en el estado 0 (elaboracion)
     * Actualiza existencias quitando la reserva
     * @return array Template y values
     */
    public function anularAction() {
        if ($this->values['permisos']['A']) {

            $datos = new AlbaranesCab($this->request['AlbaranesCab']['IDAlbaran']);
            if ($datos->getIDEstado()->getIDTipo() == '1') {
                $datos->anulaConfirmacion();
                $this->values['errores'] = $datos->getErrores();
                $this->values['alertas'] = $datos->getAlertas();
                $datos = new AlbaranesCab($this->request['AlbaranesCab']['IDAlbaran']);
            } else
                $this->values['errores'][] = "El albarán ya ha sido expedido, no se puede desconfirmar";

            $this->values['datos'] = $datos;
            unset($datos);
            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Factura el albarán si no está facturado y está expedido (estado = 2)
     *
     * @return array Template y values
     */
    public function facturarAction() {
        if ($this->values['permisos']['A']) {

            $datos = new AlbaranesCab($this->request['AlbaranesCab']['IDAlbaran']);

            if (($datos->getIDEstado()->getIDTipo() == '2') and (!$datos->getIDFactura()->getIDFactura())) {
                $idSucursal = $datos->getIDSucursal()->getIDSucursal();
                $contador = new Contadores();
                $contador = $contador->dameContador($idSucursal, 2);
                $datos = new AlbaranesCab($this->request['AlbaranesCab']['IDAlbaran']);
                $datos->facturar($contador);
                $this->values['errores'] = $datos->getErrores();
                $this->values['alertas'] = $datos->getAlertas();
                $datos = new AlbaranesCab($this->request['AlbaranesCab']['IDAlbaran']);
                unset($contador);
            } else
                $this->values['errores'][] = "El albarán ya está facturado";

            $this->values['datos'] = $datos;
            unset($datos);
            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Hace una copia del albarán.
     * Genera otro albarán en base al actual.
     * IMPORTANTE: SE TOMAN LOS PRECIOS ACTUALES DE LOS ARTICULOS
     *
     * @return array Template y values
     */
    public function duplicarAction() {
        if ($this->values['permisos']['I']) {

            $datos = new AlbaranesCab($this->request['AlbaranesCab']['IDAlbaran']);
            $idAlbaranNuevo = $datos->duplica();
            if ($idAlbaranNuevo)
                $this->values['datos'] = new AlbaranesCab($idAlbaranNuevo);
            else
                $this->values['datos'] = new AlbaranesCab($this->request['AlbaranesCab']['IDAlbaran']);

            $this->values['errores'] = $datos->getErrores();
            $this->values['alertas'] = $datos->getAlertas();
            unset($datos);

            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Envia por email el albaran en formato PDF
     * @return <type>
     */
    public function enviarAction() {

        switch ($this->request['accion']) {
            case 'Enviar':
                $para = $this->request['Para'];
                $de = $this->request['De'];
                $deNombre = $this->request['DeNombre'];
                $asunto = $this->request['Asunto'];
                $mensaje = $this->request['Mensaje'];
                $adjuntos = array($this->request['Adjunto'],);

                $envio = new Mail();
                $ok = $envio->send($para, $de, $deNombre, $asunto, $mensaje, $adjuntos);
                if ($ok) {
                    $entidad = new $this->entity($this->request['AlbaranesCab']['IDAlbaran']);
                    $entidad->auditaEmail();
                    unset($entidad);
                    $this->values['resultadoEnvio'][] = "Envío con éxito";
                } else {
                    $this->values['resultadoEnvio'] = $envio->getMensaje();
                }
                unset($envio);
                break;

            case 'CambioFormato':
                $datos = new AlbaranesCab($this->request['AlbaranesCab']['IDAlbaran']);
                $formatos = DocumentoPdf::getFormatos('albaranes');
                $formato = $this->request['Formato'];
                if ($formato == '')
                    $formato = 0;

                $this->values['archivo'] = $this->generaPdf('albaranes', array('0' => $datos->getIDAlbaran()), $formato);
                $this->values['email'] = array(
                    'Para' => $this->request['Para'],
                    'De' => $this->request['De'],
                    'DeNombre' => $this->request['DeNombre'],
                    'Cc' => $this->request['Cc'],
                    'Asunto' => $this->request['Asunto'],
                    'Formatos' => $formatos,
                    'Formato' => $formato,
                    'Mensaje' => $this->request['Mensaje'],
                    'idAlbaran' => $datos->getIDAlbaran(),
                );
                break;

            case '':
                $datos = new AlbaranesCab($this->request['AlbaranesCab']['IDAlbaran']);
                $formatos = DocumentoPdf::getFormatos('albaranes');
                $formato = $this->request['Formato'];
                if ($formato == '')
                    $formato = 0;

                $this->values['archivo'] = $this->generaPdf('albaranes', array('0' => $datos->getIDAlbaran()), $formato);
                $this->values['email'] = array(
                    'Para' => $datos->getIDCliente()->getEMail(),
                    'De' => $datos->getIDComercial()->getEMail(),
                    'DeNombre' => $datos->getIDComercial()->getNombre(),
                    'Cc' => '',
                    'Asunto' => 'Albaran n. ' . $datos->getNumeroAlbaran(),
                    'Formatos' => $formatos,
                    'Formato' => $formato,
                    'Mensaje' => 'Le adjunto el albaran ' . $datos->getNumeroAlbaran() . "\n\nUn saludo.",
                    'idAlbaran' => $datos->getIDAlbaran(),
                );
                break;
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

        // Recorro el array de los albaranes a imprimir
        foreach ($idsDocumento as $key => $idDocumento) {
            // Instancio la cabecera del albaran
            $master[$key] = new AlbaranesCab($idDocumento);

            // LLeno el array con objetos de lineas de albaran
            $lineas = array();
            $albaranLineas = new AlbaranesLineas();
            $rows = $albaranLineas->cargaCondicion('IDLinea', "IDAlbaran='{$idDocumento}' and Unidades<>0", "IDLinea ASC");
            foreach ($rows as $row) {
                $lineas[] = new AlbaranesLineas($row['IDLinea']);
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