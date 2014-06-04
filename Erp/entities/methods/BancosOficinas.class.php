<?php

/**
 * Description of BancosOficinas
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class BancosOficinas extends BancosOficinasEntity {

    public function __toString() {
        return $this->getDireccion();
    }

}

?>
