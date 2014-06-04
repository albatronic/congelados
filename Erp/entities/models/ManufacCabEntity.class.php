<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 24.03.2012 11:06:10
 */

/**
 * @orm:Entity(manufac_cab)
 */
class ManufacCabEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="manufac_cab")
     */
    protected $IDManufac;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="manufac_cab")
     * @var entities\Almacenes
     */
    protected $IDAlmacenOrigen;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="manufac_cab")
     * @var entities\Almacenes
     */
    protected $IDAlmacenDestino;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="manufac_cab")
     */
    protected $FechaOrden;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="manufac_cab")
     */
    protected $FechaEnvio = '0000-00-00';
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="manufac_cab")
     */
    protected $FechaEntrega = '0000-00-00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="manufac_cab")
     * @var entities\Proveedores
     */
    protected $IDElaborador;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="manufac_cab")
     */
    protected $Descripcion;
    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="manufac_cab")
     * @var entities\EstadosManufac
     */
    protected $IDEstado = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="manufac_cab")
     */
    protected $KilosOrigen = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="manufac_cab")
     */
    protected $ImporteOrigen = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="manufac_cab")
     */
    protected $KilosDestino = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="manufac_cab")
     */
    protected $ImporteDestino = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="manufac_cab")
     */
    protected $GastosTransporte = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="manufac_cab")
     */
    protected $GastosVarios = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="manufac_cab")
     */
    protected $TotalCoste = '0.00';
    /**
     * @orm:Column(type="string")
     */
    protected $Observaciones;
    /**
     * @orm:Column(type="string")
     */
    protected $Incidencias;
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'manufac_cab';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDManufac';
    /**
     * Relacion de entidades que dependen de esta
     * @var string
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDManufac', 'ParentEntity' => 'ManufacLineas', 'ParentColumn' => 'IDManufac'),
    );
    /**
     * Relacion de entidades de las que esta depende
     * @var string
     */
    protected $_childEntities = array(
        'Almacenes',
        'Proveedores',
        'EstadosManufac',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDManufac($IDManufac) {
        $this->IDManufac = $IDManufac;
    }

    public function getIDManufac() {
        return $this->IDManufac;
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

    public function setFechaEnvio($FechaEnvio) {
        $date = new Fecha($FechaEnvio);
        $this->FechaEnvio = $date->getFecha();
        unset($date);
    }

    public function getFechaEnvio() {
        $date = new Fecha($this->FechaEnvio);
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

    public function setIDElaborador($IDElaborador) {
        $this->IDElaborador = $IDElaborador;
    }

    public function getIDElaborador() {
        if (!($this->IDElaborador instanceof Proveedores))
            $this->IDElaborador = new Proveedores($this->IDElaborador);
        return $this->IDElaborador;
    }

    public function setDescripcion($Descripcion) {
        $this->Descripcion = trim($Descripcion);
    }

    public function getDescripcion() {
        return $this->Descripcion;
    }

    public function setIDEstado($IDEstado) {
        $this->IDEstado = $IDEstado;
    }

    public function getIDEstado() {
        if (!($this->IDEstado instanceof EstadosManufac))
            $this->IDEstado = new EstadosManufac($this->IDEstado);
        return $this->IDEstado;
    }

    public function setKilosOrigen($KilosOrigen) {
        $this->KilosOrigen = $KilosOrigen;
    }

    public function getKilosOrigen() {
        return $this->KilosOrigen;
    }

    public function setImporteOrigen($ImporteOrigen) {
        $this->ImporteOrigen = $ImporteOrigen;
    }

    public function getImporteOrigen() {
        return $this->ImporteOrigen;
    }

    public function setKilosDestino($KilosDestino) {
        $this->KilosDestino = $KilosDestino;
    }

    public function getKilosDestino() {
        return $this->KilosDestino;
    }

    public function setImporteDestino($ImporteDestino) {
        $this->ImporteDestino = $ImporteDestino;
    }

    public function getImporteDestino() {
        return $this->ImporteDestino;
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

    public function setTotalCoste($TotalCoste) {
        $this->TotalCoste = $TotalCoste;
    }

    public function getTotalCoste() {
        return $this->TotalCoste;
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

}

// END class manufac_cab
?>