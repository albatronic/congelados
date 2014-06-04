<?php

/**
 * Description of TraspasosCab
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class TraspasosCab extends TraspasosCabEntity {

    public function __toString() {
        return $this->getIDTraspaso();
    }

    public function create() {

        // Calcular el Numero de traspaso en base al contador
        $contador = new Contadores($this->IDContador);
        $this->setNumeroTraspaso($contador->asignaContador());
        unset($contador);

        return parent::create();
    }

    /**
     * Guarda la informacion (update)
     */
    public function save() {
        $this->recalcula();
        parent::save();
    }

    /**
     * Borra un traspaso y sus líneas
     * Siempre que esté en estado 0 (elaboracion) y no esté traspasado
     */
    public function erase() {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = "DELETE FROM traspasos_cab WHERE `IDTraspaso`='{$this->IDTraspaso}' AND IDEstado='0'";
            if ($this->_em->query($query)) {
                //Borrar líneas de traspasos
                $query = "DELETE FROM traspasos_lineas where `IDTraspaso`='{$this->IDTraspaso}'";
                if (!$this->_em->query($query))
                    $this->_errores = $this->_em->getError();
            } else
                $this->_errores = $this->_em->getError();
            $this->_em->desConecta();
        }
        unset($this->_em);
    }

    public function getNumeroDocumento() {
        return $this->NumeroTraspaso;
    }

    /**
     * Devuelve un array con todos los registros
     * Cada elemento tiene la primarykey y el valor de $column
     */
    public function fetchAll($column="FechaSalida") {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = "SELECT IDTraspaso as Id,$column as Value FROM traspasos_cab ORDER BY $column ASC;";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        return $rows;
    }

    /**
     * Confirma la orden de traspaso, que consiste en:
     *
     *  1.- Reservar mercancía en el registro de existencias sin indicar lote ni ubiación
     *      solo para aquellos artículos que estén sujetos a inventario
     *  2.- Poner la cabecera de la orden y sus líneas de tipo 0 (salida) en estado CONFIRMADO (1)
     *
     */
    public function confirma() {

        // Si no está confirmado
        if ($this->getIDEstado()->getIDTipo() == 0) {

            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "SELECT t1.IDArticulo, t1.IDAlmacen, t1.Unidades, t1.UnidadMedida
                        FROM {$this->_dataBaseName}.traspasos_lineas as t1, articulos as t2
                        WHERE t1.IDTraspaso='{$this->IDTraspaso}'
                            AND t1.IDEstado='0'
                            AND t1.Tipo='0'
                            AND t1.IDArticulo=t2.IDArticulo
                            AND t2.Inventario='1'";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();

            // Quitar las reservas
            $exi = new Existencias();
            foreach ($rows as $row)
                $exi->hazReserva($row['IDAlmacen'], $row['IDArticulo'], $row['Unidades'], $row['UnidadMedida']);
            unset($exi);


            // Marcar como Reservadas las líneas de la orden de traspaso de tipo 0 (salida)
            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "update {$this->_dataBaseName}.traspasos_lineas set IDEstado='1' where IDTraspaso='{$this->IDTraspaso}' and IDEstado='0' and Tipo='0'";
            $em->query($query);
            $em->desConecta();
            unset($em);

            // Confirmar la cabecera de la orden de traspaso
            $this->setIDEstado(1);
            $this->save();
        }
    }

    /**
     * Anula la confirmacion de la orden de traspaso, que consiste en:
     *
     *  1.- Quitar la reserva de mercancia en el registro de existencias sin indicar lote ni ubicacion
     *      solo para aquellos artículos que estén sujetos a inventario
     *  2.- Poner la cabecera de la orden de traspaso y sus lineas en estado PTE. DE CONFIRMAR (0)
     *
     */
    public function anulaConfirmacion() {
        // Si está confirmado
        if ($this->getIDEstado()->getIDTipo() == 1) {

            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "SELECT t1.IDArticulo, t1.IDAlmacen, t1.Unidades, t1.UnidadMedida
                        FROM {$this->_dataBaseName}.traspasos_lineas as t1, articulos as t2
                        WHERE t1.IDTraspaso='{$this->IDTraspaso}'
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

            // Poner en estado de PTE DE CONFIRMAR las líneas de la orden de traspaso de tipo 0 (salida)
            $em = new EntityManager("datos" . $_SESSION['emp']);
            $query = "update {$this->_dataBaseName}.traspasos_lineas set IDEstado='0' where IDTraspaso='{$this->IDTraspaso}' and IDEstado='1' and Tipo='0'";
            $em->query($query);

            // Borrar las eventuales lineas de expedicion
            $query = "delete from {$em->getDataBase()}.expediciones where Entidad='TraspasosCab' and IDEntidad='{$this->IDTraspaso}'";
            $em->query($query);
            $em->desConecta();
            unset($em);

            // Anular la reserva en la cabecera de la orden de traspaso
            $this->setIDEstado(0);
            $this->save();
        }
    }

    /**
     * Expide la orden de traspaso, que consiste en:
     *  1.- Expedir la mercancía indicada en las lineas de traspaso del tipo 0
     *  2.- Poner la cabecera de la orden de traspaso y sus lineas de tipo 0 en estado ENVIADO (2)
     *  3.- Generar la líneas de tipo 1 (las de entrada en el almacén destino) actualizando el 'entrando'
     *
     * @return boolean
     */
    public function expide() {

        if ($this->getIDEstado()->getIDTipo() == 1) {
            // Recorrer cada linea de traspaso del tipo 0
            $lineaTraspaso = new TraspasosLineas();
            $rows = $lineaTraspaso->cargaCondicion("IDLinea", "IDTraspaso='{$this->IDTraspaso}' and Tipo='0' and IDEstado='1'", "IDLinea ASC");
            foreach ($rows as $row) {
                $lineaTraspaso = new TraspasosLineas($row['IDLinea']);
                if (!$lineaTraspaso->expide()) {
                    $this->_errores = $lineaTraspaso->getErrores();
                    break;
                }
            }
            unset($lineaTraspaso);

            if (count($this->_errores) == 0) {
                //Marcar la orden de traspaso como enviada y establecer la fecha de envío
                $this->setIDEstado('2');
                $this->setFechaSalida('');
                $this->save();
            }
        }

        return (count($this->_errores) == 0);
    }

    /**
     * Recepciona la orden de traspaso, que consiste en:
     *  1.- Recepcionar la mercancia indicada en las lineas de traspaso del tipo 1
     *  2.- Poner la cabecera de la orden de traspaso y sus lineas en estado RECIBIDO (2)
     *
     * @param string $incidencias Posibles incidencias en la recepcion
     * @return boolean
     */
    public function recepciona($incidencias) {

        if ($this->getIDEstado()->getIDTipo() == '2') {
            // Recorrer cada linea de traspaso del tipo 1
            $lineaTraspaso = new TraspasosLineas();
            $rows = $lineaTraspaso->cargaCondicion("IDLinea", "IDTraspaso='{$this->IDTraspaso}' and Tipo='1' and IDEstado='0'", "IDLinea ASC");
            foreach ($rows as $row) {
                $lineaTraspaso = new TraspasosLineas($row['IDLinea']);
                if (!$lineaTraspaso->recepciona()) {
                    $this->_errores = $lineaTraspaso->getErrores();
                    break;
                }
            }
            unset($lineaTraspaso);

            if (count($this->_errores) == 0) {
                //Marcar la orden de traspaso como recepcionada
                $this->setIDEstado('3');
                $this->setFechaEntrada('');
                $this->setIncidencias($incidencias);
                $this->save();
            }
        }

        return (count($this->_errores) == 0);
    }

    /**
     * Recalcula los importes de la orden de traspaso en base a sus lineas
     * Actualiza las propiedades de totales pero no salva los datos.
     * Fuerza los almacenes de las lineas a los almacenes de la cabecera.
     * IMPORTANTE: Para que los calculos tomen efecto hay que llamar al método save()
     */
    public function recalcula() {

        //Fuerzo el almacen de las líneas de traspaso al de la cabecera de la orden de traspaso
        //por si se ha cambiado el almacen, siempre y cuando el estado de la linea sea 0
        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "UPDATE {$this->_dataBaseName}.traspasos_lineas SET `IDAlmacen`='{$this->IDAlmacenOrigen}' WHERE `IDTraspaso` = '{$this->IDTraspaso}' and `Tipo` = '0' and `IDEstado` = '0' and IDAlmacen <> '{$this->IDAlmacenOrigen}'";
            $this->_em->query($query);

            $query = "UPDATE {$this->_dataBaseName}.traspasos_lineas SET `IDAlmacen`='{$this->IDAlmacenDestino}' WHERE `IDTraspaso` = '{$this->IDTraspaso}' and `Tipo` = '1' and `IDEstado` = '0' and IDAlmacen <> '{$this->IDAlmacenDestino}'";
            $this->_em->query($query);
        }
        $this->_em->desConecta();

        //Calcular los totales
        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "select sum(articulos.Peso*traspasos_lineas.Unidades) as Peso,
                sum(articulos.volumen*traspasos_lineas.Unidades) as Volumen,
                sum(Unidades) as Bultos,
                sum(articulos.Pmc*traspasos_lineas.Unidades) as Costo
                from articulos,traspasos_lineas
                where (traspasos_lineas.IDArticulo=articulos.IDArticulo) and (articulos.Inventario='1') and (traspasos_lineas.IDTraspaso='{$this->IDTraspaso}')";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);

            $this->setPeso($row[0]['Peso']);
            $this->setVolumen($row[0]['Volumen']);
            $this->setBultos($row[0]['Bultos']);
            $this->setTotalCosto($row[0]['Costo']);
        }
    }

    /**
     * Hace una copia del parte de traspaso
     * Genera otro parte en base al actual.
     *
     * @return integer El id del parte generado
     */
    public function duplica() {

        $idOrigen = $this->IDTraspaso;

        // Calcular el Numero de traspaso en base al contador
        $contador = new Contadores($this->IDContador);
        $this->setNumeroTraspaso($contador->asignaContador());
        unset($contador);

        // Crear la cabecera del parte
        $destino = new TraspasosCab();
        $destino->setIDSucursal($this->IDSucursal);
        $destino->setIDContador($this->IDContador);
        // Calcular el Numero de traspaso en base al contador
        $contador = new Contadores($this->IDContador);
        $destino->setNumeroTraspaso($contador->asignaContador());
        unset($contador);
        $destino->setIDEstado(0);
        $destino->setFechaOrden(date('d-m-Y'));
        $destino->setFechaEntrada('00-00-0000');
        $destino->setDescripcion($this->Descripcion);
        $destino->setIDAlmacenOrigen($this->IDAlmacenOrigen);
        $destino->setIDAlmacenDestino($this->IDAlmacenDestino);
        $idDestino = $destino->create();

        // Crear las líneas de parte
        if ($idDestino) {
            $linea = new TraspasosLineas();
            $rows = $linea->cargaCondicion("*", "IDTraspaso='{$idOrigen}'", "IDLinea ASC");
            unset($linea);
            foreach ($rows as $row) {
                $lineaDestino = new TraspasosLineas();
                $lineaDestino->setIDTraspaso($idDestino);
                $lineaDestino->setTipo($row['Tipo']);
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
            $this->_errores[] = "Hubo un error al duplicar la orden de traspaso.";
        }

        return $idDestino;
    }

}

?>
