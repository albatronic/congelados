<?php
/**
 * Define los estados de las líneas de presupuestos de venta
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 08-02-2012
 *
 */

class EstadosLineasPresupuestos extends Tipos {

    protected $tipos = array(
        array('Id' => '0', 'Value' => 'Pte. Confirmar'),
        array('Id' => '1', 'Value' => 'Confirmado'),
    );
}
?>