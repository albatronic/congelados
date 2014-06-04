<?php

/**
 * Description of EstadosManufac
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 22-mar-2012
 *
 */
class EstadosManufac extends Tipos {

    protected $tipos = array(
        array('Id' => '0', 'Value' => 'Pte. Confirmar'),
        array('Id' => '1', 'Value' => 'Confirmado'),
        array('Id' => '2', 'Value' => 'En Elaboracion'),
        array('Id' => '3', 'Value' => 'Elaborado'),
    );

}

?>
