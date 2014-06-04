<?php

/**
 * Description of PromocionesClientes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class PromocionesClientes extends PromocionesClientesEntity {

    public function __toString() {
        return $this->getID();
    }

    /**
     * Validacion lógica
     */
    protected function validaLogico() {

        if ($this->IDGrupo != '')
            $this->IDCliente = NULL;
        else
            $this->IDGrupo = NULL;

        if ((!$this->IDGrupo) and (!$this->IDCliente))
            $this->_errores[] = 'Debe indicar un grupo de clientes o un cliente concreto';

        // Comprobar que no existe duplicidad de IDPromocion-IDGrupo
        $promocion = new PromocionesClientes();
        $promos = $promocion->cargaCondicion("Id", "IDPromocion='{$this->IDPromocion}' and IDGrupo='{$this->IDGrupo}'");
        if (count($promos))
            $this->_errores[] = 'Ya existe este grupo en la promoción';
        unset($promocion);
    }

}

?>
