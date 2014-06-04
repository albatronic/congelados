<?php

/**
 * CONTROLLER FOR PstoLineas
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 07.06.2011 00:45:18

 * Extiende a la clase controller
 */
class PstoLineasController extends Controller {

    protected $entity = "PstoLineas";
    protected $parentEntity = "PstoCab";

    public function __construct($request) {
        // Cargar lo que viene en el request
        $this->request = $request;

        $this->permisos = new ControlAcceso();
        $this->values['permisos'] = $this->permisos->getPermisos();
        $this->values['request'] = $this->request;
        $this->values['linkBy'] = array(
            'id' => 'IDPsto',
            'value' => '',
        );
    }

    public function listAction($idPsto='') {

        if ($this->values['permisos']['C']) {
            if ($idPsto == '')
                $idPsto = $this->request[2];

            $presupuesto = new $this->parentEntity($idPsto);
            if ($presupuesto->getIDEstado()->getIDTipo() == '0') {
                // Si el presupuesto está pte de confirmar, puedo modificar sus líneas y
                // por lo tanto le añado un objeto linea vacío
                $objetoNuevo = new $this->entity();
                $objetoNuevo->setIDPsto($idPsto);
                $lineas[] = $objetoNuevo;
                unset($objetoNuevo);
            }

            $lis = new $this->entity();
            $rows = $lis->cargaCondicion('IDLinea', "IDPsto = '" . $idPsto . "'", 'IDLinea ASC');
            foreach ($rows as $row) {
                $lineas[] = new $this->entity($row['IDLinea']);
            }

            if ($presupuesto->getIDEstado()->getIDTipo() == '0') {
                $template = $this->entity . '/form' . $_SESSION['ver'] . '.html.twig';
            } else {
                $template = $this->entity . '/list' . $_SESSION['ver'] . '.html.twig';
            }

            $this->values['linkBy']['value'] = $idPsto;
            $this->values['listado']['data'] = $lineas;

            unset($presupuesto);
            unset($lis);
            unset($lineas);

            return array('template' => $template, 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Crea un registro nuevo
     *
     * Siempre viene por POST
     * Si viene por POST crea un registro
     *
     * @return array con el template y valores a renderizar
     */
    public function newAction() {
        if ($this->values['permisos']['I']) {
            switch ($this->request["METHOD"]) {

                case 'POST': //CREAR NUEVO REGISTRO
                    //COGER EL LINK A LA ENTIDAD PADRE
                    if ($this->values['linkBy']['id'] != '') {
                        $this->values['linkBy']['value'] = $this->request[$this->entity][$this->values['linkBy']['id']];
                    }

                    $datos = new $this->entity();
                    $datos->bind($this->request[$this->entity]);

                    if ($datos->valida()) {
                        $datos->create();
                        $this->values['alertas'] = $datos->getAlertas();

                        //Recargo el objeto para refrescar las propiedas que
                        //hayan podido ser objeto de algun calculo durante el proceso
                        //de guardado.
                        $datos = new $this->entity($datos->getPrimaryKeyValue());
                        $this->values['datos'] = $datos;
                    } else {
                        $this->values['datos'] = $datos;
                        $this->values['errores'] = $datos->getErrores();
                    }
                    unset($datos);
                    return $this->listAction($this->values['linkBy']['value']);
                    break;
            }
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Edita, actualiza o borrar un registro
     *
     * Viene siempre por POST
     * Actualiza o Borrar según el valor de $this->request['accion']
     *
     * @return array con el template y valores a renderizar
     */
    public function editAction() {

        //COGER DEL REQUEST EL LINK A LA ENTIDAD PADRE
        if ($this->values['linkBy']['id'] != '') {
            $this->values['linkBy']['value'] = $this->request[$this->entity][$this->values['linkBy']['id']];
        }

        switch ($this->request['accion']) {
            case 'G': //GUARDAR DATOS
                if ($this->values['permisos']['A']) {
                    $datos = new $this->entity($this->request[$this->entity]['IDLinea']);
                    $datos->bind($this->request[$this->entity]);
                    if ($datos->valida()) {
                        $datos->save();
                        $this->values['errores'] = $datos->getErrores();
                        $this->values['alertas'] = $datos->getAlertas();

                        //Recargo el objeto para refrescar las propiedas que
                        //hayan podido ser motivo de algun calculo durante el proceso
                        //de guardado.
                        $datos = new $this->entity($this->request[$this->entity][$datos->getPrimaryKeyName()]);
                    }

                    $this->values['datos'] = $datos;
                    unset($datos);
                    return $this->listAction($this->values['linkBy']['value']);
                } else {
                    return array('template' => '_global/forbiden.html.twig');
                }
                break;

            case 'B': //BORRAR DATOS
                if ($this->values['permisos']['B']) {
                    $datos = new $this->entity($this->request[$this->entity]['IDLinea']);

                    if ($datos->erase()) {
                        $datos = new $this->entity();
                        $this->values['datos'] = $datos;
                        $this->values['errores'] = array();
                    } else {
                        $this->values['datos'] = $datos;
                        $this->values['errores'] = $datos->getErrores();
                    }
                    unset($datos);
                    return $this->listAction($this->request['PstoLineas']['IDPsto']);
                } else {
                    return array('template' => '_global/forbiden.html.twig');
                }
                break;
        }
    }

}

?>