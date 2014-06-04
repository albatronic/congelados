<?php

/**
 * Description of Familias
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Familias extends FamiliasEntity {

    public function __toString() {
        return $this->getFamilia();
    }

    /**
     * Antes de guardar la familia, actualizo sus articulos
     * haciendo que hereden ciertas características.
     */
    public function save() {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = "update articulos set
            Inventario='{$this->Inventario}',
            Trazabilidad='{$this->Trazabilidad}',
            PublicarWeb='{$this->PublicarWeb}',
            ConTallas='{$this->ConTallas}'                ,
            ConColores='{$this->ConColores}'
            where IDFamilia='{$this->IDFamilia}';";
            $this->_em->query($query);
            $this->_em->desConecta();
        }
        unset($this->_em);

        parent::save();
    }

}

?>
