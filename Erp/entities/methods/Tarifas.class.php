<?php

/**
 * Description of Tarifas
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Tarifas extends TarifasEntity {

    public function __toString() {
        return $this->getDescripcion();
    }

}

?>
