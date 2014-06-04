<?php

/**
 * Description of RecibosClientes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class RecibosClientes extends RecibosClientesEntity {

    public function __toString() {
        return $this->getIDRecibo();
    }

    /**
     * Devuelve el id del arqueo en donde está anotado
     * el cobro del recibo en curso
     * 
     * @return integer El id del arqueo
     */
    public function getIDArqueo() {
        
        $arqueo = new CajaLineas();
        $rows = $arqueo->cargaCondicion("IDArqueo","Entidad='RecibosClientes' AND IDEntidad='{$this->IDRecibo}'");
        unset($arqueo);
        
        return $rows[0]['IDArqueo'];
    }
}

?>
