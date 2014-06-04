<?php

/**
 * CONTROLLER FOR TablaPortes
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 07.06.2011 00:45:18

 * Extiende a la clase controller
 */

class TablaPortesController extends Controller {

    protected $entity = "TablaPortes";
    protected $parentEntity = "Agencias";


    public function __construct($request) {
        // Cargar lo que viene en el request
        $this->request = $request;

        // Cargar la configuracion del modulo (modules/moduloName/config.yaml)
        $this->form = new Form($this->entity);

        $this->permisos = new ControlAcceso();
        $this->values['permisos'] = $this->permisos->getPermisos();
        $this->values['request'] = $this->request;
        $this->values['linkBy'] = array(
            'id' => 'IDAgencia',
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

    public function listAction($idAgencia = '') {

        if ($this->values['permisos']['C']) {
            if ($idAgencia == '')
                $idAgencia = $this->request[2];

            $agencias = new Agencias($idAgencia);
            $zonas = new ZonasTransporte();
            $tramos = new TramosKilos();


            $this->values['linkBy']['value'] = $idAgencia;
            $this->values['tablaPortes'] = $agencias->getTablaPortes();
            $this->values['zonasTransporte'] = $zonas->fetchAll('Zona',false);
            $this->values['tramosKilos'] = $tramos->fetchAll(false);

            unset($agencias);
            unset($zonas);
            unset($tramos);

            return array('template' => $this->entity . '/form.html.twig', 'values' => $this->values);

        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }
}

?>