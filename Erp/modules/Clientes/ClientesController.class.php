<?php

/**
 * CONTROLLER FOR Clientes
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 07.06.2011 00:45:14

 * Extiende a la clase controller
 */
class ClientesController extends Controller {

    protected $entity = "Clientes";
    protected $parentEntity = "";
    
    /**
     * Generar el listado de clientes apoyándose en el método padre
     * Si el usuario es comercial muestra solo los suyos.
     *
     * @return array
     */
    public function listAction($aditionalFilter='') {

        $tabla = $this->form->getDataBaseName() . "." . $this->form->getTable();
        $usuario = new Agentes($_SESSION['USER']['user']['id']);
        if ($usuario->getEsComercial()) {
            $aditionalFilter = $tabla . ".IDComercial='" . $usuario->getIDAgente() . "'";
        }
        unset($usuario);

        return parent::listAction($aditionalFilter);
    }

    /**
     * Devuelve el template "listVencimientos" con un listado
     * de todos los vencimientos del cliente en curso
     *
     * El template extiende al popup y está pensado para ser mostrado
     * en una solapa
     *
     * @return array
     */
    public function listVencimientosAction() {

        $idCliente = $this->request[2];

        $datos = new Clientes($idCliente);
        $this->values['recibos'] = $datos->getRecibos();
        unset($datos);

        return array('template' => $this->entity . "/listVencimientos.html.twig", 'values' => $this->values);
    }

    /**
     * Generar el listado de clientes apoyándose en el método padre
     * Si el usuario es comercial muestra solo los suyos.
     *
     * @return array
     */
    public function listadoAction($aditionalFilter='') {

        $tabla = $this->form->getDataBaseName() . "." . $this->form->getTable();
        $usuario = new Agentes($_SESSION['USER']['user']['id']);
        if ($usuario->getEsComercial()) {
            $aditionalFilter = $tabla . ".IDComercial='" . $usuario->getIDAgente() . "'";
        }
        unset($usuario);

        return parent::listadoAction($aditionalFilter);
    }

    /**
     * Establece los parametros de exportacion y se los entrega
     * al método padre del controller principal que es el que reliza
     * el proceso de exportación en base a estos parámetros.
     *
     * @return array
     */
    public function exportarAction($aditionalFilter='') {

        $tabla = $this->form->getDataBaseName() . "." . $this->form->getTable();
        $usuario = new Agentes($_SESSION['USER']['user']['id']);
        if ($usuario->getEsComercial()) {
            $aditionalFilter = $tabla . ".IDComercial='" . $usuario->getIDAgente() . "'";
        }
        unset($usuario);

        $this->values['export'] = array(
            'title' => 'Clientes de la sucursal ' . $_SESSION['suc'],
        );
        return parent::exportarAction($aditionalFilter);
    }

}

?>