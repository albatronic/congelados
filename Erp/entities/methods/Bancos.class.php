<?php

/**
 * Description of Bancos
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Bancos extends BancosEntity {

    public function __toString() {
        return $this->getBanco();
    }

    /**
     * Borra un banco y sus oficinas
     *
     * @return boolean
     */
    public function erase() {

        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = "DELETE FROM bancos WHERE `IDBanco`='{$this->IDBanco}'";
            if ($this->_em->query($query)) {
                //Borrar oficinas
                $query = "DELETE FROM bancos_oficinas where `IDBanco`='{$this->IDBanco}'";
                if (!$this->_em->query($query))
                    $this->_errores = $this->_em->getError();
            } else
                $this->_errores = $this->_em->getError();
            $this->_em->desConecta();
        }
        unset($this->_em);

        return (count($this->_errores) == 0);
    }

}

?>
