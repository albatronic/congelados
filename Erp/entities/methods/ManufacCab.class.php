<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 23.03.2012 13:25:26
 */

/**
 * @orm:Entity(manufac_cab)
 */
class ManufacCab extends ManufacCabEntity {

    public function __toString() {
        return $this->getIDManufac();
    }

    public function getNumeroDocumento() {
        return $this->getIDManufac();
    }

    /**
     * Guarda la informacion (update)
     */
    public function save() {
        $this->recalcula();
        parent::save();
    }

    /**
     * Borra un parte de elaboracion  y sus líneas
     * Siempre que esté en estado 0 (Pte de confirmar)
     *
     * @return boolean
     */
    public function erase() {

        if ($this->IDEstado == 0) {

            $this->conecta();

            if (is_resource($this->_dbLink)) {
                $query = "DELETE FROM {$this->_dataBaseName}.manufac_cab WHERE `IDManufac`='{$this->IDManufac}' AND IDEstado='0'";
                if ($this->_em->query($query)) {
                    //Borrar líneas de elaboracion
                    $query = "DELETE FROM {$this->_dataBaseName}.manufac_lineas where `IDManufac`='{$this->IDManufac}' AND IDEstado='0'";
                    if (!$this->_em->query($query))
                        $this->_errores = $this->_em->getError();
                } else
                    $this->_errores = $this->_em->getError();
                $this->_em->desConecta();
            }
            unset($this->_em);
        }

        return (count($this->_errores) == 0);
    }

    /**
     * Confirma el parte de elaboración, que consiste en:
     *
     *  1.- Reservar mercancía en el registro de existencias sin indicar lote ni ubiación
     *      solo para aquellos artículos que estén sujetos a inventario
     *  2.- Poner la cabecera del parte y sus líneas de tipo 0 (salida) en estado CONFIRMADO (1)
     *
     */
    public function confirma() {

        // Si no está confirmado
        if ($this->getIDEstado()->getIDTipo() == 0) {

            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "SELECT t1.IDArticulo, t1.IDAlmacen, t1.Unidades, t1.UnidadMedida
                        FROM {$this->_dataBaseName}.manufac_lineas as t1, articulos as t2
                        WHERE t1.IDManufac='{$this->IDManufac}'
                            AND t1.IDEstado='0'
                            AND t1.Tipo='0'
                            AND t1.IDArticulo=t2.IDArticulo
                            AND t2.Inventario='1'";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();

            $exi = new Existencias();
            foreach ($rows as $row)
                $exi->hazReserva($row['IDAlmacen'], $row['IDArticulo'], $row['Unidades'], $row['UnidadMedida']);
            unset($exi);

            // Marcar como Reservadas las líneas del parte de elaboración de tipo 0 (salida)
            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "update {$this->_dataBaseName}.manufac_lineas set IDEstado='1' where IDManufac='{$this->IDManufac}' and IDEstado='0' and Tipo='0'";
            $em->query($query);
            $em->desConecta();
            unset($em);

            // Confirmar la cabecera del parte de elaboración
            $this->setIDEstado(1);
            $this->save();
        }
    }

    /**
     * Anula la confirmacion del parte de elaboración, que consiste en:
     *
     *  1.- Quitar la reserva de mercancia en el registro de existencias sin indicar lote ni ubicacion
     *      solo para aquellos artículos que estén sujetos a inventario
     *  2.- Poner la cabecera del parte de elaboración y sus lineas en estado PTE. DE CONFIRMAR (0)
     *
     */
    public function anulaConfirmacion() {
        // Si está confirmado
        if ($this->getIDEstado()->getIDTipo() == 1) {

            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "SELECT t1.IDArticulo, t1.IDAlmacen, t1.Unidades, t1.UnidadMedida
                        FROM {$this->_dataBaseName}.manufac_lineas as t1, articulos as t2
                        WHERE t1.IDManufac='{$this->IDManufac}'
                            AND t1.Tipo='0'
                            AND t1.IDEstado='1'
                            AND t1.IDArticulo=t2.IDArticulo
                            AND t2.Inventario='1'";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();

            // Quitar las reservas
            $exi = new Existencias();
            foreach ($rows as $row)
                $exi->quitaReserva($row['IDAlmacen'], $row['IDArticulo'], $row['Unidades'], $row['UnidadMedida']);
            unset($exi);

            // Poner en estado de PTE DE CONFIRMAR las líneas del parte de elaboración de tipo 0 (salida)
            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "update {$this->_dataBaseName}.manufac_lineas set IDEstado='0' where IDManufac='{$this->IDManufac}' and IDEstado='1' and Tipo='0'";
            $em->query($query);

            // Borrar las eventuales lineas de expedicion
            $query = "delete from {$em->getDataBase()}.expediciones where Entidad='ManufacCab' and IDEntidad='{$this->IDManufac}'";
            $em->query($query);
            $em->desConecta();
            unset($em);

            // Anular la reserva en la cabecera del parte de elaboración
            $this->setIDEstado(0);
            $this->save();
        }
    }

    /**
     * Expide el parte de elaboracion, que consiste en:
     *  1.- Expedir la mercancia indicada en las lineas de elaboracion del tipo 0
     *  2.- Poner la cabecera del parte de elaboración y sus lineas en estado EN ELABORACION (2)
     *
     * @return boolean
     */
    public function expide() {

        if ($this->getIDEstado()->getIDTipo() == 1) {
            // Recorrer cada linea de elaboracion del tipo 0
            $lineaManufac = new ManufacLineas();
            $rows = $lineaManufac->cargaCondicion("IDLinea", "IDManufac='{$this->IDManufac}' and Tipo='0' and IDEstado='1'", "IDLinea ASC");
            foreach ($rows as $row) {
                $lineaManufac = new ManufacLineas($row['IDLinea']);
                if (!$lineaManufac->expide()) {
                    $this->_errores = $lineaManufac->getErrores();
                    break;
                }
            }
            unset($lineaManufac);

            if (count($this->_errores) == 0) {
                //Marcar el parte de elaboración como expedido y establecer la fecha de envío
                $this->setIDEstado('2');
                $this->setFechaEnvio('');
                $this->save();
            }
        }

        return (count($this->_errores) == 0);
    }

    /**
     * Recepciona el parte de elaboracion, que consiste en:
     *  1.- Recepcionar la mercancia indicada en las lineas de elaboracion del tipo 1
     *  2.- Poner la cabecera del parte de elaboracion y sus lineas en estado RECIBIDO (2)
     *
     * @param string $incidencias Posibles incidencias en la recepcion
     * @return boolean
     */
    public function recepciona($incidencias) {

        if ($this->getIDEstado()->getIDTipo() == '2') {
            // Recorrer cada linea de elaboracion del tipo 1
            $lineaManufac = new ManufacLineas();
            $rows = $lineaManufac->cargaCondicion("IDLinea", "IDManufac='{$this->IDManufac}' and Tipo='1' and IDEstado='0'", "IDLinea ASC");
            foreach ($rows as $row) {
                $lineaManufac = new ManufacLineas($row['IDLinea']);
                if (!$lineaManufac->recepciona()) {
                    $this->_errores = $lineaManufac->getErrores();
                    break;
                }
            }
            unset($lineaManufac);

            if (count($this->_errores) == 0) {
                //Marcar el parte de elaboracion como expedido
                $this->setIDEstado('3');
                $this->setFechaEntrega('');
                $this->setIncidencias($incidencias);
                $this->save();
            }
        }

        return (count($this->_errores) == 0);
    }

    /**
     * Recalcula los importes del parte de elaboración en base a sus lineas
     * Actualiza las propiedades de totales pero no salva los datos.
     * Fuerza los almacenes de las lineas a los almacenes de la cabecera.
     * IMPORTANTE: Para que los calculos tomen efecto hay que llamar al método save()
     */
    public function recalcula() {

        //Fuerzo el almacen de las líneas de elaboración al de la cabecera del parte de elaboración
        //por si se ha cambiado el almacen, siempre y cuando el estado de la linea sea 0
        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "UPDATE {$this->_dataBaseName}.manufac_lineas SET `IDAlmacen`='{$this->IDAlmacenOrigen}' WHERE `IDManufac` = '{$this->IDManufac}' and `Tipo` = '0' and `IDEstado` = '0' and IDAlmacen <> '{$this->IDAlmacenOrigen}'";
            $this->_em->query($query);

            $query = "UPDATE {$this->_dataBaseName}.manufac_lineas SET `IDAlmacen`='{$this->IDAlmacenDestino}' WHERE `IDManufac` = '{$this->IDManufac}' and `Tipo` = '1' and `IDEstado` = '0' and IDAlmacen <> '{$this->IDAlmacenDestino}'";
            $this->_em->query($query);
        }
        $this->_em->desConecta();

        //Calcular los totales
        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "select sum(Unidades) as Unidades, sum(Importe) as Importe from {$this->_dataBaseName}.manufac_lineas where (IDManufac='{$this->IDManufac}') group by Tipo order by Tipo";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();

            $this->_em->desConecta();

            $this->setKilosOrigen($rows['0']['Unidades']);
            $this->setImporteOrigen($rows['0']['Importe']);
            $this->setKilosDestino($rows['1']['Unidades']);
            $this->setImporteDestino($rows['1']['Importe']);
            $this->setTotalCoste($this->getImporteOrigen() + $this->getImporteDestino() + $this->getGastosTransporte() + $this->getGastosVarios());
        }
    }

    /**
     * Hace una copia del parte de elaboración
     * Genera otro parte en base al actual.
     * Solo genera las lineas de elaboración enviadas (tipo=0)
     *
     * @return integer El id del parte generado
     */
    public function duplica() {

        $idOrigen = $this->IDManufac;

        // Crear la cabecera del parte
        $destino = new ManufacCab();
        $destino->setIDEstado(0);
        $destino->setFechaOrden(date('d-m-Y'));
        $destino->setFechaEntrega('00-00-0000');
        $destino->setIDElaborador($this->IDElaborador);
        $destino->setDescripcion($this->Descripcion);
        $destino->setIDAlmacenOrigen($this->IDAlmacenOrigen);
        $destino->setIDAlmacenDestino($this->IDAlmacenDestino);
        $idDestino = $destino->create();

        // Crear las líneas de parte
        if ($idDestino) {
            $linea = new ManufacLineas();
            $rows = $linea->cargaCondicion("*", "IDManufac='{$idOrigen}' and Tipo='0'", "IDLinea ASC");
            unset($linea);
            foreach ($rows as $row) {
                $lineaDestino = new ManufacLineas();
                $lineaDestino->setIDManufac($idDestino);
                $lineaDestino->setIDArticulo($row['IDArticulo']);
                $lineaDestino->setDescripcion($row['Descripcion']);
                $lineaDestino->setUnidades($row['Unidades']);
                $lineaDestino->setIDAlmacen($row['IDAlmacen']);
                $lineaDestino->setPrecio($row['Precio']);
                $lineaDestino->setImporte($row['Importe']);
                $lineaDestino->create();
            }
            unset($lineaDestino);
        } else {
            $this->_errores[] = "Hubo un error al duplicar el parte de elaboración.";
        }

        return $idDestino;
    }

}

?>