<?php

/**
 * CONTROLLER FOR CajaLineas
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 15.04.2012 00:44:20

 * Extiende a la clase controller
 */
class CajaLineasController extends Controller {

    protected $entity = "CajaLineas";
    protected $parentEntity = "CajaArqueos";

    public function __construct($request) {
        // Cargar lo que viene en el request
        $this->request = $request;

        // Cargar la configuracion del modulo (modules/moduloName/config.yaml)
        $this->form = new Form($this->entity);

        $this->permisos = new ControlAcceso();
        $this->values['permisos'] = $this->permisos->getPermisos();
        $this->values['request'] = $this->request;
        $this->values['linkBy'] = array(
            'id' => 'IDArqueo',
            'value' => '',
        );
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
                        $this->values['errores'] = $datos->getErrores();

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

    public function listAction($idArqueo='') {

        if ($this->values['permisos']['C']) {
            if ($idArqueo == '')
                $idArqueo = $this->request[2];

            $arqueo = new $this->parentEntity($idArqueo);
            if ($arqueo->getCajaCerrada()->getIDTipo() == '0') {
                // Si la caja está abierta, puedo hacer apuntes manuales y
                // por lo tanto le añado un objeto linea vacío
                $objetoNuevo = new $this->entity();
                $objetoNuevo->setIDArqueo($idArqueo);
                $objetoNuevo->setIDAgente($_SESSION['USER']['user']['id']);
                $lineas[] = $objetoNuevo;
                unset($objetoNuevo);
            }

            $lis = new $this->entity();
            $rows = $lis->cargaCondicion('IDApunte', "IDArqueo = '" . $idArqueo . "'", 'IDApunte ASC');
            foreach ($rows as $row) {
                $lineas[] = new $this->entity($row['IDApunte']);
            }

            $template = $this->entity . '/form.html.twig';

            $this->values['linkBy']['value'] = $idArqueo;
            $this->values['listado']['data'] = $lineas;

            unset($arqueo);
            unset($lis);
            unset($lineas);

            return array('template' => $template, 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

}

?>