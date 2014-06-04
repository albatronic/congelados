<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 06.12.2011 16:59:34
 */

/**
 * @orm:Entity(tipos_pales)
 */
class TiposPales extends TiposPalesEntity {

    public function __toString() {
        return $this->getDescripcion();
    }

    public function save() {
        $this->setCubicaje($this->cubicar());
        parent::save();
    }

    public function create() {
        $this->setCubicaje($this->cubicar());
        parent::create();
    }

    /**
     * Cubica el palé
     * Las medidas están expresadas en milímetros y el cubicaje
     * se expresa en metros cúbicos
     * @return decimal Los metros cúbicos de volumen del palé
     */
    private function cubicar() {
        return ($this->Alto/1000 * $this->Ancho/1000 * $this->Fondo/1000);
    }
}

?>