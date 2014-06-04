<?php

/**
 * CONTROLLER FOR PedidosCab
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 07.06.2011 00:45:13

 * Extiende a la clase controller
 */
class PedidosCabController extends Controller {

    protected $entity = "PedidosCab";
    protected $parentEntity = "";

    /**
     * Marca el pedido como confirmado (estado 1)
     * Pone sus líneas como confirmadas (estado 1)
     * @return array Template y values
     */
    public function confirmarAction() {
        if ($this->values['permisos']['A']) {

            $datos = new $this->entity($this->request[$this->entity]['IDPedido']);
            if ($datos->getIDEstado()->getIDTipo() == '0') {
                $datos->confirma();
                $this->values['errores'] = $datos->getErrores();
                $this->values['alertas'] = $datos->getAlertas();
                $datos = new $this->entity($this->request[$this->entity]['IDPedido']);
            }

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
     * Actualiza existencias quitando la prevision de entrada
     * @return array Template y values
     */
    public function anularAction() {
        if ($this->values['permisos']['A']) {

            $datos = new $this->entity($this->request[$this->entity]['IDPedido']);
            if ($datos->getIDEstado()->getIDTipo() == '1') {
                $datos->anulaConfirmacion();
                $this->values['errores'] = $datos->getErrores();
                $this->values['alertas'] = $datos->getAlertas();
                $datos = new $this->entity($this->request[$this->entity]['IDPedido']);
            }

            $this->values['datos'] = $datos;
            unset($datos);
            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Factura el pedido si aún no lo está y está recepcionado
     *
     * @return array Template y values
     */
    public function facturarAction() {
        if ($this->values['permisos']['A']) {

            $datos = new PedidosCab($this->request['PedidosCab']['IDPedido']);

            if (($datos->getIDEstado()->getIDTipo() == 2) and ($datos->getIDFactura()->getIDFactura() == 0)) {
                $idSucursal = $datos->getIDSucursal()->getIDSucursal();
                $contador = new Contadores();
                $contador = $contador->dameContador($idSucursal, 4);
                $datos = new PedidosCab($this->request['PedidosCab']['IDPedido']);
                $datos->facturar($contador, $this->request['Factura_FechaFactura'], $this->request['Factura_SuFactura']);
                $this->values['errores'] = $datos->getErrores();
                $this->values['alertas'] = $datos->getAlertas();
                $datos = new PedidosCab($this->request['PedidosCab']['IDPedido']);
                unset($contador);
            }
            $this->values['datos'] = $datos;
            unset($datos);
            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Hace una copia del pedido.
     * Genera otro pedido en base al actual.
     * IMPORTANTE: SE TOMAN LOS PRECIOS ACTUALES DE LOS ARTICULOS
     *
     * @return array Template y values
     */
    public function duplicarAction() {
        if ($this->values['permisos']['I']) {

            $datos = new PedidosCab($this->request['PedidosCab']['IDPedido']);
            $idPedidoNuevo = $datos->duplica();
            $this->values['errores'] = $datos->getErrores();
            $this->values['alertas'] = $datos->getAlertas();
            unset($datos);

            $this->values['datos'] = new PedidosCab($idPedidoNuevo);

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
                $this->values['resultadoEnvio'] = $envio->send($para, $de, $deNombre, $asunto, $mensaje, $adjuntos);
                unset($envio);
                break;

            case 'CambioFormato':
                $datos = new PedidosCab($this->request['PedidosCab']['IDPedido']);
                $formatos = DocumentoPdf::getFormatos('pedidos');
                $formato = $this->request['Formato'];
                if ($formato == '')
                    $formato = 0;

                $this->values['archivo'] = $this->generaPdf('pedidos', array('0' => $datos->getIDPedido()), $formato);
                $this->values['email'] = array(
                    'Para' => $this->request['Para'],
                    'De' => $this->request['De'],
                    'DeNombre' => $this->request['DeNombre'],
                    'Cc' => $this->request['Cc'],
                    'Asunto' => $this->request['Asunto'],
                    'Formatos' => $formatos,
                    'Formato' => $formato,
                    'Mensaje' => $this->request['Mensaje'],
                    'idAlbaran' => $datos->getIDPedido(),
                );
                break;

            case '':
                $datos = new $this->entity($this->request[$this->entity]['IDPedido']);
                $formatos = DocumentoPdf::getFormatos('pedidos');
                $formato = $this->request['Formato'];
                if ($formato == '')
                    $formato = 0;

                $this->values['archivo'] = $this->generaPdf('pedidos', array('0' => $datos->getIDPedido()), $formato);
                $this->values['email'] = array(
                    'Para' => $datos->getIDProveedor()->getEMail(),
                    'De' => $datos->getIDAgente()->getEMail(),
                    'DeNombre' => $datos->getIDAgente()->getNombre(),
                    'Cc' => '',
                    'Asunto' => 'Pedido n. ' . $datos->getIDPedido(),
                    'Formatos' => $formatos,
                    'Formato' => $formato,
                    'Mensaje' => 'Le adjunto el pedido ' . $datos->getIDPedido() . "\n\nUn saludo.",
                    'idPedido' => $datos->getIDPedido(),
                );
                break;
        }

        return parent::enviarAction();
    }

    /**
     * Genera un array con la informacion necesaria para imprimir el documento
     * Recibe un array con los ids de pedido
     *
     * @param integer $idsDocumento Array con los ids de pedido
     * @return array Array con dos elementos: master es un objeto pedido y detail es un array de objetos lineas de pedido
     */
    protected function getDatosDocumento(array $idsDocumento) {

        $master = array();
        $detail = array();

        // Recorro el array de los albaranes a imprimir
        foreach ($idsDocumento as $key => $idDocumento) {
            // Instancio la cabecera del pedido
            $master[$key] = new PedidosCab($idDocumento);

            // LLeno el array con objetos de lineas de pedido
            $lineas = array();
            $pedidoLineas = new PedidosLineas();
            $rows = $pedidoLineas->cargaCondicion('IDLinea', "IDPedido='{$idDocumento}'", "IDLinea ASC");
            foreach ($rows as $row) {
                $lineas[] = new PedidosLineas($row['IDLinea']);
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