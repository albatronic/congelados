<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 31.05.2012 23:27:24
 */

/**
 * @orm:Entity(inventarios_lineas)
 */
class InventariosLineas extends InventariosLineasEntity {

    protected $_flagAgrupa = false;
    protected $_idLineaNueva = 0;

    public function __toString() {
        return $this->getIDLinea();
    }

    public function create() {

        if ($this->_flagAgrupa) {
            $this->IDLinea = $this->_idLineaNueva;
            return parent::save ();
        } else
            return parent::create ();
    }

    public function save() {

        if ($this->_flagAgrupa) {
            if ($this->IDLinea != $this->_idLineaNueva)
                $this->erase();
            $this->IDLinea = $this->_idLineaNueva;
        }

        parent::save();
    }

    /**
     * Realiza validaciones logicas antes de crear/actualizar el objeto:
     * 
     * 1.- Comprueba la existencia del lote y ubicación (si procede)
     * 2.- Agrupa con código de artículo, lote ubicación
     */
    public function validaLogico() {

        // Control Lote y Ubicación
        $articulo = new Articulos($this->IDArticulo);
        if ($articulo->getStatus()) {
            $trazabilidad = ($articulo->getTrazabilidad()->getIDTipo() == 1);
            $this->Descripcion = $articulo->getDescripcion();
        } else {
            $this->_errores[] = "El artículo no existe";
        }
        unset($articulo);

        $inventario = new InventariosCab($this->IDInventario);
        $controlUbicaciones = ($inventario->getIDAlmacen()->getControlUbicaciones()->getIDTipo() == 1);
        unset($inventario);

        if ($trazabilidad) {
            if ($this->IDLote == 0)
                $this->_errores[] = "Debe indicar un lote";
            else {
                // Comprobar que el lote pertenezca al artículo
                $lote = new Lotes($this->IDLote);
                if ($lote->getIDArticulo()->getIDArticulo() != $this->IDArticulo)
                    $this->_errores[] = "El lote no pertenece al artículo";
                unset($lote);
            }
        }

        if (($controlUbicaciones) and ($this->IDUbicacion == 0))
            $this->_errores[] = "Debe indicar la ubicación";

        // Agrupar en la misma línea de inventario las entradas
        // que coinciden en el código de articulo, lote y ubicación
        $rows = $this->cargaCondicion("*", "IDArticulo='{$this->IDArticulo}' and IDLote='{$this->IDLote}' and IDUbicacion='{$this->IDUbicacion}' and IDInventario='{$this->IDInventario}'");
        if (count($rows)) {
            $this->_idLineaNueva = $rows[0]['IDLinea'];
            $this->Stock += $rows[0]['Stock'];
            $this->Cajas += $rows[0]['Cajas'];
            $this->Pales += $rows[0]['Pales'];

            $this->_flagAgrupa = true;
        }
    }

}

?>