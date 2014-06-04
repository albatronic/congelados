<?php

/**
 * CONTROLLER FOR FemitidasCab
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 07.06.2011 00:45:15

 * Extiende a la clase controller
 */
class FemitidasCabController extends Controller {

    protected $entity = "FemitidasCab";
    protected $parentEntity = "";

    /**
     * Generar el listado de facturas apoyándose en el método padre
     * Si el usuario es comercial muestra solo las
     * suyas, si no es comercial muestra todos.
     * @return array
     */
    public function listAction($aditionalFilter = "") {
        $tabla = $this->form->getDataBaseName() . "." . $this->form->getTable();
        $usuario = new Agentes($_SESSION['USER']['user']['id']);
        if ($usuario->getEsComercial())
            $filtro = $tabla . ".IDComercial='" . $usuario->getIDAgente() . "'";
        return parent::listAction($filtro);
    }

    /**
     * Envia por email la factura en formato PDF
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
                    $entidad = new $this->entity($this->request['FemitidasCab']['IDFactura']);
                    $entidad->auditaEmail();
                    unset($entidad);
                    $this->values['resultadoEnvio'][] = "Envío con éxito";
                } else {
                    $this->values['resultadoEnvio'] = $envio->getMensaje();
                }
                unset($envio);
                break;

            case 'CambioFormato':
                $datos = new FemitidasCab($this->request['FemitidasCab']['IDFactura']);
                $formatos = DocumentoPdf::getFormatos('facturas');
                $formato = $this->request['Formato'];
                if ($formato == '')
                    $formato = 0;

                $this->values['archivo'] = $this->generaPdf('facturas', array('0' => $datos->getIDFactura()), $formato);
                $this->values['email'] = array(
                    'Para' => $this->request['Para'],
                    'De' => $this->request['De'],
                    'DeNombre' => $this->request['DeNombre'],
                    'Cc' => $this->request['Cc'],
                    'Asunto' => $this->request['Asunto'],
                    'Formatos' => $formatos,
                    'Formato' => $formato,
                    'Mensaje' => $this->request['Mensaje'],
                    'idAlbaran' => $datos->getIDFactura(),
                );
                break;

            case '':
                $datos = new FemitidasCab($this->request['FemitidasCab']['IDFactura']);
                $formatos = DocumentoPdf::getFormatos('facturas');
                $formato = $this->request['Formato'];
                if ($formato == '')
                    $formato = 0;

                $this->values['archivo'] = $this->generaPdf('facturas', array('0' => $datos->getIDFactura()), $formato);
                $this->values['email'] = array(
                    'Para' => $datos->getIDCliente()->getEMail(),
                    'De' => $datos->getIDComercial()->getEMail(),
                    'DeNombre' => $datos->getIDComercial()->getNombre(),
                    'Cc' => '',
                    'Asunto' => 'Factura N. ' . $datos->getNumeroFactura(),
                    'Formatos' => $formatos,
                    'Formato' => $formato,
                    'Mensaje' => 'Le adjunto la factura ' . $datos->getNumeroFactura() . "\n\nUn saludo.",
                    'idFactura' => $datos->getIDFactura(),
                );
                break;
        }

        return parent::enviarAction();
    }

    /**
     * Devuelve el template "listVencimientos" con un listado
     * de todos los vencimientos de la factura en curso
     *
     * El template extiende al popup y está pensado para ser mostrado
     * en una solapa
     *
     * @return array
     */
    public function listVencimientosAction() {

        $idFactura = $this->request[2];

        $datos = new FemitidasCab($idFactura);
        $this->values['recibos'] = $datos->getRecibos();

        unset($datos);

        return array('template' => $this->entity . "/listVencimientos.html.twig", 'values' => $this->values);
    }

    /**
     * Genera un array con la informacion necesaria para imprimir el documento
     * Recibe un array con los ids de factura
     *
     * @param array $idsDocumento Array con los ids de facturas
     * @return array Array con dos elementos: master es un objeto factura y detail es un array de objetos lineas de factura
     */
    protected function getDatosDocumento($idsDocumento) {

        $master = array();
        $detail = array();

        // Recorro el array de las facturas a imprimir
        foreach ($idsDocumento as $key => $idDocumento) {
            // Instancio la cabecera de la factura
            $master[$key] = new FemitidasCab($idDocumento);

            // LLeno el array con objetos de lineas de factura
            $facturaLineas = new FemitidasLineas();
            $rows = $facturaLineas->cargaCondicion('IDLinea', "IDFactura='{$idDocumento}' and Unidades<>0", "IDLinea ASC");
            foreach ($rows as $row) {
                $lineas[] = new FemitidasLineas($row['IDLinea']);
            }
            $detail[$key] = $lineas;
        }

        return array(
            'master' => $master,
            'detail' => $detail,
        );
    }

    /**
     * Edita, actualiza o borrar un registro
     *
     * Si viene por GET es editar
     * Si viene por POST puede ser actualizar o borrar
     * según el valor de $this->request['accion']
     *
     * @return array con el template y valores a renderizar
     */
    public function editAction() {

        switch ($this->request["METHOD"]) {
            case 'GET':
                if ($this->values['permisos']['C']) {
                    //SI EN LA POSICION 3 DEL REQUEST VIENE ALGO,
                    //SE ENTIENDE QUE ES EL VALOR DE LA CLAVE PARA LINKAR CON LA ENTIDAD PADRE
                    //ESTO SE UTILIZA PARA LOS FORMULARIOS PADRE->HIJO
                    if ($this->request['3'] != '')
                        $this->values['linkBy']['value'] = $this->request['3'];

                    //MOSTRAR DATOS. El ID viene en la posicion 2 del request
                    $datos = new $this->entity($this->request[2]);
                    if ($datos->getStatus()) {
                        $this->values['datos'] = $datos;
                        $this->values['errores'] = $datos->getErrores();
                        return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
                    } else {
                        $this->values['errores'] = array("Valor no encontrado. El objeto que busca no existe. Es posible que haya sido eliminado por otro usuario.");
                        return array('template' => $this->entity . '/new.html.twig', 'values' => $this->values);
                    }
                } else {
                    return array('template' => '_global/forbiden.html.twig');
                }
                break;

            case 'POST':
                //COGER DEL REQUEST EL LINK A LA ENTIDAD PADRE
                if ($this->values['linkBy']['id'] != '') {
                    $this->values['linkBy']['value'] = $this->request[$this->entity][$this->values['linkBy']['id']];
                }

                switch ($this->request['accion']) {
                    case 'Guardar': //GUARDAR DATOS
                        if ($this->values['permisos']['A']) {
                            // Cargo la entidad
                            $datos = new $this->entity($this->request[$this->entity][$this->form->getPrimaryKey()]);
                            // Comprar si se han cambiado la forma de pago.
                            // En cuyo caso hay que borrar los vencimientos y volverlos a crear.
                            if ($datos->getIDFP()->getIDFP() != $this->request[$this->entity]['IDFP']) {
                                $cambioFormaPago = $datos->borraVctos();
                            }
                            // Vuelco los datos del request
                            $datos->bind($this->request[$this->entity]);
                            if ($datos->valida($this->form->getRules())) {
                                $this->values['alertas'] = $datos->getAlertas();
                                $datos->recalcula();
                                if ($cambioFormaPago) {
                                    $datos->creaVctos();
                                    // Anotar en caja sin procede
                                    $datos->anotaEnCaja();
                                }

                                //Recargo el objeto para refrescar las propiedas que
                                //hayan podido ser objeto de algun calculo durante el proceso
                                //de guardado.
                                $datos = new $this->entity($this->request[$this->entity][$datos->getPrimaryKeyName()]);
                            } else {
                                $this->values['errores'] = $datos->getErrores();
                                $this->values['alertas'] = $datos->getAlertas();
                            }
                            $this->values['datos'] = $datos;
                            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
                        } else {
                            return array('template' => '_global/forbiden.html.twig');
                        }
                        break;

                    case 'Borrar': //BORRAR DATOS
                        if ($this->values['permisos']['B']) {
                            $datos = new $this->entity($this->request[$this->entity][$this->form->getPrimaryKey()]);

                            if ($datos->erase()) {
                                $datos = new $this->entity();
                                $this->values['datos'] = $datos;
                                $this->values['errores'] = array();
                                unset($datos);
                                return array('template' => $this->entity . '/index.html.twig', 'values' => $this->values);
                            } else {
                                $this->values['datos'] = $datos;
                                $this->values['errores'] = $datos->getErrores();
                                $this->values['alertas'] = $datos->getAlertas();
                                unset($datos);
                                return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
                            }
                        } else {
                            return array('template' => '_global/forbiden.html.twig');
                        }
                        break;
                }
                break;
        }
    }

}

?>