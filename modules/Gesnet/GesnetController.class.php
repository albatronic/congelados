<?php

/**
 * Description of GesnetController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 28-may-2011
 *
 */
class GesnetController {

    public function IndexAction() {
        $values = array();
        return array('template' => 'Gesnet/Index.html.twig', 'values' => $values);
    }

}

?>
