<?php

/**
 * CONTROLLER FOR Proveedores
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 07.06.2011 00:45:17

 * Extiende a la clase controller
 */

class ProveedoresController extends Controller {

    protected $entity = "Proveedores";
    protected $parentEntity = "";


    /**
     * Devuelve el template "listVencimientos" con un listado
     * de todos los vencimientos del proveedor en curso
     *
     * El template extiende al popup y está pensado para ser mostrado
     * en una solapa
     *
     * @return array
     */
    public function listVencimientosAction() {

        $idProveedor = $this->request[2];

        $datos = new Proveedores($idProveedor);
        $this->values['recibos'] = $datos->getRecibos();
        unset($datos);

        return array('template' => $this->entity . "/listVencimientos.html.twig", 'values' => $this->values);
    }
}

?>