<?php

/**
 * CONTROLLER FOR BancosOficinas
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 07.06.2011 19:41:32

 * Extiende a la clase controller
 */

class BancosOficinasController extends Controller {

    protected $entity = "BancosOficinas";
    protected $parentEntity = "Bancos";

    /**
     * Devuelve todas las oficinas de un Banco
     * indicado en la posicion 2 del request.
     * @return array
     */
    public function listAction($idBanco='') {

        if ($idBanco == '')
            $idBanco = $this->request[2];

        $tabla = $this->form->getDataBaseName() . "." . $this->form->getTable();
        $filtro = $tabla . ".IDBanco='" . $this->request[2] . "'";

        $this->values['linkBy']['value'] = $idBanco;

        return parent::listAction($filtro);
    }

    /**
     * Devuelve todas las oficinas de un Banco
     * indicado en la posicion 2 del request.
     * @return array
     */
    public function listadoAction($idOpcion='') {
        $this->listado->filter['columnSelected'] = $this->form->getLinkBy();
        $this->listado->filter['value'] = $this->request[2];
        $this->values['linkBy']['value'] = $this->request[2];

        return parent::listadoAction();
    }
}

?>