<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 16.01.2012 23:56:05
 */

/**
 * @orm:Entity(favoritos)
 */
class Favoritos extends FavoritosEntity {

    public function __toString() {
        return $this->getId();
    }

}

?>