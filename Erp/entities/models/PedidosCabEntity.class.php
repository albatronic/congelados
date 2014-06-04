<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(pedidos_cab)
 */
class PedidosCabEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $IDPedido;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $IDSucursal;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $IDAlmacen;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $IDAgente;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $SuPedido;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $Referencia;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $Fecha;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $FechaEntrega = '00/00/0000';
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $FechaEntrada = '00/00/0000';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $IDProveedor;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $Importe = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $Descuento = '0.00';
    /**
     * @orm:Column(type="integer")
     */
    protected $BaseImponible1 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $Iva1 = '0.00';
    /**
     * @orm:Column(type="integer")
     */
    protected $CuotaIva1 = '0.00';
    /**
     * @orm:Column(type="integer")
     */
    protected $Recargo1 = '0.00';
    /**
     * @orm:Column(type="integer")
     */
    protected $CuotaRecargo1 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $BaseImponible2 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $Iva2 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $CuotaIva2 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $Recargo2 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $CuotaRecargo2 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $BaseImponible3 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $Iva3 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $CuotaIva3 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $Recargo3 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $CuotaRecargo3 = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $TotalBases = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $TotalIva = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $TotalRecargo = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $Total = '0.00';
    /**
     * @orm:Column(type="string")
     */
    protected $Observaciones;
    /**
     * @orm:Column(type="string")
     */
    protected $Incidencias;
    /**
     * @orm:Column(type="string")
     */
    protected $Clave;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $IDEstado = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="albaranes_cab")
     */
    protected $IDFactura = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $IDFP = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $IDAgencia;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $Deposito = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_cab")
     */
    protected $FlagFacturar = '0';
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'pedidos_cab';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDPedido';

    /**
     * GETTERS Y SETTERS
     */
    public function setIDPedido($IDPedido) {
        $this->IDPedido = $IDPedido;
    }

    public function getIDPedido() {
        return $this->IDPedido;
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

    public function setSuPedido($SuPedido) {
        $this->SuPedido = $SuPedido;
    }

    public function getSuPedido() {
        return $this->SuPedido;
    }

    public function setReferencia($Referencia) {
        $this->Referencia = $Referencia;
    }

    public function getReferencia() {
        return $this->Referencia;
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

    public function setFechaEntrega($FechaEntrega) {
        $date = new Fecha($FechaEntrega);
        $this->FechaEntrega = $date->getFecha();
        unset($date);
    }

    public function getFechaEntrega() {
        $date = new Fecha($this->FechaEntrega);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setFechaEntrada($FechaEntrada) {
        $date = new Fecha($FechaEntrada);
        $this->FechaEntrada = $date->getFecha();
        unset($date);
    }

    public function getFechaEntrada() {
        $date = new Fecha($this->FechaEntrada);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setIDProveedor($IDProveedor) {
        $this->IDProveedor = $IDProveedor;
    }

    public function getIDProveedor() {
        if (!($this->IDProveedor instanceof Proveedores))
            $this->IDProveedor = new Proveedores($this->IDProveedor);
        return $this->IDProveedor;
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

    public function setTotal($Total) {
        $this->Total = $Total;
    }

    public function getTotal() {
        return $this->Total;
    }

    public function setObservaciones($Observaciones) {
        $this->Observaciones = trim($Observaciones);
    }

    public function getObservaciones() {
        return $this->Observaciones;
    }

    public function setIncidencias($Incidencias) {
        $this->Incidencias = trim($Incidencias);
    }

    public function getIncidencias() {
        return $this->Incidencias;
    }

    public function setClave($Clave) {
        $this->Clave = trim($Clave);
    }

    public function getClave() {
        return $this->Clave;
    }

    public function setIDEstado($IDEstado) {
        $this->IDEstado = $IDEstado;
    }

    public function getIDEstado() {
        if (!($this->IDEstado instanceof EstadosPedidos))
            $this->IDEstado = new EstadosPedidos($this->IDEstado);
        return $this->IDEstado;
    }

    public function setIDFactura($IDFactura) {
        $this->IDFactura = $IDFactura;
    }

    public function getIDFactura() {
        if (!$this->IDFactura instanceof FrecibidasCab)
            $this->IDFactura = new FrecibidasCab($this->IDFactura);
        return $this->IDFactura;
    }

    public function setIDFP($IDFP) {
        $this->IDFP = $IDFP;
    }

    public function getIDFP() {
        if (!($this->IDFP instanceof FormasPago))
            $this->IDFP = new FormasPago($this->IDFP);
        return $this->IDFP;
    }

    public function setIDAgencia($IDAgencia) {
        $this->IDAgencia = $IDAgencia;
    }

    public function getIDAgencia() {
        if (!($this->IDAgencia instanceof Agencias))
            $this->IDAgencia = new Agencias($this->IDAgencia);
        return $this->IDAgencia;
    }

    public function setDeposito($Deposito) {
        $this->Deposito = $Deposito;
    }

    public function getDeposito() {
        if (!$this->Deposito instanceof ValoresSN)
            $this->Deposito = new ValoresSN($this->Deposito);
        return $this->Deposito;
    }

    public function setFlagFacturar($FlagFacturar) {
        $this->FlagFacturar = $FlagFacturar;
    }

    public function getFlagFacturar() {
        if (!($this->FlagFacturar instanceof ValoresSN))
            $this->FlagFacturar = new ValoresSN($this->FlagFacturar);
        return $this->FlagFacturar;
    }
}

// END class pedidos_cab
?>