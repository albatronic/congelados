<?php
/**
 * Define los tipos de promociones
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 19-nov-2011
 *
 */

class PromocionesTipos extends Tipos {

    protected $tipos = array(
        array('Id' => '0', 'Value' => '% Dcto'),
        array('Id' => '1', 'Value' => '% Margen'),
        array('Id' => '2', 'Value' => 'Precio Neto'),
    );
}
?>