<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 20.02.2012 00:11:14
 */

/**
 * @orm:Entity(expediciones)
 */
class Expediciones extends ExpedicionesEntity {

    public function __toString() {
        return $this->getIDLinea();
    }

    /**
     * Valida que la cantidad indicada esté disponible en
     * el almacén, ubicación y lote
     * 
     * @return boolean
     */
    public function validaLogico() {

        $articulo = new Articulos($this->IDArticulo);
        $bloqueoStock = ($articulo->getBloqueoStock()->getIDTIpo() == '1');

        if ($this->Unidades == 0)
            $this->_errores[] = "Debe indicar una cantidad distinta a cero";

        $almacen = new Almacenes($this->IDAlmacen);
        if (($almacen->getControlUbicaciones()->getIDTipo() == '1') and ($this->IDUbicacion == 0)) {
            $this->_errores[] = "Debe indicar la ubicación";
        }
        unset($almacen);

        if ($bloqueoStock) {

            $udadMedidaVenta = $articulo->getUMV();
            $udadMedidaAlmacen = $articulo->getUMA();

            $stock = new Existencias();
            $existencias = $stock->getStock($this->IDArticulo, $this->IDAlmacen, $this->IDLote, $this->IDUbicacion);
            unset($stock);

            // Calcular las cantidades de ese articulo, almacen, lote y ubicación que estan
            // en lineas de expediciones sin estar expedidas aún. O sea lo que está en
            // proceso de expedición. No tengo en cuenta los valores de la BD de la línea actual
            // porque puede que hayan cambiado.
            $filtro = "IDLinea<>'{$this->IDLinea}' and Expedida='0' and IDAlmacen='{$this->IDAlmacen}' and IDLote='{$this->IDLote}' and IDUbicacion='{$this->IDUbicacion}'";
            $expedicion = new Expediciones();
            $rows = $expedicion->cargaCondicion("sum(Unidades) as Unidades,sum(Pales) as Pales,sum(Cajas) as Cajas", $filtro);
            $row = $rows[0];
            $row['Unidades'] += $this->Unidades;
            $row['Pales'] += $this->Pales;
            $row['Cajas'] += $this->Cajas;

            unset($expedicion);


            if ($existencias['DI'] < $articulo->convertUnit('UMV', 'UMA', $row['Unidades']))
                $this->_errores[] = "Hay " . round($articulo->convertUnit('UMA', 'UMV', $existencias['DI']), 2) . " {$udadMedidaVenta} (" . round($existencias['DI'], 2) . " {$udadMedidaAlmacen}) disponibles para ese lote y ubicación";
            if ($existencias['CA'] < $row['Cajas'])
                $this->_errores[] = "Hay {$existencias['CA']} cajas disponibles para ese lote y ubicacion";
            if ($existencias['PT'] < $row['Pales'])
                $this->_errores[] = "Hay {$existencias['PT']} pales disponibles para ese lote y ubicacion";
        }

        unset($articulo);

        return (count($this->_errores) == 0);
    }

    /**
     * Expide todas las líneas de expedición correspondientes
     * a la entidad $entidad y línea de entidad $idLineaEntidad
     *
     * @param string $entidad La entidad padre (AlbaranesCab,ManufacCab,TraspasosCab)
     * @param integer $idLineaEntidad El id de la línea de la entidad padre
     * @return float La cantidad de unidades expedidas
     */
    public function expide($entidad, $idLineaEntidad) {

        $unidadesExpedidas = 0;

        $rows = $this->cargaCondicion("*", "Entidad='{$entidad}' and IDLineaEntidad='{$idLineaEntidad}' and Expedida='0'");

        foreach ($rows as $row) {
            // Si no se han indicado unidades a expedir,
            // no se actualiza stock ni se genera mvto de almacen.
            if ($row['Unidades'] != 0) {
                $valores = array(
                    'UM' => $row['UnidadMedida'],
                    'Reales' => $row['Unidades'],
                    'Pales' => $row['Pales'],
                    'Cajas' => $row['Cajas'],
                    'Reservadas' => 0,
                    'Entrando' => 0,
                );

                $mvtoAlmacen = new MvtosAlmacen();
                $ok = $mvtoAlmacen->genera(
                                $row['Entidad'],
                                'S', // Salida
                                $row['IDEntidad'], // El id del albaran
                                $row['IDAlmacen'], // El id del almacen
                                $row['IDArticulo'], // El id del articulo
                                $row['IDLote'], // El id del lote
                                $row['IDUbicacion'], // El id de la ubicacion
                                0, // Flag de deposito
                                $valores); // Valores con los que actualizar
            }

            $unidadesExpedidas += $row['Unidades'];

            if ($ok) {
                // Marcar la linea de expedición como expedida
                $expedicion = new Expediciones($row['IDLinea']);
                $expedicion->setExpedida(1);
                $expedicion->save();
            } else
                break;
        }
        unset($mvtoAlmacen);
        unset($expedicion);

        return $unidadesExpedidas;
    }

    /**
     * Devuelve un array con los datos de cabecera de la expedicion
     * de la entidad $entidad y $idEntidad
     * 
     * El array es:
     * 
     *      IDAlmacen => ,
     *      IDAlmacenero => ,
     *      IDRepartidor => ,
     *      CreatedBy => ,
     *      CreatedAt => ,
     *      ModifiedBy => ,
     *      ModifiedAt => ,
     *      
     * @param string $entidad El nombre de la entidad (AlbaranesCab,PedidosCab, TraspasosCab, etc)
     * @param integer $idEntidad El id de la entidad
     * @return array Array con la cabecera de la expedición
     */
    public function getCabecera($entidad, $idEntidad) {
        
        $rows = $this->cargaCondicion("IDAlmacen,IDAlmacenero,IDRepartidor,CreatedBy,CreatedAt,ModifiedBy,ModifiedAt", "Entidad='{$entidad}' and IDLineaEntidad='{$idEntidad}'", "IDLinea ASC LIMIT 1");
        return $rows[0];
    }
    
    /**
     * Devuelve un array de objetos Expediciones de la entidad $entidad y $idEntidad
     * 
     * @param string $entidad El nombre de la entidad (AlbaranesCab,PedidosCab, TraspasosCab, etc)
     * @param integer $idEntidad El id de la entidad
     * @return Expediciones 
     */
    public function getDetalle($entidad, $idEntidad) {

        $lineas = array();

        $rows = $this->cargaCondicion("IDLinea", "Entidad='{$entidad}' and IDLineaEntidad='{$idEntidad}'", "IDLinea ASC");
        foreach ($rows as $row) {
            $lineas[] = new Expediciones($row['IDLinea']);
        }

        return $lineas;
    }

    /**
     * Devuelve un string con la descripción de los lotes ÚNICOS separados por guión
     * que han sido servidos en la línea $idEntidad de la entidad $entidad.
     *
     * @param string $entidad El nombre de la entidad (AlbaranesCab, PedidosCab, TraspasosCab, ManufacCab)
     * @param integer $idLineaEntidad El id de la entidad linea
     * @return string Descripción de lotes seperados por guión
     */
    public function getLotes($entidad, $idLineaEntidad) {

        $arrayLotes = array();

        $rows = $this->cargaCondicion("IDLote", "Entidad='{$entidad}' and IDLineaEntidad='{$idLineaEntidad}'", "IDLinea ASC");
        foreach ($rows as $row) {
            $lote = new Lotes($row['IDLote']);
            $arrayLotes[$lote->getLote()] = "";
            unset($lote);
        }
        
        foreach ($arrayLotes as $lote => $nada)
            $lotes .= $lote . " - ";  
        
        $lotes = substr($lotes, 0, -3);

        return $lotes;
    }

    /**
     * 
     * Devuelve la descripcion de la unidad de medida
     * @return string La unidad de medida
     */
    public function getUnidadMedidaArticulo() {
        return $this->getIDArticulo()->{"get$this->UnidadMedida"}();
    }

}

?>