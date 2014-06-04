<?php
/**
 * CONTROLLER FOR PstoCab
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 07.06.2011 00:45:18

 * Extiende a la clase controller
 */
class PstoCabController extends Controller {

    protected $entity = "PstoCab";
    protected $parentEntity = "";

    /**
     * Generar el listado de presupuestos apoyándose en el método padre
     * Si el usuario es comercial muestra solo los
     * suyos, si no es comercial muestra todos.
     * @return array
     */
    public function listAction() {
        $tabla = $this->form->getDataBaseName() . "." . $this->form->getTable();
        $usuario = new Agentes($_SESSION['USER']['user']['id']);
        if ($usuario->getEsComercial())
            $filtro = $tabla . ".IDComercial='" . $usuario->getIDAgente() . "'";
        unset($usuario);

        return parent::listAction($filtro);
    }

    /**
     * Marca el presupuesto como confirmado (estado 1)
     * Generar un albaran en base al presupuesto
     * @return array Template y values
     */
    public function confirmarAction() {
        if ($this->values['permisos']['A']) {

            $datos = new PstoCab($this->request['PstoCab']['IDPsto']);
            $datos->confirma();
            $this->values['errores'] = $datos->getErrores();
            $this->values['alertas'] = $datos->getAlertas();
            unset($datos);

            $template = "PstoCab/edit.html.twig";
            $this->values['datos'] = new PstoCab($this->request['PstoCab']['IDPsto']);

            return array('template' => $template, 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Marca el presupuesto y sus lineas como no confirmado (estado 0)
     * NO BORRAR EL ALBARAN QUE SE GENERÓ EN BASE A EL.
     * @return array Template y values
     */
    public function anularAction() {
        if ($this->values['permisos']['A']) {

            $datos = new PstoCab($this->request['PstoCab']['IDPsto']);
            $datos->anulaConfirmacion();
            $this->values['errores'] = $datos->getErrores();
            $this->values['alertas'] = $datos->getAlertas();
            unset($datos);

            $this->values['datos'] = new PstoCab($this->request['PstoCab']['IDPsto']);

            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Hace una copia del presupuesto.
     * Genera otro presupuesto en base al actual.
     * IMPORTANTE: SE TOMAN LOS PRECIOS ACTUALES DE LOS ARTICULOS
     * 
     * @return array Template y values
     */
    public function duplicarAction() {
        if ($this->values['permisos']['I']) {

            $datos = new PstoCab($this->request['PstoCab']['IDPsto']);
            $idPstoNuevo = $datos->duplica();
            if ($idPstoNuevo)
                $this->values['datos'] = new PstoCab($idPstoNuevo);
            else
                $this->values['datos'] = new PstoCab($this->request['PstoCab']['IDPsto']);

            $this->values['errores'] = $datos->getErrores();
            $this->values['alertas'] = $datos->getAlertas();
            unset($datos);

            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Envia por email el presupuesto en formato PDF
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
            $datos = new PstoCab($this->request['PstoCab']['IDPsto']);
            $formatos = DocumentoPdf::getFormatos('presupuestos');
            $formato = $this->request['Formato'];
            if ($formato == '')
                $formato = 0;

            $this->values['archivo'] = $this->generaPdf('presupuestos', array('0' => $datos->getIDPsto()), $formato);
            $this->values['email'] = array(
                'Para' => $datos->getIDCliente()->getEMail(),
                'De' => $datos->getIDComercial()->getEMail(),
                'DeNombre' => $datos->getIDComercial()->getNombre(),
                'Cc' => '',
                'Asunto' => 'Presupuesto n. ' . $datos->getIDPsto(),
                'Formatos' => $formatos,
                'Formato' => $formato,
                'Mensaje' => 'Le adjunto el presupuesto ' . $datos->getIDPsto() . "\n\nUn saludo.",
                'idPsto' => $datos->getIDPsto(),
            );
        }

        return parent::enviarAction();
    }

    /**
     * Genera un array con la informacion necesaria para imprimir el documento
     * Recibe el id del presupuesto
     *
     * @param array $idsDocumento Array con los ids de presupuestos
     * @return array Array con dos elementos: master es un objeto presupuesto y detail es un array de objetos lineas de presupuesto
     */
    protected function getDatosDocumento(array $idsDocumento) {

        $master = array();
        $detail = array();

        // Recorro el array de los albaranes a imprimir
        foreach ($idsDocumento as $key => $idDocumento) {
            // Instancio la cabecera del albaran
            $master[$key] = new PstoCab($idDocumento);

            // LLeno el array con objetos de lineas de presupuesto
            $lineas = array();
            $pstoLineas = new PstoLineas();
            $rows = $pstoLineas->cargaCondicion('IDLinea', "IDPsto='{$idDocumento}'", "IDPsto ASC");
            foreach ($rows as $row) {
                $lineas[] = new PstoLineas($row['IDLinea']);
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