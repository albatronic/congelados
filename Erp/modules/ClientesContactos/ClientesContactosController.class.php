<?php

/**
 * CONTROLLER FOR ClientesContactos
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 07.06.2011 00:45:14

 * Extiende a la clase controller
 */

class ClientesContactosController extends Controller {

    protected $entity = "ClientesContactos";
    protected $parentEntity = "Clientes";

    /**
     * Devuelve todas las personas de contacto del cliente
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
        $filtro = $tabla . ".IDCliente='" . $this->request[2] . "'";

        $this->values['linkBy']['value'] = $idCliente;

        return parent::listAction($filtro);
    }

}

?>