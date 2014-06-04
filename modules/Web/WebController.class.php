<?php

/**
 * Description of WebController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 28-may-2011
 *
 */
class WebController {

    public function IndexAction() {
        $values = array();
        return array('template' => 'Web/Index.html.twig', 'values' => $values);
    }

}

?>
