<?php

/**
 * Description of Permisos
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Permisos extends PermisosEntity {

    public function __toString() {
        return $this->getPermisos();
    }

}

?>
