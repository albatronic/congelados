<?php
/**
 * Definicion de los estados de los traspasos entre almacenes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 19-nov-2011
 *
 */

class EstadosTraspasos extends Tipos {

    protected $tipos = array(
        array('Id' => '0', 'Value' => 'Pte. Confirmar'),
        array('Id' => '1', 'Value' => 'Confirmado'),
        array('Id' => '2', 'Value' => 'Enviado'),
        array('Id' => '3', 'Value' => 'Recibido')
    );
}
?>