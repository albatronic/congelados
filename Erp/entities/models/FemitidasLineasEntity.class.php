<?php

/**
 * Lineas de Facturas Emitidas
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 14.02.2012 11:52:04
 */

/**
 * @orm:Entity(femitidas_lineas)
 */
class FemitidasLineasEntity extends Entity {

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     * @var entities\FemitidasCab
     */
    protected $IDFactura = '0';
    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $IDLinea;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     * @var entities\Articulos
     */
    protected $IDArticulo;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $Descripcion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $Unidades = '1.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $Precio = '0.000';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $Descuento = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $Importe = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $ImporteCosto = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     * @var entities\AlbaranesCab
     */
    protected $IDAlbaran = '00000000000';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     * @var entities\AlbaranesLineas
     */
    protected $IDLineaAlbaran = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $Iva = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $Recargo = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $ComisionAgente = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $ComisionMontador = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     * @var entities\TiposVenta
     */
    protected $IDVenta = '1';
    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="femitidas_lineas")
     * @var entities\ValoresSN
     */
    protected $Comisionar = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     * @var entities\Agentes
     */
    protected $IDAgente;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     * @var entities\Agentes
     */
    protected $IDComercial;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     * @var entities\Promociones
     */
    protected $IDPromocion = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $AltoAl = '0.000';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $AnchoAl = '0.000';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $MtsAl = '0.000';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $AltoFa = '0.000';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $AnchoFa = '0.000';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="femitidas_lineas")
     */
    protected $MtsFa = '0.000';
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'femitidas_lineas';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDLinea';
    /**
     * Relacion de entidades que dependen de esta
     * @var string
     */
    protected $_parentEntities = array(
    );
    /**
     * Relacion de entidades de las que esta depende
     * @var string
     */
    protected $_childEntities = array(
        'FemitidasCab',
        'Articulos',
        'AlbaranesCab',
        'AlbaranesLineas',
        'TiposVenta',
        'ValoresSN',
        'Agentes',
        'Promociones',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDFactura($IDFactura) {
        $this->IDFactura = $IDFactura;
    }

    public function getIDFactura() {
        if (!($this->IDFactura instanceof FemitidasCab))
            $this->IDFactura = new FemitidasCab($this->IDFactura);
        return $this->IDFactura;
    }

    public function setIDLinea($IDLinea) {
        $this->IDLinea = $IDLinea;
    }

    public function getIDLinea() {
        return $this->IDLinea;
    }

    public function setIDArticulo($IDArticulo) {
        $this->IDArticulo = $IDArticulo;
    }

    public function getIDArticulo() {
        if (!($this->IDArticulo instanceof Articulos))
            $this->IDArticulo = new Articulos($this->IDArticulo);
        return $this->IDArticulo;
    }

    public function setDescripcion($Descripcion) {
        $this->Descripcion = trim($Descripcion);
    }

    public function getDescripcion() {
        return $this->Descripcion;
    }

    public function setUnidades($Unidades) {
        $this->Unidades = $Unidades;
    }

    public function getUnidades() {
        return $this->Unidades;
    }

    public function setPrecio($Precio) {
        $this->Precio = $Precio;
    }

    public function getPrecio() {
        return $this->Precio;
    }

    public function setDescuento($Descuento) {
        $this->Descuento = $Descuento;
    }

    public function getDescuento() {
        return $this->Descuento;
    }

    public function setImporte($Importe) {
        $this->Importe = $Importe;
    }

    public function getImporte() {
        return $this->Importe;
    }

    public function setImporteCosto($ImporteCosto) {
        $this->ImporteCosto = $ImporteCosto;
    }

    public function getImporteCosto() {
        return $this->ImporteCosto;
    }

    public function setIDAlbaran($IDAlbaran) {
        $this->IDAlbaran = $IDAlbaran;
    }

    public function getIDAlbaran() {
        if (!($this->IDAlbaran instanceof AlbaranesCab))
            $this->IDAlbaran = new AlbaranesCab($this->IDAlbaran);
        return $this->IDAlbaran;
    }

    public function setIDLineaAlbaran($IDLineaAlbaran) {
        $this->IDLineaAlbaran = $IDLineaAlbaran;
    }

    public function getIDLineaAlbaran() {
        if (!($this->IDLineaAlbaran instanceof AlbaranesLineas))
            $this->IDLineaAlbaran = new AlbaranesLineas($this->IDLineaAlbaran);
        return $this->IDLineaAlbaran;
    }

    public function setIva($Iva) {
        $this->Iva = $Iva;
    }

    public function getIva() {
        return $this->Iva;
    }

    public function setRecargo($Recargo) {
        $this->Recargo = $Recargo;
    }

    public function getRecargo() {
        return $this->Recargo;
    }

    public function setComisionAgente($ComisionAgente) {
        $this->ComisionAgente = $ComisionAgente;
    }

    public function getComisionAgente() {
        return $this->ComisionAgente;
    }

    public function setComisionMontador($ComisionMontador) {
        $this->ComisionMontador = $ComisionMontador;
    }

    public function getComisionMontador() {
        return $this->ComisionMontador;
    }

    public function setIDVenta($IDVenta) {
        $this->IDVenta = $IDVenta;
    }

    public function getIDVenta() {
        if (!($this->IDVenta instanceof TiposVenta))
            $this->IDVenta = new TiposVenta($this->IDVenta);
        return $this->IDVenta;
    }

    public function setComisionar($Comisionar) {
        $this->Comisionar = $Comisionar;
    }

    public function getComisionar() {
        if (!($this->Comisionar instanceof ValoresSN))
            $this->Comisionar = new ValoresSN($this->Comisionar);
        return $this->Comisionar;
    }

    public function setIDAgente($IDAgente) {
        $this->IDAgente = $IDAgente;
    }

    public function getIDAgente() {
        if (!($this->IDAgente instanceof Agentes))
            $this->IDAgente = new Agentes($this->IDAgente);
        return $this->IDAgente;
    }

    public function setIDComercial($IDComercial) {
        $this->IDComercial = $IDComercial;
    }

    public function getIDComercial() {
        if (!($this->IDComercial instanceof Agentes))
            $this->IDComercial = new Agentes($this->IDComercial);
        return $this->IDComercial;
    }

    public function setIDPromocion($IDPromocion) {
        $this->IDPromocion = $IDPromocion;
    }

    public function getIDPromocion() {
        if (!($this->IDPromocion instanceof Promociones))
            $this->IDPromocion = new Promociones($this->IDPromocion);
        return $this->IDPromocion;
    }

    public function setAltoAl($AltoAl) {
        $this->AltoAl = $AltoAl;
    }

    public function getAltoAl() {
        return $this->AltoAl;
    }

    public function setAnchoAl($AnchoAl) {
        $this->AnchoAl = $AnchoAl;
    }

    public function getAnchoAl() {
        return $this->AnchoAl;
    }

    public function setMtsAl($MtsAl) {
        $this->MtsAl = $MtsAl;
    }

    public function getMtsAl() {
        return $this->MtsAl;
    }

    public function setAltoFa($AltoFa) {
        $this->AltoFa = $AltoFa;
    }

    public function getAltoFa() {
        return $this->AltoFa;
    }

    public function setAnchoFa($AnchoFa) {
        $this->AnchoFa = $AnchoFa;
    }

    public function getAnchoFa() {
        return $this->AnchoFa;
    }

    public function setMtsFa($MtsFa) {
        $this->MtsFa = $MtsFa;
    }

    public function getMtsFa() {
        return $this->MtsFa;
    }

}

// END class femitidas_lineas
?>