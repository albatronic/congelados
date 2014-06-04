<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:48
 */

/**
 * @orm:Entity(recibos_proveedores)
 */
class RecibosProveedoresEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recibos_proveedores")
     */
    protected $IDRecibo;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="recibos_proveedores")
     */
    protected $Recibo;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recibos_clientes")
     */
    protected $IDSucursal;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="recibos_proveedores")
     */
    protected $IDFactura;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recibos_proveedores")
     */
    protected $IDProveedor;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="recibos_proveedores")
     */
    protected $Fecha;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="recibos_proveedores")
     */
    protected $Vencimiento;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recibos_proveedores")
     */
    protected $Importe = '0.00';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="recibos_proveedores")
     */
    protected $CBanco = '';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recibos_proveedores")
     */
    protected $Asiento = '0';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="recibos_proveedores")
     */
    protected $Concepto = '';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recibos_proveedores")
     */
    protected $IDEstado = '0';
    /**
     * @orm:Column(type="string")
     */
    protected $IDRemesa;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="recibos_clientes")
     */
    protected $Remesar = '1';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="recibos_proveedores")
     */
    protected $CContable = '0000000000';
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'recibos_proveedores';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDRecibo';

    /**
     * GETTERS Y SETTERS
     */
    public function setIDRecibo($IDRecibo) {
        $this->IDRecibo = $IDRecibo;
    }

    public function getIDRecibo() {
        return $this->IDRecibo;
    }

    public function setRecibo($Recibo) {
        $this->Recibo = $Recibo;
    }

    public function getRecibo() {
        return $this->Recibo;
    }

    public function setIDSucursal($IDSucursal) {
        $this->IDSucursal = $IDSucursal;
    }

    public function getIDSucursal() {
        if (!($this->IDSucursal instanceof Sucursales))
            $this->IDSucursal = new Sucursales($this->IDSucursal);
        return $this->IDSucursal;
    }

    public function setIDFactura($IDFactura) {
        $this->IDFactura = $IDFactura;
    }

    public function getIDFactura() {
        if (!$this->IDFactura instanceof FrecibidasCab)
            $this->IDFactura = new FrecibidasCab($this->IDFactura);
        return $this->IDFactura;
    }

    public function setIDProveedor($IDProveedor) {
        $this->IDProveedor = $IDProveedor;
    }

    public function getIDProveedor() {
        if (!($this->IDProveedor instanceof Proveedores))
            $this->IDProveedor = new Proveedores($this->IDProveedor);
        return $this->IDProveedor;
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

    public function setVencimiento($Vencimiento) {
        $date = new Fecha($Vencimiento);
        $this->Vencimiento = $date->getFecha();
        unset($date);
    }

    public function getVencimiento() {
        $date = new Fecha($this->Vencimiento);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setImporte($Importe) {
        $this->Importe = $Importe;
    }

    public function getImporte() {
        return $this->Importe;
    }

    public function setCBanco($CBanco) {
        $this->CBanco = trim($CBanco);
    }

    public function getCBanco() {
        return $this->CBanco;
    }

    public function setAsiento($Asiento) {
        $this->Asiento = $Asiento;
    }

    public function getAsiento() {
        return $this->Asiento;
    }

    public function setConcepto($Concepto) {
        $this->Concepto = trim($Concepto);
    }

    public function getConcepto() {
        return $this->Concepto;
    }

    public function setIDEstado($IDEstado) {
        $this->IDEstado = $IDEstado;
    }

    public function getIDEstado() {
        if (!($this->IDEstado instanceof EstadosRecibos))
            $this->IDEstado = new EstadosRecibos($this->IDEstado);
        return $this->IDEstado;
    }

    public function setIDRemesa($IDRemesa) {
        $this->IDRemesa = trim($IDRemesa);
    }

    public function getIDRemesa() {
        return $this->IDRemesa;
    }

    public function setRemesar($Remesar) {
        $this->Remesar = $Remesar;
    }

    public function getRemesar() {
        if (!($this->Remesar instanceof ValoresSN))
            $this->Remesar = new ValoresSN($this->Remesar);
        return $this->Remesar;
    }

    public function setCContable($CContable) {
        $this->CContable = trim($CContable);
    }

    public function getCContable() {
        return $this->CContable;
    }

}

// END class recibos_proveedores
?>