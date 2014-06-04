<?php
/**
 * Description of OrigenesApunteCaja
 *
 * Define los diferentes origenes por los que se provocan apuntes de caja
 * 
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 15-abr-2012
 *
 */

class OrigenesApunteCaja extends Tipos {

    protected $tipos = array(
        array('Id' => '0', 'Value' => 'Apertura'),
        array('Id' => '1', 'Value' => 'Manual'),
        array('Id' => '2', 'Value' => 'Ventas'),
        array('Id' => '3', 'Value' => 'Recibos Ventas'),
        array('Id' => '4', 'Value' => 'Compras'),
        array('Id' => '5', 'Value' => 'Recibos Compras'),
        array('Id' => '6', 'Value' => 'Traspasos'),
        array('Id' => '7', 'Value' => 'Contratos'),
        array('Id' => '8', 'Value' => 'Empeño'),
    );
}
?>
