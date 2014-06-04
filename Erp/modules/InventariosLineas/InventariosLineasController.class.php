<?php

/**
 * CONTROLLER FOR InventariosLineas
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 31.05.2012 23:27:24

 * Extiende a la clase controller
 */
class InventariosLineasController extends Controller {

    protected $entity = "InventariosLineas";
    protected $parentEntity = "InventariosCab";

    public function __construct($request) {
        // Cargar lo que viene en el request
        $this->request = $request;

        // Cargar la configuracion del modulo (modules/moduloName/config.yaml)
        $this->form = new Form($this->entity);

        $this->permisos = new ControlAcceso();
        $this->values['permisos'] = $this->permisos->getPermisos();
        $this->values['request'] = $this->request;
        $this->values['linkBy'] = array(
            'id' => 'IDInventario',
            'value' => '',
        );
    }

    public function listAction($idInventario='') {

        if ($this->values['permisos']['C']) {
            if ($idInventario == '')
                $idInventario = $this->request[2];

            $inventario = new $this->parentEntity($idInventario);
            if ($inventario->getCerrado()->getIDTipo() == '0') {
                // Si el Inventario está abierto
                // por lo tanto le añado un objeto linea vacío
                $objetoNuevo = new $this->entity();
                $objetoNuevo->setIDInventario($idInventario);
                $lineas[] = $objetoNuevo;
                unset($objetoNuevo);
            }

            $lis = new $this->entity();
            $rows = $lis->cargaCondicion('IDLinea', "IDInventario = '" . $idInventario . "'", 'IDLinea ASC');
            foreach ($rows as $row) {
                $lineas[] = new $this->entity($row['IDLinea']);
            }

            if ($inventario->getCerrado()->getIDTipo() == '0') {
                $template = $this->entity . '/form.html.twig';
            } else {
                $template = $this->entity . '/list.html.twig';
            }

            $this->values['linkBy']['value'] = $idInventario;
            $this->values['listado']['data'] = $lineas;
            $this->values['idAlmacen'] = $inventario->getIDAlmacen()->getIDAlmacen();
            $this->values['controlUbicaciones'] = ($inventario->getIDAlmacen()->getControlUbicaciones()->getIDTIpo() == '1');

            unset($inventario);
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

                    if ($datos->valida($this->form->getRules())) {
                        $datos->create();
                        $this->values['alertas'] = $datos->getAlertas();
                    } else {
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
                    if ($datos->valida($this->form->getRules())) {
                        $datos->save();
                        $this->values['errores'] = $datos->getErrores();
                        $this->values['alertas'] = $datos->getAlertas();
                    } else {
                        $this->values['errores'] = $datos->getErrores();
                    }
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
                        $this->values['errores'] = array();
                    } else {
                        $this->values['errores'] = $datos->getErrores();
                    }
                    unset($datos);
                    return $this->listAction($this->request[$this->entity]['IDInventario']);
                } else {
                    return array('template' => '_global/forbiden.html.twig');
                }
                break;
        }
    }

}

?>