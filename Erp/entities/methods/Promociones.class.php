<?php

/**
 * Description of Promociones
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Promociones extends PromocionesEntity {

    public function __toString() {
        return $this->getTitulo();
    }

    protected function validaLogico() {
        if ($this->IDFamilia != '')
            $this->IDArticulo = NULL;
        if (($this->IDFamilia == '') and ($this->IDArticulo == ''))
            $this->_errores[] = 'Debe indicar una familia o un articulo concreto';
    }

}

?>
