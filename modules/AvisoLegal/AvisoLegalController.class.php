<?php

/**
 * Description of AvisoLegalController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 28-may-2011
 *
 */
class AvisoLegalController {

    public function IndexAction() {
        $values = array();
        return array('template' => 'AvisoLegal/Index.html.twig', 'values' => $values);
    }

}

?>