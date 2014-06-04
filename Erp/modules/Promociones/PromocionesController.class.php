<?php

/**
 * CONTROLLER FOR Promociones
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 07.06.2011 00:45:17

 * Extiende a la clase controller
 */
include "modules/PromocionesClientes/PromocionesClientesController.class.php";

class PromocionesController extends Controller {

    protected $entity = "Promociones";
    protected $parentEntity = "";

    public function __construct($request) {

        parent::__construct($request);

        $formaPago = new FormasPago();
        $formasPago = $formaPago->fetchAll("Descripcion");
        $formasPago[] = array('Id' => '0', 'Value' => '*** TODAS LAS FORMAS DE PAGO ****');
        unset($formaPago);

        $this->values['formasPago'] = $formasPago;
    }

    /**
     * Genera el listado apoyandose en el metodo de PromocionesClientesController
     * @param string $aditionalFilter
     * @return array Tempalete y valores
     */
    public function listadoAction($aditionalFilter = '') {
        $listadoController = new PromocionesClientesController($this->request);
        return $listadoController->listadoAction();
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
                        $lineasAlbaranes = new AlbaranesLineas();
                        $promoAplicada = $lineasAlbaranes->cargaCondicion("IDPromocion", "IDPromocion='{$datos->getIDPromocion()}'");
                        $this->values['promoAplicada'] = count($promoAplicada);
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
                            $datos = new $this->entity();
                            $datos->bind($this->request[$this->entity]);
                            if ($datos->valida($this->form->getRules())) {
                                $this->values['alertas'] = $datos->getAlertas();
                                $datos->save();

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
                            $datos = new $this->entity();
                            $datos->bind($this->request[$this->entity]);

                            if ($datos->erase()) {
                                $datos = new $this->entity();
                                $this->values['datos'] = $datos;
                                $this->values['errores'] = array();
                                return array('template' => $this->entity . '/new.html.twig', 'values' => $this->values);
                            } else {
                                $this->values['datos'] = $datos;
                                $this->values['errores'] = $datos->getErrores();
                                $this->values['alertas'] = $datos->getAlertas();
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