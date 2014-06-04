<?php

/**
 * Articulos
 *
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:46
 */

/**
 * @orm:Entity(articulos)
 */
class ArticulosEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $IDArticulo;

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Codigo;

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Descripcion = '';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $IDFamilia = '';

    /**
     * @orm:Column(type="integer")
     */
    protected $IDSubfamilia = '';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $IDFabricante = '';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Pvd = '0.000';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Pvp = '0.000';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Margen = '0.00';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Pmc = '0.000';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $IDIva = '1';

    /**
     * @orm:Column(type="string")
     */
    protected $Etiqueta;

    /**
     * @orm:Column(type="string")
     */
    protected $CodigoEAN;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Caducidad = '0';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Garantia;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Peso = '0.0';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Volumen = '0.0';

    /**
     * @orm:Column(type="string")
     */
    protected $Caracteristicas;

    /**
     * @orm:Column(type="datetime")
     */
    protected $FechaUltimoPrecio;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Vigente = '1';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Inventario = '1';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Trazabilidad = '1';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $PackingCompras = '1.00';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $PackingVentas = '1.00';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Merma = '0.00';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $BloqueoStock = '0';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $IDEstado = '5';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $StockMinimo = '0.00';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $StockMaximo = '0.00';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $MinimoVentaAlto = '0';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $MinimoVentaAncho = '0';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $MinimoVenta = '0';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $MultiploAlto = '0';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $MultiploAncho = '0';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $RecargoEnergetico = '0';

    /**
     * Unidad de Medida Básica
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $UMB = '1';

    /**
     * Unidad de Medida para las Compras
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $UMC = '1';

    /**
     * Factor de conversion de la UMC hacia la UMB
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $CUMC = '1';

    /**
     * Unidad de Medida de Almacén
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $UMA = '1';

    /**
     * Factor de conversion de la UMA hacia la UMB
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $CUMA = '1';

    /**
     * Unidad de Media para las Ventas
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $UMV = '1';

    /**
     * Factor de conversion de la UMV hacia la UMB
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $CUMV = '1';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $PublicarWeb = '1';

    /**
     * @orm:Column(type="string")
     */
    protected $UrlAmigable;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $ConTallasColores = '0';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $IDUso = '2';

    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';

    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'articulos';

    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDArticulo';

    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDArticulo', 'ParentEntity' => 'AlbaranesLineas', 'ParentColumn' => 'IDArticulo'),
        array('SourceColumn' => 'IDArticulo', 'ParentEntity' => 'PstoLineas', 'ParentColumn' => 'IDArticulo'),
        array('SourceColumn' => 'IDArticulo', 'ParentEntity' => 'FemitidasLineas', 'ParentColumn' => 'IDArticulo'),
        array('SourceColumn' => 'IDArticulo', 'ParentEntity' => 'FrecibidasLineas', 'ParentColumn' => 'IDArticulo'),
        array('SourceColumn' => 'IDArticulo', 'ParentEntity' => 'PedidosLineas', 'ParentColumn' => 'IDArticulo'),
        array('SourceColumn' => 'IDArticulo', 'ParentEntity' => 'Existencias', 'ParentColumn' => 'IDArticulo'),
        array('SourceColumn' => 'IDArticulo', 'ParentEntity' => 'Promociones', 'ParentColumn' => 'IDArticulo'),
        array('SourceColumn' => 'IDArticulo', 'ParentEntity' => 'InventariosLineas', 'ParentColumn' => 'IDArticulo'),
        array('SourceColumn' => 'IDArticulo', 'ParentEntity' => 'Lotes', 'ParentColumn' => 'IDArticulo'),
        array('SourceColumn' => 'IDArticulo', 'ParentEntity' => 'TraspasosLineas', 'ParentColumn' => 'IDArticulo'),
        array('SourceColumn' => 'IDArticulo', 'ParentEntity' => 'MvtosAlmacen', 'ParentColumn' => 'IDArticulo'),
    );

    /**
     * Relacion de entidades de las que depende esta
     * @var array
     */
    protected $_childEntities = array(
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDArticulo($IDArticulo) {
        $this->IDArticulo = $IDArticulo;
    }

    public function getIDArticulo() {
        return $this->IDArticulo;
    }

    public function setCodigo($Codigo) {
        $this->Codigo = trim($Codigo);
    }

    public function getCodigo() {
        return $this->Codigo;
    }

    public function setDescripcion($Descripcion) {
        $this->Descripcion = trim($Descripcion);
    }

    public function getDescripcion() {
        return $this->Descripcion;
    }

    public function setIDFamilia($IDFamilia) {
        $this->IDFamilia = $IDFamilia;
    }

    public function getIDFamilia() {
        if (!($this->IDFamilia instanceof Familias))
            $this->IDFamilia = new Familias($this->IDFamilia);
        return $this->IDFamilia;
    }

    public function setIDSubfamilia($IDSubfamilia) {
        $this->IDSubfamilia = $IDSubfamilia;
    }

    public function getIDSubfamilia() {
        if (!($this->IDSubfamilia instanceof Subfamilias))
            $this->IDSubfamilia = new Subfamilias($this->IDSubfamilia);
        return $this->IDSubfamilia;
    }

    public function setIDFabricante($IDFabricante) {
        $this->IDFabricante = $IDFabricante;
    }

    public function getIDFabricante() {
        if (!($this->IDFabricante instanceof Fabricantes))
            $this->IDFabricante = new Fabricantes($this->IDFabricante);
        return $this->IDFabricante;
    }

    public function setPvd($Pvd) {
        $this->Pvd = $Pvd;
    }

    public function getPvd() {
        return $this->Pvd;
    }

    public function setPvp($Pvp) {
        $this->Pvp = $Pvp;
    }

    public function getPvp() {
        return $this->Pvp;
    }

    public function setMargen($Margen) {
        $this->Margen = $Margen;
    }

    public function getMargen() {
        return $this->Margen;
    }

    public function setPmc($Pmc) {
        $this->Pmc = $Pmc;
    }

    public function getPmc() {
        return $this->Pmc;
    }

    public function setIDIva($IDIva) {
        $this->IDIva = $IDIva;
    }

    public function getIDIva() {
        if (!($this->IDIva instanceof TiposIva))
            $this->IDIva = new TiposIva($this->IDIva);
        return $this->IDIva;
    }

    public function setEtiqueta($Etiqueta) {
        $this->Etiqueta = trim($Etiqueta);
    }

    public function getEtiqueta() {
        return $this->Etiqueta;
    }

    public function setCodigoEAN($CodigoEAN) {
        $this->CodigoEAN = trim($CodigoEAN);
    }

    public function getCodigoEAN() {
        return $this->CodigoEAN;
    }

    public function setCaducidad($Caducidad) {
        $this->Caducidad = $Caducidad;
    }

    public function getCaducidad() {
        return $this->Caducidad;
    }

    public function setGarantia($Garantia) {
        $this->Garantia = $Garantia;
    }

    public function getGarantia() {
        return $this->Garantia;
    }

    public function setPeso($Peso) {
        $this->Peso = $Peso;
    }

    public function getPeso() {
        return $this->Peso;
    }

    public function setVolumen($Volumen) {
        $this->Volumen = $Volumen;
    }

    public function getVolumen() {
        return $this->Volumen;
    }

    public function setCaracteristicas($Caracteristicas) {
        $this->Caracteristicas = trim($Caracteristicas);
    }

    public function getCaracteristicas() {
        return $this->Caracteristicas;
    }

    public function setFechaUltimoPrecio($FechaUltimoPrecio) {
        $this->FechaUltimoPrecio = $FechaUltimoPrecio;
    }

    public function getFechaUltimoPrecio() {
        return $this->FechaUltimoPrecio;
    }

    public function setVigente($Vigente) {
        $this->Vigente = $Vigente;
    }

    public function getVigente() {
        if (!($this->Vigente instanceof ValoresSN))
            $this->Vigente = new ValoresSN($this->Vigente);
        return $this->Vigente;
    }

    public function setInventario($Inventario) {
        $this->Inventario = $Inventario;
    }

    public function getInventario() {
        if (!($this->Inventario instanceof ValoresSN))
            $this->Inventario = new ValoresSN($this->Inventario);
        return $this->Inventario;
    }

    public function setTrazabilidad($Trazabilidad) {
        $this->Trazabilidad = $Trazabilidad;
    }

    public function getTrazabilidad() {
        if (!($this->Trazabilidad instanceof ValoresSN))
            $this->Trazabilidad = new ValoresSN($this->Trazabilidad);
        return $this->Trazabilidad;
    }

    public function setPackingCompras($PackingCompras) {
        $this->PackingCompras = $PackingCompras;
    }

    public function getPackingCompras() {
        return $this->PackingCompras;
    }

    public function setPackingVentas($PackingVentas) {
        $this->PackingVentas = $PackingVentas;
    }

    public function getPackingVentas() {
        return $this->PackingVentas;
    }

    public function setMerma($Merma) {
        $this->Merma = $Merma;
    }

    public function getMerma() {
        return $this->Merma;
    }

    public function setBloqueoStock($BloqueoStock) {
        $this->BloqueoStock = $BloqueoStock;
    }

    public function getBloqueoStock() {
        if (!($this->BloqueoStock instanceof ValoresSN))
            $this->BloqueoStock = new ValoresSN($this->BloqueoStock);
        return $this->BloqueoStock;
    }

    public function setIDEstado($IDEstado) {
        $this->IDEstado = $IDEstado;
    }

    public function getIDEstado() {
        if (!($this->IDEstado instanceof ArticulosEstados))
            $this->IDEstado = new ArticulosEstados($this->IDEstado);
        return $this->IDEstado;
    }

    public function setStockMinimo($StockMinimo) {
        $this->StockMinimo = $StockMinimo;
    }

    public function getStockMinimo() {
        return $this->StockMinimo;
    }

    public function setStockMaximo($StockMaximo) {
        $this->StockMaximo = $StockMaximo;
    }

    public function getStockMaximo() {
        return $this->StockMaximo;
    }

    public function setMinimoVentaAlto($MinimoVentaAlto) {
        $this->MinimoVentaAlto = $MinimoVentaAlto;
    }

    public function getMinimoVentaAlto() {
        return $this->MinimoVentaAlto;
    }

    public function setMinimoVentaAncho($MinimoVentaAncho) {
        $this->MinimoVentaAncho = $MinimoVentaAncho;
    }

    public function getMinimoVentaAncho() {
        return $this->MinimoVentaAncho;
    }

    public function setMinimoVenta($MinimoVenta) {
        $this->MinimoVenta = $MinimoVenta;
    }

    public function getMinimoVenta() {
        return $this->MinimoVenta;
    }

    public function setMultiploAlto($MultiploAlto) {
        $this->MultiploAlto = $MultiploAlto;
    }

    public function getMultiploAlto() {
        return $this->MultiploAlto;
    }

    public function setMultiploAncho($MultiploAncho) {
        $this->MultiploAncho = $MultiploAncho;
    }

    public function getMultiploAncho() {
        return $this->MultiploAncho;
    }

    public function setRecargoEnergetico($RecargoEnergetico) {
        $this->RecargoEnergetico = $RecargoEnergetico;
    }

    public function getRecargoEnergetico() {
        if (!($this->RecargoEnergetico instanceof ValoresSN))
            $this->RecargoEnergetico = new ValoresSN($this->RecargoEnergetico);
        return $this->RecargoEnergetico;
    }

    public function setUMB($UMB) {
        $this->UMB = $UMB;
    }

    public function getUMB() {
        if (!($this->UMB instanceof UnidadesMedida))
            $this->UMB = new UnidadesMedida($this->UMB);
        return $this->UMB;
    }

    public function setUMC($UMC) {
        $this->UMC = $UMC;
    }

    public function getUMC() {
        if (!($this->UMC instanceof UnidadesMedida))
            $this->UMC = new UnidadesMedida($this->UMC);
        return $this->UMC;
    }

    public function setCUMC($CUMC) {
        $this->CUMC = $CUMC;
    }

    public function getCUMC() {
        return $this->CUMC;
    }

    public function setUMA($UMA) {
        $this->UMA = $UMA;
    }

    public function getUMA() {
        if (!($this->UMA instanceof UnidadesMedida))
            $this->UMA = new UnidadesMedida($this->UMA);
        return $this->UMA;
    }

    public function setCUMA($CUMA) {
        $this->CUMA = $CUMA;
    }

    public function getCUMA() {
        return $this->CUMA;
    }

    public function setUMV($UMV) {
        $this->UMV = $UMV;
    }

    public function getUMV() {
        if (!($this->UMV instanceof UnidadesMedida))
            $this->UMV = new UnidadesMedida($this->UMV);
        return $this->UMV;
    }

    public function setCUMV($CUMV) {
        $this->CUMV = $CUMV;
    }

    public function getCUMV() {
        return $this->CUMV;
    }

    public function setPublicarWeb($PublicarWeb) {
        $this->PublicarWeb = $PublicarWeb;
    }

    public function getPublicarWeb() {
        if (!($this->PublicarWeb instanceof ValoresSN))
            $this->PublicarWeb = new ValoresSN($this->PublicarWeb);
        return $this->PublicarWeb;
    }

    public function setUrlAmigable($UrlAmigable) {
        $this->UrlAmigable = trim($UrlAmigable);
    }

    public function getUrlAmigable() {
        return $this->UrlAmigable;
    }

    public function setConTallasColores($ConTallasColores) {
        $this->ConTallasColores = $ConTallasColores;
    }

    public function getConTallasColores() {
        if (!($this->ConTallasColores instanceof ValoresSN))
            $this->ConTallasColores = new ValoresSN($this->ConTallasColores);
        return $this->ConTallasColores;
    }

    public function setIDUso($IDUso) {
        $this->IDUso = $IDUso;
    }

    public function getIDUso() {
        if (!($this->IDUso instanceof UsosArticulos))
            $this->IDUso = new UsosArticulos($this->IDUso);
        return $this->IDUso;
    }
}

// END class articulos
?>