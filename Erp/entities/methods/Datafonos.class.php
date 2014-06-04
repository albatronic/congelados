<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 15.04.2012 00:54:30
 */

/**
 * @orm:Entity(datafonos)
 */
class Datafonos extends DatafonosEntity {

    public function __toString() {
        if ($this->Datafono)
            return $this->getDatafono();
        else
            return "";
    }

}

?>