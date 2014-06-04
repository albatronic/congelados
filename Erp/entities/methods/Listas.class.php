<?php

/**
 * Description of Listas
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Listas extends ListasEntity {

    public function __toString() {
        return $this->getDescripcion();
    }

}

?>
