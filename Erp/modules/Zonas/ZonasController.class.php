<?php

/**
 * CONTROLLER FOR Zonas
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 24.08.2011 16:51:13

 * Extiende a la clase controller
 */
class ZonasController extends Controller {

    protected $entity = "Zonas";

    public function listAction() {
        $tabla = $this->form->getDataBaseName() . "." . $this->form->getTable();
        $filtro = $tabla . ".IDSucursal='" . $_SESSION['suc'] . "'";

        return parent::listAction($filtro);
    }

    public function listadoAction() {
        $tabla = $this->form->getDataBaseName() . "." . $this->form->getTable();
        $filtro = $tabla . ".IDSucursal='" . $_SESSION['suc'] . "'";

        return parent::listadoAction($filtro);
    }

}

?>