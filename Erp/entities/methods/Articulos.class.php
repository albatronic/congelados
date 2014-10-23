<?php

/**
 * Description of Articulos
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Articulos extends ArticulosEntity {

    public function __toString() {
        return $this->getDescripcion();
    }

    public function getCodigoFabricante() {
        return (is_object($this->IDFabricante)) ?
                $this->getIDFabricante()->getIDFabricante() :
                $this->IDFabricante;
    }

    /**
     * Devuelve un array con todos los articulos vigentes de la familia indicada, sujetos a trazabilidad e inventario
     * Cada elemento tiene la primarykey y el valor de $column
     *
     * @param integer $idFamilia El id de familia (opcional)
     * @param integer $trazabilidad Flag trazabilidad (por defecto 1 => Si)
     * @param integer $inventario Flag control inventario ( por defecto 1 => Si)
     * @param string $colum La columna para rellenar el value del array
     * @return array Array de artículos
     */
    public function fetchAll($idFamilia = '', $trazabilidad = '1', $inventario = '1', $column = 'Descripcion') {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $filtro = "WHERE (Vigente='1') AND (Trazabilidad='{$trazabilidad}') AND (Inventario='{$inventario}')";
            if ($idFamilia != '')
                $filtro .= " AND (IDFamilia='{$idFamilia}')";
            $query = "SELECT IDArticulo as Id,$column as Value FROM articulos $filtro ORDER BY $column ASC;";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        $rows[] = array('Id' => '', Value => ':: Articulo');
        return $rows;
    }

    /**
     * Devuelve un array con todos los articulos de la familia indicada, sujetos a trazabilidad e inventario
     * Cada elemento tiene la primarykey y el valor de $column
     *
     * @param integer $idFamilia El id de familia (opcional)
     * @param integer $trazabilidad Flag trazabilidad (por defecto 1 => Si)
     * @param integer $inventario Flag control inventario ( por defecto 1 => Si)
     * @param string $colum La columna para rellenar el value del array
     * @return array Array de artículos
     */
    public function getArticulos($idFamilia = '', $trazabilidad = '1', $inventario = '1', $column = 'Descripcion') {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $filtro = "WHERE (Trazabilidad='{$trazabilidad}') AND (Inventario='{$inventario}')";
            if ($idFamilia != '')
                $filtro .= " AND (IDFamilia='{$idFamilia}')";
            $query = "SELECT IDArticulo as Id,$column as Value FROM articulos $filtro ORDER BY $column ASC;";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        $rows[] = array('Id' => '', Value => ':: Articulo');
        return $rows;
    }

    /**
     * Pongo el precio medio de compra igual al precio de costo
     *
     * @return integer El id del ultimo articulo creado
     */
    public function create() {

        $this->Pmc = $this->Pvd;

        return parent::create();
    }

    /**
     * Si hay stock no se puede cambiar el estado de inventario ni las unidades de medida
     * Si las unidades de media son iguales, los factores de conversión los pongo a 1
     * Calculo el precio de venta sobre EL PRECIO MEDIO DE COSTO
     * Si columna 'etiqueta' está vacía, le pongo valor de la descripción del artículo
     * Crear la Url Amigable
     */
    protected function validaLogico() {

        // Si hay stock no se puede cambiar el estado de inventario ni las unidades de medida
        $exi = new Existencias();

        if ($exi->hayRegistroExistencias($this->IDArticulo)) {
            // Hay registro de existencias
            $articulo = new Articulos($this->IDArticulo);
            $this->setInventario($articulo->getInventario()->getIDTipo());
            $this->setUMA($articulo->getUMA()->getId());
            $this->setUMC($articulo->getUMC()->getId());
            $this->setUMV($articulo->getUMV()->getId());
            unset($articulo);
            $this->_alertas[] = "Hay Stock, no puede cambiar ni el estado de Inventario ni las Unidades de Medida";
        } else {
            // Si no es inventariable, pongo los valores relativos
            // al inventario a 0.
            if ($this->Inventario == 0) {
                $this->Trazabilidad = 0;
                $this->BloqueoStock = 0;
                $this->StockMinimo = 0;
                $this->StockMaximo = 0;
                $this->Caducidad = 0;
            }

            // Si las unidades de media son iguales, los factores de conversión los pongo a 1
            if ($this->UMC == $this->UMB)
                $this->CUMC = 1;
            if ($this->UMA == $this->UMB)
                $this->CUMA = 1;
            if ($this->UMV == $this->UMB)
                $this->CUMV = 1;
        }

        // Calculo el precio de venta sobre EL PRECIO MEDIO DE COSTO
        if ($this->Pmc == 0)
            $this->Pmc = $this->Pvd;
        if ($this->Pmc != 0)
            $this->Pvp = $this->Pmc * ( 1 + $this->Margen / 100);

        // Si columna 'etiqueta' está vacía, le pongo valor
        if ($this->Etiqueta == '')
            $this->setEtiqueta($this->Descripcion);

        // Crear la Url Amigable
        $this->setUrlAmigable($this->validaUrlAmigable($this->UrlAmigable));

        unset($exi);
    }

    /**
     * Devuelve el PVP SIN impuestos correspondiente a la unidad de medida indicada.
     * Por defecto la Unidad de Venta.
     *
     * Devuelve el importe redondeado a $decimales cifras decimales (por defecto 2)
     *
     * @param string La Unidad de Medida.Por defecto la de venta
     * @param integer El número de decimales
     * @return decimal El PVP sin impuestos.
     */
    public function getPrecioVenta($um = 'UMV', $decimales = 2) {

        $um = strtoupper($um);

        $pvp = $this->Pvp * $this->{"getC$um"}();

        return round($pvp, $decimales);
    }

    /**
     * Devuelve el precio de venta mínimo calculado en base
     * al Precio Medio de costo aplicándole el porcentaje de margén mínimo
     * indicado en la familia a la que pertenece este artículo
     *
     * @param string $um La unidad de medida en la que se expresará el precio
     * @param integer $decimales El número de decimales con los que se expresará el precio
     * @return decimal El precio de venta minimo
     */
    public function getPrecioMinimoVenta($um = 'UMV', $decimales = 2) {

        $um = strtoupper($um);

        $familia = new Familias($this->getIDFamilia()->getIDFamilia());
        $margenMinimo = $familia->getMargenMinimo();
        unset($familia);

        $pvpMinimo = $this->Pmc * ( 1 + $margenMinimo / 100);
        $pvpMinimo = $pvpMinimo * $this->{"getC$um"}();

        return round($pvpMinimo, $decimales);
    }

    /**
     * Devuelve el PVP CON impuestos correspondiente a la unidad de medida indicada.
     * Por defecto la Unidad de Venta.
     *
     * Devuelve el importe redondeado a $decimales cifras decimales (por defecto 2)
     *
     * @param string La Unidad de Medida.Por defecto la de venta
     * @param integer El número de decimales
     * @return decimal El PVP con impuestos.
     */
    public function getPrecioVentaConImpuestos($um = 'UMV', $decimales = 2) {

        $um = strtoupper($um);

        $pvp = $this->Pvp * $this->{"getC$um"}() * (1 + $this->getIDIva()->getIva() / 100);

        return round($pvp, $decimales);
    }

    /**
     * Devuelve el Pmc sin impuestos correspondiente a la unidad de medida indicada.
     * Por defecto la Unidad de Compra.
     *
     * @param string La Unidad de Medida. Por defecto la de compra
     * @return float El Precio Medio de Compra sin impuestos.
     */
    public function getPrecioCosto($um = 'UMC') {

        $um = strtoupper($um);

        return $this->Pmc * $this->{"getC$um"}();
    }

    /**
     * Recibe un objeto AlbaranCab o PstoCab y devuelve un array
     * con cinco elementos:
     *
     *  [Promocion] -> El objeto promocion (si hay)
     *  [Promo][Precio] -> El precio de la promocion
     *  [Promo][Descuento] -> El descuento de la promocion
     *  [Tarifa][Precio] -> El precio de la tarifa
     *  [Tarifa][Descuento] -> El descuento de la tarifa
     *
     * Para ello consulta promociones vigentes para el artículo, cliente
     * y si no hay, aplica la tarifa asociada al cliente.
     *
     * @param objeto $objetoVenta Un objeto albaran o presupuesto
     * @return array Array con el objeto promocion, el precio y el descuento de la tarifa y de la promocion
     */
    public function cotizar($objetoVenta, $unidades) {
        $precios = array();

        // ---------------------------------------------------------------------
        // Coger el precio de tarifa
        // ---------------------------------------------------------------------
        $tarifa = $objetoVenta->getIDCliente()->getIDTarifa();
        if ($tarifa->getTipo()->getIDTipo() == '0') {
            //Descuento sobre el PVP
            $precios['Tarifa']['Precio'] = $this->getPrecioVenta();
            $precios['Tarifa']['Descuento'] = $tarifa->getValor();
        } else {
            //Margen sobre el PVD
            $precios['Tarifa']['Precio'] = $this->getPrecioCosto() * ( 1 + $tarifa->getValor() / 100 );
            $precios['Tarifa']['Descuento'] = 0;
        }

        // -------------------------------------------------------------------------
        //  Buscar promociones. En caso de haber promo para artículo y para familia,
        //  prevalecen los promos a nivel de artículo sobre
        //  las promos a nivel de familia 
        // -------------------------------------------------------------------------
        $promocion = new Promociones();
        $filtro = "( 
                (IDArticulo='{$this->getIDArticulo()}') or 
                (IDFamilia='{$this->getIDFamilia()->getIDFamilia()}')
            ) AND (CantidadMinima<='{$unidades}')
              AND (FinPromocion>='{$objetoVenta->getDate()}') 
              AND (IDFP='{$objetoVenta->getIDFP()->getIDFP()}')";
        $promociones = $promocion->cargaCondicion("*", $filtro, "IDArticulo DESC");

        if (!count($promociones)) {
            $filtro = "( 
                (IDArticulo='{$this->getIDArticulo()}') or 
                (IDFamilia='{$this->getIDFamilia()->getIDFamilia()}')
            ) AND (CantidadMinima<='{$unidades}')
              AND (FinPromocion>='{$objetoVenta->getDate()}') 
              AND (IDFP='0')";
            $promociones = $promocion->cargaCondicion("*", $filtro, "IDArticulo DESC");
        }

        $hayPromo = false;
        if (count($promociones)) {
            // Recorro las promos-clientes a ver si hay alguna especifica para el cliente
            while ((!$hayPromo) and ( list(, $promocion) = each($promociones))) {

                // El articulo está en promocion, ver si aplica al cliente
                $promoCliente = new PromocionesClientes();
                $filtro = "IDPromocion='{$promocion['IDPromocion']}' and (IDCliente='{$objetoVenta->getIDCliente()->getIDCliente()}')";
                $promoClientes = $promoCliente->cargaCondicion("Id,IdPromocion", $filtro);
                if ($promoClientes[0]['Id'] != '') {
                    // La promo aplica
                    $hayPromo = true;
                    $promocion = new Promociones($promoClientes[0]['IdPromocion']);
                }
            }

            if (!$hayPromo) {
                // Recorro los promos-clientes a ver si hay alguna para el grupo de clientes
                reset($promociones);
                while ((!$hayPromo) and ( list(, $promocion) = each($promociones))) {
                    $promoCliente = new PromocionesClientes();
                    $filtro = "IDPromocion='{$promocion['IDPromocion']}' and (IDGrupo='{$objetoVenta->getIDCliente()->getIDGrupo()->getIDGrupo()}')";
                    $promoClientes = $promoCliente->cargaCondicion("Id,IdPromocion", $filtro);
                    if ($promoClientes[0]['Id'] != '') {
                        // La promo aplica
                        $hayPromo = true;
                        $promocion = new Promociones($promoClientes[0]['IdPromocion']);
                    }
                }
                unset($promoCliente);
            }
        }

        if ($hayPromo) {
            // Hay promo, coger precios de promo
            $precios['Promocion'] = $promocion;
            switch ($promocion->getTipoPromocion()->getIDTipo()) {
                case '0': //Promocion de descuento en PVP
                    $precios['Promo']['Precio'] = $this->getPrecioVenta();
                    $precios['Promo']['Descuento'] = $promocion->getValor();
                    break;
                case '1': //Promocion en Margen sobre PVD
                    $precios['Promo']['Precio'] = $this->getPrecioCosto() * ( 1 + $promocion->getValor() / 100 );
                    $precios['Promo']['Descuento'] = 0;
                    break;
                case '2': //Promocion en precio neto
                    $precios['Promo']['Precio'] = $promocion->getValor();
                    $precios['Promo']['Descuento'] = 0;
                    break;
            }
        }

        unset($promocion);
        return $precios;
    }

    /**
     * Recalcula y actualiza:
     * 
     *  1) el precio medio de compra (Pmc)
     *  2) el precio de compra (Pvd)
     *  3) el margen o el pvp dependiendo del valor del parámetro ACTU_PRECIOS, en cualquier caso
     *     estos valores se calculan respecto el precio medio de compra
     *  4) la fecha del último precio
     *
     * NOTA: Este método sobreentiende que primero se han acutalizado las existencias
     * por lo tanto las existencias anteriores son las actuales menos las compradas.
     *
     * La fórmula que se utiliza es:
     *
     *   nuevoPmc = ( (existenciasAnteriores * PMC) + (unidadesCompradas * ultimoPrecioCompra) ) / (existenciasAnteriores + unidadesCompradas)
     *
     * @param float $unidadesCompradas La unidades compradas expresadas en la unidad de medida de compra
     * @param float $ultimoPrecioCompra El último precio de compra expresado en la unidad de medida de compra
     * @return boolean
     */
    public function actualizaPrecios($unidadesCompradas, $ultimoPrecioCompra) {

        $exi = new Existencias();
        $stock = $exi->getStock($this->IDArticulo, '', '', '', '', 'UMC');
        unset($exi);

        $existenciasAnteriores = ( $stock['RE'] - $unidadesCompradas );

        $pmcUnidadCompra = $this->Pmc * $this->CUMC;

        if (($existenciasAnteriores + $unidadesCompradas) != 0) {
            $nuevoPmc = ( ($existenciasAnteriores * $pmcUnidadCompra) + ($unidadesCompradas * $ultimoPrecioCompra) ) / ($existenciasAnteriores + $unidadesCompradas);
            $nuevoPmc = $nuevoPmc / $this->CUMC;
            $this->setPmc($nuevoPmc);
        }

        // Leo el parametro 'ACTU_PRECIOS' para ver el comportamiento a seguir
        // en el cambio de precio de venta o margen. Si no estuviera definido,
        // se respeta el PVP a costa del MARGEN
        $parametro = $_SESSION['USER']['user']['actuPrecios'];
        if (($parametro != 'MARGEN') and ( $parametro != 'PVP'))
            $parametro = 'MARGEN';

        // Calculo el nuevo margen o el nuevo precio de venta (según el parámetro) sobre el PRECIO MEDIO DE COSTO
        if ($this->Pmc == 0)
            $this->Pmc = $this->Pvd;

        if ($parametro == 'PVP')
            $this->Pvp = $this->Pmc * ( 1 + $this->Margen / 100);
        else {
            if ($this->Pmc != 0)
                $this->Margen = 100 * ($this->Pvp / $this->Pmc - 1);
            else
                $this->Margen = 0;
        }



        $this->setPvd($ultimoPrecioCompra / $this->CUMC);
        $this->setFechaUltimoPrecio(date('Y-m-d H:i:s'));

        return $this->save();
    }

    /**
     * Transforma una cantidad expresada en la unidad de medida origen a
     * otra cantidad expresada en la unidad de medida destino redondeada
     * a los dígitos de precisión indicados (por defecto 3 dígitos decimales)
     *
     * @param <type> $unidadMediaOrigen Valores Posibles (UMC,UMV,UMA)
     * @param <type> $unidadMedidaDestino  Valores Posibles (UMC,UMV,UMA)
     * @param float $cantidad Cantidad a transformar
     * @param integer Digitos de precision del redondeo (defecto = 3)
     * @return float La cantidad transformada
     */
    public function convertUnit($unidadMedidaOrigen, $unidadMedidaDestino, $cantidad, $precision = 3) {

        $unidadMedidaOrigen = strtoupper($unidadMedidaOrigen);
        $unidadMedidaDestino = strtoupper($unidadMedidaDestino);

        if ($unidadMedidaOrigen == $unidadMedidaDestino)
            return $cantidad;
        else {
            $cantidadUnidadMedidaBasica = $cantidad * $this->{"getC$unidadMedidaOrigen"}();
            $cantidadUnidadMedidaDestino = $cantidadUnidadMedidaBasica / $this->{"getC$unidadMedidaDestino"}();
            return round($cantidadUnidadMedidaDestino, $precision);
        }
    }

    /**
     * Devuelve el literal correspondiente al tipo de unidad de medida
     * $tipoUm del artículo
     *
     * @param string $tipoUm Tipo de unidad de medida (UMB,UMC,UMV,UMA)
     * @return string
     */
    public function getUnidadMedida($tipoUm) {

        $tipoUm = strtoupper($tipoUm);

        return $this->{"get$tipoUm"}();
    }

    /**
     * Devuelve el factor de conversión a unidad de medida básica
     * que siempre es 1
     *
     * @return int El factor de conversión
     */
    public function getCUMB() {
        return 1;
    }

    /**
     * Devuelve un array con las ubicaciones del $idAlmacen
     * donde hay existencias del artículo y le añade al final
     * el resto de ubicaciones.
     *
     * Si el parámetro $todas se pone al false, solo devuelve las ubicaciones
     * donde haya existencias.
     *
     * Se utiliza principalmente en los procesos de recepción de mercancía
     * (recepción de pedidos y de elaboraciones)
     *
     * @param integer $idAlmacen El id de almacén
     * @param string $filtroUbicacion Filtro de la ubicación (defecto %)
     * @param boolean $todas
     * @return array Las ubicaciones array('Id' => IDUbicacion, 'Value' => Ubicacion, 'Reales' => Existencias Reales)
     */
    public function getUbicaciones($idAlmacen, $filtroUbicacion = "%", $todas = TRUE) {
        $ubicaciones = array();

        $mapa = new AlmacenesMapas();

        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            //Ubicaciones donde haya existencias.
            //$query = "Call UbicacionesArticulo({$idAlmacen},{$this->IDArticulo});";
            $query = "SELECT DISTINCT e.IDUbicacion AS Id, m.Ubicacion AS Value, e.Reales as Reales
                        FROM
                            {$this->_dataBaseName}.existencias e,
                            {$mapa->getDataBaseName()}.almacenes_mapas m
                        WHERE
                            e.IDUbicacion = m.IDUbicacion AND
                            e.IDAlmacen= '{$idAlmacen}' AND
                            e.IDArticulo= '{$this->IDArticulo}' AND
                            e.Reales > 0 AND
                            m.Ubicacion LIKE '{$filtroUbicacion}'";
            $em->query($query);
            $habituales = $em->fetchResult();
            $em->desConecta();
            unset($em);
        }

        if ($todas) {
            $habituales[] = array('Id' => '0', 'Value' => ':: Resto de Ubicaciones ::');
            $ubicaciones = array_merge($habituales, $mapa->fetchAll($idAlmacen, $filtroUbicacion));
        } else
            $ubicaciones = $habituales;

        unset($mapa);

        return $ubicaciones;
    }

    /**
     * Devuelve un array con todos los lotes de los que
     * hay existencias para el artículo y el $idAlmacen
     *
     * Si el parametro $agrupado es TRUE se mostrará un solo registro por lote
     * acumulando todas las existencias que haya en distintas ubicaciones
     *
     * El array devuelto tiene 3 elementos:
     *
     *  Id: El id de lote
     *
     *  Value: El nombre del lote
     *
     *  Reales: Las existencias reales del lote y almacen
     *
     * @param integer $idAlmacen El id de almacen
     * @param boolean $agrupado Agrupar por ubicaciones true/false (defecto FALSE)
     * @return array Array de lotes
     */
    public function getLotesDisponibles($idAlmacen, $agrupado = FALSE) {
        $lotes = array();

        $this->conecta();
        if (is_resource($this->_dbLink)) {
            //Lotes con existencias.
            //$query = "Call LotesArticulo({$idAlmacen},{$this->IDArticulo});";
            if ($agrupado) {
                $query = "SELECT e.IDLote AS Id, l.Lote AS Value, sum(e.Reales) as Reales
                        FROM {$this->_dataBaseName}.existencias e, {$this->_dataBaseName}.lotes l
                        WHERE
                            e.IDArticulo = '{$this->IDArticulo}' AND
                            e.IDAlmacen = '{$idAlmacen}' AND
                            e.IDLote = l.IDLote AND
                            Reales > 0
                        GROUP BY e.IDLote
                        ORDER BY l.FechaCaducidad ASC";
            } else {
                $query = "SELECT distinct e.IDLote AS Id, l.Lote AS Value, e.Reales as Reales
                        FROM {$this->_dataBaseName}.existencias e, {$this->_dataBaseName}.lotes l
                        WHERE
                            e.IDArticulo = '{$this->IDArticulo}' AND
                            e.IDAlmacen = '{$idAlmacen}' AND
                            e.IDLote = l.IDLote AND
                            e.Reales > 0
                        ORDER BY l.FechaCaducidad ASC";
            }
            $this->_em->query($query);
            $lotes = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }

        return $lotes;
    }

    /**
     * Devuelve un array con todos los lotes y ubicaciones donde
     * hay existencias reales del artículo en el almacén indicado
     *
     * Si se indica el parametro 'cantidad' solo devuelve los lotes-ubicaciones
     * necesarios para satisfacer esa cantidad. En caso contrario, devuelve todos.
     *
     * El array tiene 4 columnas: IDLote, FechaCaducidad, IDUbicacion, Reales
     * El orden es ascendente por FechaCadudidad
     *
     * @param integer $idAlmacen El id de almacén
     * @param decimal $cantidad La cantidad que se necesita (opcional)
     * @return array Los Lotes, Ubicaciones y Existencias Reales
     */
    public function getLotesUbicaciones($idAlmacen, $cantidad = 0) {
        $lotesUbicaciones = array();

        $this->conecta();
        if (is_resource($this->_dbLink)) {
            //Lotes con existencias.
            //$query = "Call LotesUbicacionesArticulo({$idAlmacen},{$this->IDArticulo});";
            $query = "SELECT DISTINCT e.IDLote, l.FechaCaducidad, e.IDUbicacion, e.Reales
                        FROM {$this->_dataBaseName}.existencias e, {$this->_dataBaseName}.lotes l
                        WHERE
                            e.IDArticulo = '{$this->IDArticulo}' AND
                            e.IDAlmacen= '{$idAlmacen}' AND
                            e.IDLote = l.IDLote AND
                            e.Reales > 0
                        ORDER BY l.FechaCaducidad, e.Reales ASC";
            $this->_em->query($query);
            $lotesUbicaciones = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }

        if ($cantidad > 0) {
            //Devolver solo lo necesario para satisfacer esta cantidad
            $aux = array();
            $acumulado = 0;
            foreach ($lotesUbicaciones as $item) {
                $aux[] = $item;
                $acumulado += $item['Reales'];
                if ($acumulado >= $cantidad)
                    break;
            }
            $lotesUbicaciones = $aux;
        }

        return $lotesUbicaciones;
    }

    /**
     * Devuelve un array con las reservas del articulo y almacen indicado
     *
     * Cada elemento del array es otro array con los elementos:
     * array (
     *   'IDAlbaran' =>
     *   'NumeroAlbaran' =>
     *   'Fecha' =>
     *   'IDCliente' =>
     *   'RazonSocial =>
     *   'Reservas' =>
     * )
     *
     * @param integer $idAlmacen El id de almacen
     * @return array Array con las reservas
     */
    public function getReservas($idAlmacen) {
        $reservas = array();

        $agente = new Agentes();
        $dataBaseEmpresas = $agente->getDataBaseName();
        
        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            //$query = "Call ReservasArticuloAlmacen('{$this->IDArticulo}','{$idAlmacen}');";
            $query = "select
                        cab.IDAlbaran,
                        cab.NumeroAlbaran,
                        cab.Fecha,
                        cab.IDCliente,
                        cli.RazonSocial,
                        age.Nombre as Comercial,
                        sum(lin.Unidades) as Reservas
                      from
                        {$this->_dataBaseName}.albaranes_cab    as cab,
                        {$this->_dataBaseName}.albaranes_lineas as lin,
                        {$this->_dataBaseName}.clientes         as cli,
                        {$dataBaseEmpresas}.agentes          as age
                      where
                        cab.IDAlbaran  = lin.IDAlbaran and
                        cab.IDCliente  = cli.IDCliente and
                        cab.IDComercial  = age.IDAgente and
                        lin.IDEstado   = '1' and
                        lin.IDArticulo = '{$this->IDArticulo}' and
                        lin.IDAlmacen  = '{$idAlmacen}'
                      group by IDAlbaran
                      order by Fecha ASC";
            $em->query($query);//echo $query;
            $reservas = $em->fetchResult();
            $em->desConecta();
        }
        unset($em);

        return $reservas;
    }
    
    /**
     * Devuelve un array con las entradas en curso del articulo y almacen indicado
     *
     * Cada elemento del array es otro array con los elementos:
     * array (
     *   'IDPedido' =>
     *   'Fecha' =>
     *   'FechaEntraga' =>
     *   'IDProveedor' =>
     *   'RazonSocial =>
     *   'Entrando' =>
     * )
     *
     * @param integer $idAlmacen El id de almacen
     * @return array Array con las unidades entrando
     */
    public function getEntrando($idAlmacen) {
        $reservas = array();

        $tablaPedidos = new PedidosCab();
        $tablaPedidosLineas = new PedidosLineas();
        $tablaProveedores = new Proveedores();

        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "select
                        cab.IDPedido,                    
                        cab.Fecha,
                        cab.FechaEntrega,
                        cab.IDProveedor,
                        prv.RazonSocial,
                        sum(lin.Unidades) as Entrando
                      from
                        {$tablaPedidos->getDataBaseName()}.{$tablaPedidos->getTableName()} as cab,
                        {$tablaPedidosLineas->getDataBaseName()}.{$tablaPedidosLineas->getTableName()} as lin,
                        {$tablaProveedores->getDataBaseName()}.{$tablaProveedores->getTableName()} as prv
                      where
                        cab.IDPedido  = lin.IDPedido and
                        cab.IDProveedor  = prv.IDProveedor and
                        lin.IDEstado   = '1' and
                        lin.IDArticulo = '{$this->IDArticulo}' and
                        lin.IDAlmacen  = '{$idAlmacen}'
                      group by IDPedido
                      order by Fecha ASC";
            $em->query($query);//echo $query,"<br/>";
            $reservas = $em->fetchResult();
            $em->desConecta();
        }
        unset($em);

        unset($tablaPedidos);
        unset($tablaPedidosLineas);
        unset($tablaProveedores);

        return $reservas;
    }
    
    /**
     * Genera la Url amigable del articulo
     * Utiliza el parametro pasado o en su defecto la descripcion del articulo
     * para generar la url amigable. La url generada es 100 caracteres a lo sumo
     * @param <type> $UrlAmigable
     * @return string La Url Amigable
     */
    public function validaUrlAmigable($UrlAmigable = '') {

        if (trim($UrlAmigable) == '')
            $UrlAmigable = $this->Descripcion . "_" . $this->Codigo;

        $url = Textos::limpia($UrlAmigable);
        $url = substr(trim($UrlAmigable), 0, 100);

        $url = str_replace(" ", "-", $url);
        $url = str_replace("/", "-", $url);
        $url = str_replace("\"", "", $url);
        $url = str_replace("+", "-", $url);
        $url = str_replace("&", "", $url);

        $arti = new Articulos();
        $rows = $arti->cargaCondicion('Codigo', "UrlAmigable='{$url}'");
        if (count($rows)) {
            if ((count($rows) == 1) and ( $rows[0]['Codigo'] == $this->Codigo)) {
                
            } else
                $url .= "_" . date("s");
        }

        unset($arti);

        return $url;
    }

    public function kk() {
        $a = new Articulos();
        $rows = $a->cargaCondicion('IDArticulo');
        foreach ($rows as $key => $value) {
            $a = new Articulos($value['IDArticulo']);
            $a->setUrlAmigable($a->validaUrlAmigable($a->getUrlAmigable()));
            $a->save();
        }
        unset($a);
    }

}

?>
