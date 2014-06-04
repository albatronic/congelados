<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.02.2012 18:52:41
 */

/**
 * @orm:Entity(parametros)
 */
class Parametros extends ParametrosEntity {

    public function __toString() {
        return $this->getIDParametro();
    }

}

?>