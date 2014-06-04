<?php

/**
 * Description of ClientesContactos
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class ClientesContactos extends ClientesContactosEntity {

    public function __toString() {
        return $this->getNombre();
    }

}

?>
