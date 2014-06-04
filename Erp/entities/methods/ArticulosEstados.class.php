<?php

/**
 * Description of ArticulosEstados
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class ArticulosEstados extends ArticulosEstadosEntity {

    public function __toString() {
        return $this->getEstado();
    }

}

?>
