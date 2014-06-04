<?php

/**
 * Description of ClientesGrupos
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class ClientesGrupos extends ClientesGruposEntity {

    public function __toString() {
        if ($this->getGrupo() != '')
            return $this->getGrupo();
        else
            return "";
    }

}

?>
