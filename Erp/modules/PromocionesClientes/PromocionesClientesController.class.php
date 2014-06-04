<?php

/**
 * CONTROLLER FOR PromocionesClientes
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 07.06.2011 00:45:17

 * Extiende a la clase controller
 */
class PromocionesClientesController extends Controller {

    protected $entity = "PromocionesClientes";
    protected $parentEntity = "Promociones";

    public function __construct($request) {

        parent::__construct($request);

        $grupo = new ClientesGrupos();
        $grupos = $grupo->fetchAll("Grupo");
        $grupos[] = array('Id' => '-1', 'Value' => '*** TODOS LOS GRUPOS ****');
        unset($grupo);

        $this->values['grupos'] = $grupos;
    }

    public function listAction($idPromocion = '') {

        if ($idPromocion == '')
            $idPromocion = $this->request[2];

        $this->listado->filter['columnsSelected'] = array();
        $this->listado->filter['valuesSelected'] = array();

        $tabla = $this->form->getDataBaseName() . "." . $this->form->getTable();
        $filtro = $tabla . ".IDPromocion='" . $idPromocion . "'";

        $this->values['linkBy']['value'] = $idPromocion;

        return parent::listAction($filtro);
    }

    /**
     * Crea un registro nuevo
     *
     * Si viene por GET muestra un template vacio
     * Si viene por POST crea un registro
     *
     * @return array con el template y valores a renderizar
     */
    public function newAction() {

        if ($this->values['permisos']['I']) {
            switch ($this->request["METHOD"]) {
                case 'GET': //MOSTRAR FORMULARIO VACIO
                    //SI EN LA POSICION 2 DEL REQUEST VIENE ALGO,
                    //SE ENTIENDE QUE ES EL VALOR DE LA CLAVE PARA LINKAR CON LA ENTIDAD PADRE
                    //ESTO SE UTILIZA PARA LOS FORMULARIOS PADRE->HIJO
                    if ($this->request['2'] != '')
                        $this->values['linkBy']['value'] = $this->request['2'];

                    $datos = new $this->entity();
                    $this->values['datos'] = $datos;
                    $this->values['errores'] = array();
                    return array('template' => $this->entity . '/new.html.twig', 'values' => $this->values);
                    break;

                case 'POST': //CREAR NUEVO REGISTRO
                    $idPromocion = $this->request[$this->entity][$this->values['linkBy']['id']];

                    //COGER EL LINK A LA ENTIDAD PADRE
                    if ($this->values['linkBy']['id'] != '') {
                        $this->values['linkBy']['value'] = $this->request[$this->entity][$this->values['linkBy']['id']];
                    }

                    $idGrupo = $this->request['PromocionesClientes']['IDGrupo'];
                    if ($idGrupo == -1) {
                        // Todos los grupos. Hay que crear la promocion para todos los grupos
                        $grupo = new ClientesGrupos();
                        $grupos = $grupo->cargaCondicion("IDGrupo","1","Grupo ASC");
                        foreach ($grupos as $value) {
                            $promo = new PromocionesClientes();
                            $promo->setIDPromocion($idPromocion);
                            $promo->setIDGrupo($value['IDGrupo']);
                            if ($promo->valida($this->form->getRules()))
                            $promo->create();
                        }
                        unset($promo);
                        unset($grupo);

                        return $this->listAction($idPromocion);
                    } else {
                        $datos = new $this->entity();
                        $datos->bind($this->request[$this->entity]);

                        if ($datos->valida($this->form->getRules())) {
                            $datos->create();
                            $this->values['errores'] = $datos->getErrores();
                            $this->values['alertas'] = $datos->getAlertas();

                            //Recargo el objeto para refrescar las propiedas que
                            //hayan podido ser objeto de algun calculo durante el proceso
                            //de guardado.
                            $datos = new $this->entity($datos->getPrimaryKeyValue());
                            $this->values['datos'] = $datos;

                            if ($this->values['errores']) {
                                return array('template' => $this->entity . '/new.html.twig', 'values' => $this->values);
                            } else {
                                return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
                            }
                        } else {
                            $this->values['datos'] = $datos;
                            $this->values['errores'] = $datos->getErrores();
                            $this->values['alertas'] = $datos->getAlertas();
                            return array('template' => $this->entity . '/new.html.twig', 'values' => $this->values);
                        }
                    }
                    break;
            }
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

}

?>