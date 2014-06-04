<?php

/**
 * Description of Provincias
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Provincias extends ProvinciasEntity {

    public function __toString() {
        return $this->getProvincia();
    }

}

?>
