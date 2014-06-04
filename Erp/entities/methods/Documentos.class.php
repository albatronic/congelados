<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 16.04.2012 00:18:47
 */

/**
 * @orm:Entity(documentos)
 */
class Documentos extends DocumentosEntity {

    public function __toString() {
        return $this->getId();
    }

    /**
     * Antes de crear, calcula la clave en md5
     * @return boolean
     */
    public function create() {
        $this->Clave = md5($this->PathName);
        
        return parent::create();
    }

}

?>