<?php

/**
 * Description of Log
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Log extends LogEntity {

    public function __toString() {
        return $this->getIDEvento();
    }

}

?>
