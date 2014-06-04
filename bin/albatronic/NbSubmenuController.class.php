<?php
/**
 * Description of NbSubmenuController
 *
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 07.06.2011 19:41:33

 * Extiende a la clase controller
 */

class NbSubmenuController extends Controller {

    protected $entity = "Submenu";
    protected $parentEntity = "Menu";

    /**
     * Devuelve todas las subopciones de menu de la
     * opcion indicada en la posicion 2 del request.
     * @return array
     */
    public function listAction($idOpcion='') {

        if ($idOpcion == '')
            $idOpcion = $this->request[2];

        $tabla = $this->form->getDataBaseName() . "." . $this->form->getTable();
        $filtro = $tabla . ".IDOpcion='" . $idOpcion . "'";

        $this->values['linkBy']['value'] = $idOpcion;

        return parent::listAction($filtro);
    }

}

?>
