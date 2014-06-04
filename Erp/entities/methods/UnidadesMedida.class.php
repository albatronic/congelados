<?php

/**
 * Description of UnidadesMedida
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class UnidadesMedida extends UnidadesMedidaEntity {

    public function __toString() {
        return $this->getUnidadMedida();
    }

}

?>
