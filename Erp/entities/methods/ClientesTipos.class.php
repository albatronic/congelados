<?php

/**
 * Description of ClientesTipos
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class ClientesTipos extends ClientesTiposEntity {

    public function __toString() {
        return $this->getTipo();
    }

}

?>
