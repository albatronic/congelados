<?php

/**
 * Description of Etiquetas
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Etiquetas extends EtiquetasEntity {

    public function __toString() {
        return $this->getDescripcion();
    }

}

?>
