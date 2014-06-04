<?php
/**
 * Define los estados de las líneas de pedidos de compra
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 19-nov-2011
 *
 */

class EstadosLineasPedidos extends Tipos {

    protected $tipos = array(
        array('Id' => '0', 'Value' => 'Pte. Confirmar'),
        array('Id' => '1', 'Value' => 'Entrando'),
        array('Id' => '2', 'Value' => 'Recepcionado'),
        array('Id' => '3', 'Value' => 'Facturado'),
    );
}
?>