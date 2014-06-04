<?php

/**
 * CONTROLLER FOR Bancos
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 07.06.2011 19:41:32

 * Extiende a la clase controller
 */

include "modules/BancosOficinas/BancosOficinasController.class.php";

class BancosController extends Controller {

    protected $entity = "Bancos";
    protected $parentEntity = "";

    public function listadoAction() {
        $listadoController = new BancosOficinasController($this->request);
        return $listadoController->listadoAction();
    }
}

?>