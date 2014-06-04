<?php

/**
 * CONTROLLER FOR Familias
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 07.06.2011 00:45:15

 * Extiende a la clase controller
 */

include "modules/Subfamilias/SubfamiliasController.class.php";

class FamiliasController extends Controller {

    protected $entity = "Familias";
    protected $parentEntity = "";

    /**
     * Genera el listado apoyandose en el metodo de SbufamiliasController
     * @param string $aditionalFilter
     * @return array Tempalete y valores
     */
    public function listadoAction($aditionalFilter = '') {
        $listadoController = new SubfamiliasController($this->request);
        return $listadoController->listadoAction();
    }

}


?>