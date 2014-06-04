<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(psto_cab)
 */
class PstoCabEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $IDPsto;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $IDContador;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $NumeroPsto;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $IDSucursal;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $IDAlmacen;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $IDAgente;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $IDComercial;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $Fecha;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $FechaAceptacion = '00/00/0000';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $IDCliente = '';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $IDDirec = '';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $Importe = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $Descuento = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $BaseImponible1 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $Iva1 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $CuotaIva1 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $Recargo1 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $CuotaRecargo1 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $BaseImponible2 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $Iva2 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $CuotaIva2 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $Recargo2 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $CuotaRecargo2 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $BaseImponible3 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $Iva3 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $CuotaIva3 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $Recargo3 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $CuotaRecargo3 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $TotalBases = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $TotalIva = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $TotalRecargo = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $RecargoFinanciero = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $CuotaRecargoFinanciero = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $Total = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $IDEstado = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $IDAlbaran = '0';
    /**
     * @orm:Column(type="string")
     */
    protected $Observaciones;
    /**
     * @orm:Column(type="integer")
     * @assert:Blank(groups="psto_cab")
     */
    protected $Peso = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $Volumen = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $Bultos = '0';
    /**
     * @orm:Column(type="string")
     */
    protected $Expedicion = '';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $IDAgencia = '1';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $IDFP;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="psto_cab")
     */
    protected $Clave;
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'psto_cab';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDPsto';

    /**
     * GETTERS Y SETTERS
     */
    public function setIDPsto($IDPsto) {
        $this->IDPsto = $IDPsto;
    }

    public function getIDPsto() {
        return $this->IDPsto;
    }

    public function setIDContador($IDContador) {
        $this->IDContador = $IDContador;
    }

    public function getIDContador() {
        if (!($this->IDContador instanceof Contadores))
            $this->IDContador = new Contadores($this->IDContador);
        return $this->IDContador;
    }

    public function setNumeroPsto($NumeroPsto) {
        $this->NumeroPsto = trim($NumeroPsto);
    }

    public function getNumeroPsto() {
        return $this->NumeroPsto;
    }

    public function setIDSucursal($IDSucursal) {
        $this->IDSucursal = $IDSucursal;
    }

    public function getIDSucursal() {
        if (!($this->IDSucursal instanceof Sucursales))
            $this->IDSucursal = new Sucursales($this->IDSucursal);
        return $this->IDSucursal;
    }

    public function setIDAlmacen($IDAlmacen) {
        $this->IDAlmacen = $IDAlmacen;
    }

    public function getIDAlmacen() {
        if (!($this->IDAlmacen instanceof Almacenes))
            $this->IDAlmacen = new Almacenes($this->IDAlmacen);
        return $this->IDAlmacen;
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

    public function setFecha($Fecha) {
        $date = new Fecha($Fecha);
        $this->Fecha = $date->getFecha();
        unset($date);
    }

    public function getFecha() {
        $date = new Fecha($this->Fecha);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setFechaAceptacion($FechaAceptacion) {
        $date = new Fecha($FechaAceptacion);
        $this->FechaAceptacion = $date->getFecha();
        unset($date);
    }

    public function getFechaAceptacion() {
        $date = new Fecha($this->FechaAceptacion);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setIDCliente($IDCliente) {
        $this->IDCliente = $IDCliente;
    }

    public function getIDCliente() {
        if (!($this->IDCliente instanceof Clientes))
            $this->IDCliente = new Clientes($this->IDCliente);
        return $this->IDCliente;
    }

    public function setIDDirec($IDDirec) {
        $this->IDDirec = $IDDirec;
    }

    public function getIDDirec() {
        if (!($this->IDDirec instanceof ClientesDentrega))
            $this->IDDirec = new ClientesDentrega($this->IDDirec);
        return $this->IDDirec;
    }

    public function setImporte($Importe) {
        $this->Importe = $Importe;
    }

    public function getImporte() {
        return $this->Importe;
    }

    public function setDescuento($Descuento) {
        $this->Descuento = $Descuento;
    }

    public function getDescuento() {
        return $this->Descuento;
    }

    public function setBaseImponible1($BaseImponible1) {
        $this->BaseImponible1 = $BaseImponible1;
    }

    public function getBaseImponible1() {
        return $this->BaseImponible1;
    }

    public function setIva1($Iva1) {
        $this->Iva1 = $Iva1;
    }

    public function getIva1() {
        return $this->Iva1;
    }

    public function setCuotaIva1($CuotaIva1) {
        $this->CuotaIva1 = $CuotaIva1;
    }

    public function getCuotaIva1() {
        return $this->CuotaIva1;
    }

    public function setRecargo1($Recargo1) {
        $this->Recargo1 = $Recargo1;
    }

    public function getRecargo1() {
        return $this->Recargo1;
    }

    public function setCuotaRecargo1($CuotaRecargo1) {
        $this->CuotaRecargo1 = $CuotaRecargo1;
    }

    public function getCuotaRecargo1() {
        return $this->CuotaRecargo1;
    }

    public function setBaseImponible2($BaseImponible2) {
        $this->BaseImponible2 = $BaseImponible2;
    }

    public function getBaseImponible2() {
        return $this->BaseImponible2;
    }

    public function setIva2($Iva2) {
        $this->Iva2 = $Iva2;
    }

    public function getIva2() {
        return $this->Iva2;
    }

    public function setCuotaIva2($CuotaIva2) {
        $this->CuotaIva2 = $CuotaIva2;
    }

    public function getCuotaIva2() {
        return $this->CuotaIva2;
    }

    public function setRecargo2($Recargo2) {
        $this->Recargo2 = $Recargo2;
    }

    public function getRecargo2() {
        return $this->Recargo2;
    }

    public function setCuotaRecargo2($CuotaRecargo2) {
        $this->CuotaRecargo2 = $CuotaRecargo2;
    }

    public function getCuotaRecargo2() {
        return $this->CuotaRecargo2;
    }

    public function setBaseImponible3($BaseImponible3) {
        $this->BaseImponible3 = $BaseImponible3;
    }

    public function getBaseImponible3() {
        return $this->BaseImponible3;
    }

    public function setIva3($Iva3) {
        $this->Iva3 = $Iva3;
    }

    public function getIva3() {
        return $this->Iva3;
    }

    public function setCuotaIva3($CuotaIva3) {
        $this->CuotaIva3 = $CuotaIva3;
    }

    public function getCuotaIva3() {
        return $this->CuotaIva3;
    }

    public function setRecargo3($Recargo3) {
        $this->Recargo3 = $Recargo3;
    }

    public function getRecargo3() {
        return $this->Recargo3;
    }

    public function setCuotaRecargo3($CuotaRecargo3) {
        $this->CuotaRecargo3 = $CuotaRecargo3;
    }

    public function getCuotaRecargo3() {
        return $this->CuotaRecargo3;
    }

    public function setTotalBases($TotalBases) {
        $this->TotalBases = $TotalBases;
    }

    public function getTotalBases() {
        return $this->TotalBases;
    }

    public function setTotalIva($TotalIva) {
        $this->TotalIva = $TotalIva;
    }

    public function getTotalIva() {
        return $this->TotalIva;
    }

    public function setTotalRecargo($TotalRecargo) {
        $this->TotalRecargo = $TotalRecargo;
    }

    public function getTotalRecargo() {
        return $this->TotalRecargo;
    }

    public function setRecargoFinanciero($RecargoFinanciero) {
        $this->RecargoFinanciero = $RecargoFinanciero;
    }

    public function getRecargoFinanciero() {
        return $this->RecargoFinanciero;
    }

    public function setCuotaRecargoFinanciero($CuotaRecargoFinanciero) {
        $this->CuotaRecargoFinanciero = $CuotaRecargoFinanciero;
    }

    public function getCuotaRecargoFinanciero() {
        return $this->CuotaRecargoFinanciero;
    }

    public function setTotal($Total) {
        $this->Total = $Total;
    }

    public function getTotal() {
        return $this->Total;
    }

    public function setIDEstado($IDEstado) {
        $this->IDEstado = $IDEstado;
    }

    public function getIDEstado() {
        if (!($this->IDEstado instanceof EstadosPresupuestos))
            $this->IDEstado = new EstadosPresupuestos($this->IDEstado);
        return $this->IDEstado;
    }

    public function setIDAlbaran($IDAlbaran) {
        $this->IDAlbaran = $IDAlbaran;
    }

    public function getIDAlbaran() {
        if (!$this->IDAlbaran instanceof AlbaranesCab)
            $this->IDAlbaran = new AlbaranesCab($this->IDAlbaran);
        return $this->IDAlbaran;
    }

    public function setObservaciones($Observaciones) {
        $this->Observaciones = trim($Observaciones);
    }

    public function getObservaciones() {
        return $this->Observaciones;
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

    public function setBultos($Bultos) {
        $this->Bultos = $Bultos;
    }

    public function getBultos() {
        return $this->Bultos;
    }

    public function setExpedicion($Expedicion) {
        $this->Expedicion = trim($Expedicion);
    }

    public function getExpedicion() {
        return $this->Expedicion;
    }

    public function setIDAgencia($IDAgencia) {
        $this->IDAgencia = $IDAgencia;
    }

    public function getIDAgencia() {
        if (!($this->IDAgencia instanceof Agencias))
            $this->IDAgencia = new Agencias($this->IDAgencia);
        return $this->IDAgencia;
    }

    public function setIDFP($IDFP) {
        $this->IDFP = $IDFP;
    }

    public function getIDFP() {
        if (!($this->IDFP instanceof FormasPago))
            $this->IDFP = new FormasPago($this->IDFP);
        return $this->IDFP;
    }

    public function setClave($Clave) {
        $this->Clave = $Clave;
    }

    public function getClave() {
        return $this->Clave;
    }

}

// END class psto_cab
?>