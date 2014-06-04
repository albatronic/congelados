<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 25.02.2012 20:14:14
 */

/**
 * @orm:Entity(recepciones)
 */
class Recepciones extends RecepcionesEntity {

    public function __toString() {
        return $this->getIDLinea();
    }

    /**
     * Calculo las unidades (kgrs) netas restando a las unidades brutas
     * Comprueba que se haya indicado el lote si procede(
     * Comprueba que se haya indicado la ubiación si procede
     *
     * @return boolean
     */
    public function validaLogico() {

        $this->setUnidadesNetas($this->UnidadesBrutas - ($this->Pales * $this->DestarePale + $this->Cajas * $this->DestareCaja));
        if (($this->Unidades == 0) or ($this->UnidadesNetas == 0))
            $this->_errores[] = "Debe indicar la cantidad recibida y las unidades brutas";

        $articulo = new Articulos($this->IDArticulo);
        if (($articulo->getTrazabilidad()->getIDTipo()) and (!$this->IDLote))
            $this->_errores[] = "Debe indicar un lote";
        unset($articulo);

        $almacen = new Almacenes($this->IDAlmacen);
        if ( ($almacen->getControlUbicaciones()->getIDTipo()) and (!$this->IDUbicacion) )
            $this->_errores[] = "Debe indicar una ubicación";
        unset($almacen);
        
        return (count($this->_errores) == 0);
    }

    /**
     * Recepciona todas las líneas de recepción correspondientes
     * a la entidad $entidad y línea de entidad $idLineaEntidad
     *
     * @param string $entidad La entidad padre (PedidosCab,ManufacCab,TraspasosCab)
     * @param integer $idLineaEntidad El id de la línea de la entidad padre
     * @return float La cantidad de unidades recepcionadas
     */
    public function recepciona($entidad, $idLineaEntidad, $flagDeposito='0') {

        $unidadesRecepcionadas = 0;

        $rows = $this->cargaCondicion("*", "Entidad='{$entidad}' and IDLineaEntidad='{$idLineaEntidad}' and Recepcionada='0'");

        foreach ($rows as $row) {
            $valores = array(
                'UM' => $row['UnidadMedida'],
                'Reales' => $row['UnidadesNetas'],
                'Pales' => $row['Pales'],
                'Cajas' => $row['Cajas'],
                'Reservadas' => 0,
                'Entrando' => 0,
            );

            $mvtoAlmacen = new MvtosAlmacen();
            $ok = $mvtoAlmacen->genera(
                            $row['Entidad'],
                            'E', // Entrada
                            $row['IDEntidad'], // El id del albaran
                            $row['IDAlmacen'], // El id del almacen
                            $row['IDArticulo'], // El id del articulo
                            $row['IDLote'], // El id del lote
                            $row['IDUbicacion'], // El id de la ubicacion
                            $flagDeposito, // Flag de deposito
                            $valores); // Valores con los que actualizar

            $unidadesRecepcionadas += $row['UnidadesNetas'];

            if ($ok) {
                // Marcar la linea de recepción como recepcionada.
                $recepcion = new Recepciones($row['IDLinea']);
                $recepcion->setRecepcionada(1);
                $recepcion->save();
            } else
                break;
        }
        unset($mvtoAlmacen);
        unset($recepcion);

        return $unidadesRecepcionadas;
    }

    /**
     * Devuelve un array de objetos Recepciones de la entidad $entidad y $idEntidad
     *
     * @param string $entidad El nombre de la entidad (AlbaranesCab,PedidosCab, TraspasosCab, etc)
     * @param integer $idEntidad El id de la entidad*
     * @return array Array de objetos Recepciones
     */
    public function getDetalle($entidad, $idEntidad) {

        $lineas = array();

        $rows = $this->cargaCondicion("IDLinea", "Entidad='{$entidad}' and IDLineaEntidad='{$idEntidad}'", "IDLinea ASC");
        foreach ($rows as $row) {
            $lineas[] = new Recepciones($row['IDLinea']);
        }

        return $lineas;
    }

    /**
     * Devuelve un string con la descripción de los lotes separados por guión
     * que han sido servidos en la línea $idEntidad de la entidad $entidad.
     *
     * @param string $entidad El nombre de la entidad (AlbaranesCab,PedidosCab, TraspasosCab, ManufacCab)
     * @param integer $idLineaEntidad El id de la entidad línea
     * @return string Descripción de lotes seperados por guión
     */
    public function getLotes($entidad, $idLineaEntidad) {

        $lotes = "";

        $rows = $this->cargaCondicion("IDLote", "Entidad='{$entidad}' and IDLineaEntidad='{$idLineaEntidad}'", "IDLinea ASC");
        foreach ($rows as $row) {
            $lote = new Lotes($row['IDLote']);
            $lotes .= $lote->getLote() . " - ";
            unset($lote);
        }
        $lotes = substr($lotes, 0, -3);

        return $lotes;
    }

    /**
     * Devuelve la descripcion de la unidad de medida 
     * @return string La unidad de medida
     */
    public function getUnidadMedidaArticulo() {
        return $this->getIDArticulo()->{"get$this->UnidadMedida"}();
    }
}

?>