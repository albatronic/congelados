<?php

/**
 * Description of FormasPago
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class FormasPago extends FormasPagoEntity {

    public function __toString() {
        return $this->getDescripcion();
    }

}

?>
