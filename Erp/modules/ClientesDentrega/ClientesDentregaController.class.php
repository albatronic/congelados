<?php

/**
 * CONTROLLER FOR ClientesDentrega
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 07.06.2011 00:45:14

 * Extiende a la clase controller
 */

class ClientesDentregaController extends Controller {

    protected $entity = "ClientesDentrega";
    protected $parentEntity = "Clientes";

    /**
     * Devuelve todas las direcciones de entrega del cliente
     * indicado en el parámetro $idCliente o en su defecto
     * en la posicion 2 del request.
     *
     * @param string Codigo de cliente
     * @return array
     */
    public function listAction($idCliente='') {

        if ($idCliente == '')
            $idCliente = $this->request[2];

        $tabla = $this->form->getDataBaseName() . "." . $this->form->getTable();
        $filtro = $tabla . ".IDCliente='" . $idCliente . "'";

        $this->values['linkBy']['value'] = $idCliente;

        return parent::listAction($filtro);
    }

}

?>