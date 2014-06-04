<?php

/**
 * Description of FemitidasLineas
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class FemitidasLineas extends FemitidasLineasEntity {

    public function __toString() {
        return $this->getIDLinea();
    }

    /**
     * Devuelve separados por guión los lotes servidos en la línea de factura
     * que a su vez viene de una línea de albarán
     *
     * @return string Los lotes servidos
     */
    public function getLotes() {

        return $this->getIDLineaAlbaran()->getLotes();
    }
}

?>
