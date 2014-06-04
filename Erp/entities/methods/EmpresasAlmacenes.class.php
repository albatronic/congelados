<?php

/**
 * Description of EmpresasAlmacenes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class EmpresasAlmacenes extends EmpresasAlmacenesEntity {

    public function __toString() {
        return $this->getId();
    }

}

?>
