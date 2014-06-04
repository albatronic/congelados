<?php

/**
 * Description of SaasController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 28-may-2011
 *
 */
class SaasController {

    public function IndexAction() {
        $values = array();
        return array('template' => 'Saas/Index.html.twig', 'values' => $values);
    }

}

?>
