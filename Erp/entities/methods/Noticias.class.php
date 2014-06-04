<?php

/**
 * Description of Noticias
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Noticias extends NoticiasEntity {

    public function __toString() {
        return $this->getNoticia();
    }

}

?>
