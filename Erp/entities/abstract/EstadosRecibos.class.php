<?php
/**
 * Define los estados de los Recibos de clientes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 19-nov-2011
 *
 */

class EstadosRecibos extends Tipos {

    protected $tipos = array(
        array('Id' => '0', 'Value' => 'Pte Cobro'),
        array('Id' => '1', 'Value' => 'En cartera'),
        array('Id' => '2', 'Value' => 'Descontado'),
        array('Id' => '3', 'Value' => 'Gestion Cobro'),
        array('Id' => '4', 'Value' => 'Endosado'),
        array('Id' => '5', 'Value' => 'Impagado'),
        array('Id' => '6', 'Value' => 'Cobrado'),
        array('Id' => '7', 'Value' => 'Dudoso cobro'),
    );
}
?>
