<?php

/**
 * Description of Fabricantes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Fabricantes extends FabricantesEntity {

    public function __toString() {
        return ($this->getFabricante() != '') ? $this->getFabricante() : "";
    }

}

?>
