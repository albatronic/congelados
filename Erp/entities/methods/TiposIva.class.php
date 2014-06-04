<?php

/**
 * Description of TiposIva
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class TiposIva extends TiposIvaEntity {

    public function __toString() {
        return $this->getIva();
    }

}

?>
