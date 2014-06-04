<?php

/**
 * Description of TiposVenta
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class TiposVenta extends TiposVentaEntity {

    public function __toString() {
        return $this->getTipoVenta();
    }

}

?>
