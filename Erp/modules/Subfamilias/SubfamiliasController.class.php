<?php
/**
* CONTROLLER FOR Subfamilias
* @author Sergio Perez <sergio.perez@albatronic.com>
* @copyright INFORMATICA ALBATRONIC SL 
* @since 07.06.2011 00:45:18

* Extiende a la clase controller
*/

class SubfamiliasController extends Controller {

	protected $entity = "Subfamilias";
        protected $parentEntity = "Familias";

    /**
     * Devuelve todas las subfamilias de la familia
     * indicado en la posicion 2 del request.
     * @return array
     */
    public function listAction($idFamilia='') {
        if ($idFamilia == '')
            $idFamilia = $this->request[2];

        $tabla = $this->form->getDataBaseName() . "." . $this->form->getTable();
        $filtro = $tabla . ".IDFamilia='" . $idFamilia . "'";

        $this->values['linkBy']['value'] = $idFamilia;

        return parent::listAction($filtro);
    }
}
?>