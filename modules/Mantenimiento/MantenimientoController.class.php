<?php

/**
 * Description of MantenimientoController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 28-may-2011
 *
 */
class MantenimientoController {

    public function IndexAction() {
        $values = array();
        return array('template' => 'Mantenimiento/Index.html.twig', 'values' => $values);
    }

}

?>
