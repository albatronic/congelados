<?php

/**
 * CONTROLLER FOR TraspasosLineas
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 23.03.2012 19:13:09

 * Extiende a la clase controller
 */
class TraspasosLineasController extends Controller {

    protected $entity = "TraspasosLineas";
    protected $parentEntity = "TraspasosCab";

    public function __construct($request) {
        // Cargar lo que viene en el request
        $this->request = $request;

        $this->permisos = new ControlAcceso();
        $this->values['permisos'] = $this->permisos->getPermisos();
        $this->values['request'] = $this->request;
        $this->values['linkBy'] = array(
            'id' => 'IDTraspaso',
            'value' => '',
        );
    }

    public function listAction($idTraspaso='', $tipo='') {

        if ($this->values['permisos']['C']) {
            if ($idTraspaso == '')
                $idTraspaso = $this->request[2];
            if ($tipo == '')
                $tipo = $this->request[3];

            // Si el parte está pte de confirmar (0), puedo modificar todas las líneas y añado un objeto linea vacío
            // Si el parte está confirmado o en elaboración, puedo modificar las líneas de tipo 1 y añado un objeto linea vacío
            $traspaso = new $this->parentEntity($idTraspaso);
            $estadoTraspaso = $traspaso->getIDEstado()->getIDTipo();
            if (( $estadoTraspaso == '0') or (($estadoTraspaso > 0) and ($estadoTraspaso < 3) and ($tipo == '1'))) {
                $objetoNuevo = new $this->entity();
                $objetoNuevo->setIDTraspaso($idTraspaso);
                $objetoNuevo->setTipo($tipo);
                if ($tipo == '0')
                    $objetoNuevo->setIDAlmacen($traspaso->getIDAlmacenOrigen()->getIDAlmacen());
                else
                    $objetoNuevo->setIDAlmacen($traspaso->getIDAlmacenDestino()->getIDAlmacen());
                $lineas[] = $objetoNuevo;
                unset($objetoNuevo);
            }

            $lis = new $this->entity();
            $rows = $lis->cargaCondicion('IDLinea', "IDTraspaso='{$idTraspaso}' and Tipo='{$tipo}'", 'IDLinea ASC');
            foreach ($rows as $row) {
                $lineas[] = new $this->entity($row['IDLinea']);
            }

            switch ($estadoTraspaso) {
                case '0' : // Pte. Confirmar
                    $template = $this->entity . "/form{$tipo}.html.twig";
                    break;
                case '1' : // Confirmado
                case '2' : // En Elaboración
                    if ($tipo == 0)
                        $template = $this->entity . "/list.html.twig";
                    else
                        $template = $this->entity . "/form{$tipo}.html.twig";
                    break;
                case '3' : // Elaborado
                    $template = $this->entity . "/list.html.twig";
                    break;
            }

            $this->values['linkBy']['value'] = $idTraspaso;
            $this->values['listado']['data'] = $lineas;

            unset($traspaso);
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
                    return $this->listAction($this->values['linkBy']['value'], $this->request[$this->entity]['Tipo']);
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
                        $this->values['alertas'] = $datos->getAlertas();
                        $datos->save();

                        //Recargo el objeto para refrescar las propiedas que
                        //hayan podido ser motivo de algun calculo durante el proceso
                        //de guardado.
                        $datos = new $this->entity($this->request[$this->entity][$datos->getPrimaryKeyName()]);
                    } else {
                        $this->values['errores'] = $datos->getErrores();
                        $this->values['alertas'] = $datos->getAlertas();
                    }

                    $this->values['datos'] = $datos;
                    unset($datos);
                    return $this->listAction($this->values['linkBy']['value'], $this->request[$this->entity]['Tipo']);
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
                    return $this->listAction($this->request['TraspasosLineas']['IDTraspaso'], $this->request[$this->entity]['Tipo']);
                } else {
                    return array('template' => '_global/forbiden.html.twig');
                }
                break;
        }
    }

    /**
     * Marca el traspaso como confirmado (estado 1)
     * Pone sus líneas como Reservadas (estado 1)
     * Actualiza existencias haciendo la reserva
     * @return array Template y values
     */
    public function expedirAction() {
        if ($this->values['permisos']['A']) {

            $datos = new TraspasosCab($this->request['TraspasosCab']['IDTraspaso']);
            if ($datos->expide()) {
                $datos = new TraspasosCab($this->request['TraspasosCab']['IDTraspaso']);
                $this->values['datos'] = $datos;
                unset($datos);
            } else {
                $this->values['errores'] = $datos->getErrores();
                $this->values['alertas'] = $datos->getAlertas();
            }
            return $this->listAction($this->request['TraspasosCab']['IDTraspaso'], '0');
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    public function recepcionarAction() {
        if ($this->values['permisos']['A']) {

            $datos = new TraspasosCab($this->request['TraspasosCab']['IDTraspaso']);
            $datos->recepciona();
            $this->values['errores'] = $datos->getErrores();
            $this->values['alertas'] = $datos->getAlertas();

            $datos = new TraspasosCab($this->request['TraspasosCab']['IDTraspaso']);

            $this->values['datos'] = $datos;
            unset($datos);
            return $this->listAction($this->request['TraspasosCab']['IDTraspaso'], '1');
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

}

?>