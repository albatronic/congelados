<?php

/**
 * Description of RutasVentas
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class RutasVentas extends RutasVentasEntity {

    public function __toString() {
        return $this->getId();
    }

}

?>
