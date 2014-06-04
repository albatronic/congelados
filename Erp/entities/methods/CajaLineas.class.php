<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 15.04.2012 00:44:20
 */

/**
 * @orm:Entity(caja_lineas)
 */
class CajaLineas extends CajaLineasEntity {

    public function __toString() {
        return $this->getIDApunte();
    }

    public function getDia() {

        $fecha = new Fecha(substr($this->Fecha, 0, 10));
        return $fecha->getddmmaaaa();
    }

    public function getHora() {
        return substr($this->Fecha, -8);
    }

    /**
     * Despues de crear la línea, actualizo el arqueo
     *
     * @return boolean
     */
    public function create() {

        $ok = parent::create();

        if ($ok) {
            $this->getIDArqueo()->save();
            $this->setIDArqueo($this->IDArqueo->getIDArqueo());
        }

        return $ok;
    }

    /**
     * Despues de borrar la línea, actualizo el arqueo
     */
    public function erase() {
        if (parent::erase()) {
            $this->getIDArqueo()->save();
            $this->setIDArqueo($this->IDArqueo->getIDArqueo());
        }
    }

    public function validaLogico() {
        $this->setFecha(date('Y-m-d H:i:s'));
    }

    /**
     * Devuelve el numero de documento que ha generado el apunte de caja
     * @return string El numero de documento
     */
    public function getNumeroDocumento() {

        $numeroDocumento = "";

        $documento = $this->getDocumento();
        if ($documento)
            $numeroDocumento = $documento->getNumeroDocumento();

        unset($documento);
        return $numeroDocumento;
    }

    /**
     * Devuelve el objeto que ha generado el apunte de caja
     * @return objeto El objeto
     */
    public function getDocumento() {

        $documento = NULL;

        if (($this->Entidad != '') and ($this->IDEntidad != '')) {
            $documento = new $this->Entidad($this->IDEntidad);
        }

        return $documento;
    }

    /**
     * Devuelve el texto que hay en la columna 'IDRemesa' de
     * las entidades 'RecibosClientes' o 'RecibosProveedores' asociada
     * al apunte de caja.
     * 
     * @return string El literal de la remesa
     */
    public function getRemesa() {
        
        $remesa = '';

        if ((($this->Origen == '3') or ($this->Origen == '5')) and ($this->Entidad != '') and ($this->IDEntidad != '')) {
            $recibo = new $this->Entidad($this->IDEntidad);
            $remesa = $recibo->getIDRemesa();
            unset($recibo);
        }

        return $remesa;
    }

}

?>