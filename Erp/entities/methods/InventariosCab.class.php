<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 31.05.2012 23:22:38
 */

/**
 * @orm:Entity(inventarios_cab)
 */
class InventariosCab extends InventariosCabEntity {

    public function __toString() {
        return $this->IDInventario;
    }

    public function getNumeroDocumento() {
        return $this->IDInventario;
    }

    /**
     * Borra un inventario y sus líneas
     * Siempre que no esté cerrado
     *
     * @return boolean
     */
    public function erase() {

        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = "DELETE FROM {$this->_dataBaseName}.inventarios_cab WHERE `IDInventario`='{$this->IDInventario}' AND Cerrado='0'";
            if ($this->_em->query($query)) {
                //Borrar líneas de inventario
                $query = "DELETE FROM {$this->_dataBaseName}.inventarios_lineas where `IDInventario`='{$this->IDInventario}'";
                if (!$this->_em->query($query))
                    $this->_errores = $this->_em->getError();
            } else
                $this->_errores = $this->_em->getError();
            $this->_em->desConecta();
        }
        unset($this->_em);

        return (count($this->_errores) == 0);
    }

    /**
     * Cierra el inventario.
     * 
     * Consiste en generar los movimientos de almacén necesarios
     * para dejar las existencias de cada articulo/lote/ubicación 
     * según lo indicado en las líneas de inventario
     */
    public function cierra() {

        $ok = true;

        // Si no está cerrado
        if ($this->Cerrado == 0) {

            $lineas = new InventariosLineas();
            $rows = $lineas->cargaCondicion("*", "IDInventario='{$this->IDInventario}'");
            unset($lineas);

            $exi = new Existencias();
            foreach ($rows as $row) {
                // Buscar la existencias que hay para calcular el mvto
                // de almacén de tal forma que se queden como dice el inventario
                $stock = $exi->cargaCondicion("*", "IDAlmacen='{$this->IDAlmacen}' and IDArticulo='{$row['IDArticulo']}' and IDLote='{$row['IDLote']}' and IDUbicacion='{$row['IDUbicacion']}'");

                $valores = array(
                    'UM' => 'UMA',
                    'Reales' => $row['Stock'] - $stock[0]['Reales'],
                    'Pales' => $row['Pales'] - $stock[0]['Pales'],
                    'Cajas' => $row['Cajas'] - $stock[0]['Cajas'],
                    'Reservadas' => 0,
                    'Entrando' => 0,
                );
                /**
                if ($valores['Reales']>=0)
                    $signo = "E";
                else {
                    $signo = "S";
                    $valores['Reales'] = abs($valores['Reales']);
                }
                */
                $signo = "E";
                $mvtoAlmacen = new MvtosAlmacen();
                $ok = $mvtoAlmacen->
                                genera('InventariosCab',
                                        $signo, // Signo del movimiento
                                        $this->IDInventario, // El id del inventario
                                        $this->IDAlmacen, // El id del almacen
                                        $row['IDArticulo'], // El id del articulo
                                        $row['IDLote'], // El id del lote
                                        $row['IDUbicacion'], // El id de la ubicacion
                                        0, // Flag de deposito
                                        $valores); // Valores con los que actualizar
            }
            unset($exi);
            unset($mvtoAlmacen);

            // Marcar el inventario como cerrado
            if ($ok) {
                $this->setCerrado(1);
                $this->save();
            }
        }

        return $ok;
    }

}

?>