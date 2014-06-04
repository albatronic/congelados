<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 19.06.2012 12:27:30
 */

/**
 * @orm:Entity(traspasos_cab)
 */
class TraspasosCabEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $IDTraspaso;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     * @var entities\Sucursales
     */
    protected $IDSucursal;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     * @var entities\Contadores
     */
    protected $IDContador;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $NumeroTraspaso;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $FechaOrden = '';
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $FechaSalida = '0000-00-00';
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $FechaEntrada = '0000-00-00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     * @var entities\Almacenes
     */
    protected $IDAlmacenOrigen = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     * @var entities\Almacenes
     */
    protected $IDAlmacenDestino = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     * @var entities\Agencias
     */
    protected $IDAgencia = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $GastosTransporte = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $GastosVarios = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $TotalGastos = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $TotalCosto = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $Peso = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $Volumen = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $Bultos = '0.00';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $Expedicion = '';
    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="traspasos_cab")
     * @var entities\EstadosTraspasos
     */
    protected $IDEstado = '0';
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
    protected $Descripcion;
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'traspasos_cab';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDTraspaso';
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
        'Sucursales',
        'Contadores',
        'Almacenes',
        'Agencias',
        'EstadosTraspasos',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDTraspaso($IDTraspaso) {
        $this->IDTraspaso = $IDTraspaso;
    }

    public function getIDTraspaso() {
        return $this->IDTraspaso;
    }

    public function setIDSucursal($IDSucursal) {
        $this->IDSucursal = $IDSucursal;
    }

    public function getIDSucursal() {
        if (!($this->IDSucursal instanceof Sucursales))
            $this->IDSucursal = new Sucursales($this->IDSucursal);
        return $this->IDSucursal;
    }

    public function setIDContador($IDContador) {
        $this->IDContador = $IDContador;
    }

    public function getIDContador() {
        if (!($this->IDContador instanceof Contadores))
            $this->IDContador = new Contadores($this->IDContador);
        return $this->IDContador;
    }

    public function setNumeroTraspaso($NumeroTraspaso) {
        $this->NumeroTraspaso = trim($NumeroTraspaso);
    }

    public function getNumeroTraspaso() {
        return $this->NumeroTraspaso;
    }

    public function setFechaOrden($FechaOrden) {
        $date = new Fecha($FechaOrden);
        $this->FechaOrden = $date->getFecha();
        unset($date);
    }

    public function getFechaOrden() {
        $date = new Fecha($this->FechaOrden);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setFechaSalida($FechaSalida) {
        $date = new Fecha($FechaSalida);
        $this->FechaSalida = $date->getFecha();
        unset($date);
    }

    public function getFechaSalida() {
        $date = new Fecha($this->FechaSalida);
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

    public function setIDAlmacenOrigen($IDAlmacenOrigen) {
        $this->IDAlmacenOrigen = $IDAlmacenOrigen;
    }

    public function getIDAlmacenOrigen() {
        if (!($this->IDAlmacenOrigen instanceof Almacenes))
            $this->IDAlmacenOrigen = new Almacenes($this->IDAlmacenOrigen);
        return $this->IDAlmacenOrigen;
    }

    public function setIDAlmacenDestino($IDAlmacenDestino) {
        $this->IDAlmacenDestino = $IDAlmacenDestino;
    }

    public function getIDAlmacenDestino() {
        if (!($this->IDAlmacenDestino instanceof Almacenes))
            $this->IDAlmacenDestino = new Almacenes($this->IDAlmacenDestino);
        return $this->IDAlmacenDestino;
    }

    public function setIDAgencia($IDAgencia) {
        $this->IDAgencia = $IDAgencia;
    }

    public function getIDAgencia() {
        if (!($this->IDAgencia instanceof Agencias))
            $this->IDAgencia = new Agencias($this->IDAgencia);
        return $this->IDAgencia;
    }

    public function setGastosTransporte($GastosTransporte) {
        $this->GastosTransporte = $GastosTransporte;
    }

    public function getGastosTransporte() {
        return $this->GastosTransporte;
    }

    public function setGastosVarios($GastosVarios) {
        $this->GastosVarios = $GastosVarios;
    }

    public function getGastosVarios() {
        return $this->GastosVarios;
    }

    public function setTotalGastos($TotalGastos) {
        $this->TotalGastos = $TotalGastos;
    }

    public function getTotalGastos() {
        return $this->TotalGastos;
    }

    public function setTotalCosto($TotalCosto) {
        $this->TotalCosto = $TotalCosto;
    }

    public function getTotalCosto() {
        return $this->TotalCosto;
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

    public function setIDEstado($IDEstado) {
        $this->IDEstado = $IDEstado;
    }

    public function getIDEstado() {
        if (!($this->IDEstado instanceof EstadosTraspasos))
            $this->IDEstado = new EstadosTraspasos($this->IDEstado);
        return $this->IDEstado;
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

    public function setDescripcion($Descripcion) {
        $this->Descripcion = trim($Descripcion);
    }

    public function getDescripcion() {
        return $this->Descripcion;
    }

}

// END class traspasos_cab
?>