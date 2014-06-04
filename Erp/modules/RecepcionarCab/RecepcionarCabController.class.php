<?php

/**
 * CONTROLLER FOR RecepcionarCab
 *
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 26.07.2011 00:45:13

 * Extiende a la clase controller
 */
class RecepcionarCabController extends Controller {

    protected $entity = "RecepcionarCab";
    protected $parentEntity = "";

    public function __construct($request) {

        // Orígenes de recepción
        $this->values['tipos'] = array(
            array('Id' => 'PedidosCab', 'Value' => 'Pedidos de Compra'),
            array('Id' => 'ManufacCab', 'Value' => 'Elaboraciones'),
            array('Id' => 'TraspasosCab', 'Value' => 'Traspasos'),
        );

        parent::__construct($request);
    }

    /**
     * Generar un listado con los objetos origen de recepción pendientes de recepcionar
     * para el almacen indicado.
     *
     * @return array
     */
    public function listAction() {

        if ($this->values['permisos']['C']) {

            switch ($this->request['idTipo']) {
                case 'PedidosCab':
                    $data = $this->getPedidos();
                    $template = $this->entity . '/listPedidos.html.twig';
                    break;
                case 'ManufacCab':
                    $data = $this->getElaboraciones();
                    $template = $this->entity . '/listManufac.html.twig';
                    break;
                case 'TraspasosCab':
                    $data = $this->getTraspasos();
                    $template = $this->entity . '/listTraspasos.html.twig';
                    break;
                default:
                    $data = array();
            }

            $this->values['listado']['data'] = $data;
            $this->values['listado']['filter'] = array(
                'idAlmacen' => $this->request['idAlmacen'],
                'idTipo' => $this->request['idTipo'],
                'idPedido' => $this->request['idPedido'],
                'idProveedor' => $this->request['idProveedor'],

            );
            unset($data);
        } else {
            $template = "_global/forbiden.html.twig";
        }

        return array('template' => $template, 'values' => $this->values);
    }

    /**
     * Devuelve un array de objetos PedidosCab con los pedidos
     * que están en estado Confirmado (1) y cuyo destino es el
     * almacén seleccionado
     *
     * @return array Array de objetos PedidosCab
     */
    private function getPedidos() {

        $data = array();

        $pedido = new PedidosCab();
        $filtro = "IDEstado='1' and IDAlmacen='{$this->request['idAlmacen']}'";
        $rows = $pedido->cargaCondicion('IDPedido', $filtro, 'FechaEntrega ASC');
        unset($pedido);

        foreach ($rows as $row) {
            $data[] = new PedidosCab($row['IDPedido']);
        }

        return $data;
    }

    /**
     * Devuelve un array de objetos ManufacCab con las elaboraciones
     * que están en estado En Elaboración (2) y cuyo almacén destino es
     * el seleccionado
     *
     * @return array Array de objetos ManufacCab
     */
    private function getElaboraciones() {

        $data = array();

        $manufac = new ManufacCab();
        $filtro = "IDEstado='2' and IDAlmacenDestino='{$this->request['idAlmacen']}'";
        $rows = $manufac->cargaCondicion('IDManufac', $filtro, 'FechaOrden ASC');
        unset($manufac);

        foreach ($rows as $row) {
            $data[] = new ManufacCab($row['IDManufac']);
        }

        return $data;
    }

    /**
     * Devuelve un array de objetos TraspasosCab con los traspasos
     * que están en estado Enviado (2) y cuyo almacén destino es
     * el seleccionado
     *
     * @return array Array de objetos TraspasosCab
     */
    private function getTraspasos() {

        $data = array();

        $traspaso = new TraspasosCab();
        $filtro = "IDEstado='2' and IDAlmacenDestino='{$this->request['idAlmacen']}'";
        $rows = $traspaso->cargaCondicion('IDTraspaso', $filtro, 'FechaOrden ASC');
        unset($traspaso);

        foreach ($rows as $row) {
            $data[] = new TraspasosCab($row['IDTraspaso']);
        }

        return $data;
    }

}

?>