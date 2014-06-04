<?php
/**
 * CONTROLLER FOR NbMenu
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 07.06.2011 19:41:33

 * Extiende a la clase controller
 *
 */

include "entities/methods/Menu.class.php";

class NbMenuController extends Controller {

    protected $entity = "Menu";
    protected $parentEntity = "";

    /**
     * Genera el listado de opciones de menu y submenu
     * Utiliza el metodo listadoAction() del controller SubmenuController
     * @return array Template y valores
     */
    public function listadoAction() {
        $listadoController = new SubmenuController($this->request);
        return $listadoController->listadoAction();
    }
}
?>
