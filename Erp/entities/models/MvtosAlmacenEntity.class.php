<?php

/**
 * Movimientos de Almacen
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 17.08.2011 22:57:35
 */

class MvtosAlmacenEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $Id;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $Fecha;
    /**
     * @orm:Column(type="")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $Hora;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $IDAgente = '';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $IDAlmacen;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $IDUbicacion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $IDLote = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $IDArticulo = '0';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $Descripcion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $IDTipo = '0';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $IDDocumento = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $UnidadesE = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $PalesE = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $CajasE = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $UnidadesS = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $PalesS = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $CajasS = '0.00';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="mvtos_almacen")
     */
    protected $Observaciones;
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'mvtos_almacen';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'Id';

    /**
     * GETTERS Y SETTERS
     */
    public function setId($Id) {
        $this->Id = $Id;
    }

    public function getId() {
        return $this->Id;
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

    public function setHora($Hora) {
        $this->Hora = $Hora;
    }

    public function getHora() {
        return $this->Hora;
    }

    public function setIDAgente($IDAgente) {
        $this->IDAgente = $IDAgente;
    }

    public function getIDAgente() {
        if (!($this->IDAgente instanceof Agentes))
            $this->IDAgente = new Agentes($this->IDAgente);
        return $this->IDAgente;
    }

    public function setIDAlmacen($IDAlmacen) {
        $this->IDAlmacen = $IDAlmacen;
    }

    public function getIDAlmacen() {
        if (!($this->IDAlmacen instanceof Almacenes))
            $this->IDAlmacen = new Almacenes($this->IDAlmacen);
        return $this->IDAlmacen;
    }

    public function setIDUbicacion($IDUbicacion) {
        $this->IDUbicacion = $IDUbicacion;
    }

    public function getIDUbicacion() {
        if (!($this->IDUbicacion instanceof AlmacenesMapas))
            $this->IDUbicacion = new AlmacenesMapas ($this->IDUbicacion);
        return $this->IDUbicacion;
    }

    public function setIDLote($IDLote) {
        $this->IDLote = $IDLote;
    }

    public function getIDLote() {
        if (!($this->IDLote instanceof Lotes))
            $this->IDLote = new Lotes($this->IDLote);
        return $this->IDLote;
    }

    public function setIDArticulo($IDArticulo) {
        $this->IDArticulo = trim($IDArticulo);
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

    public function setIDTipo($IDTipo) {
        $this->IDTipo = $IDTipo;
    }

    public function getIDTipo() {
        if (!($this->IDTipo instanceof TiposMvtosAlmacen))
            $this->IDTipo = new TiposMvtosAlmacen($this->IDTipo);
        return $this->IDTipo;
    }

    public function setIDDocumento($IDDocumento) {
        $this->IDDocumento = trim($IDDocumento);
    }

    public function getIDDocumento() {
        return $this->IDDocumento;
    }

    public function setUnidadesE($UnidadesE) {
        $this->UnidadesE = $UnidadesE;
    }

    public function getUnidadesE() {
        return $this->UnidadesE;
    }

    public function setPalesE($PalesE) {
        $this->PalesE = $PalesE;
    }

    public function getPalesE() {
        return $this->PalesE;
    }

    public function setCajasE($CajasE) {
        $this->CajasE = $CajasE;
    }

    public function getCajasE() {
        return $this->CajasE;
    }

    public function setUnidadesS($UnidadesS) {
        $this->UnidadesS = $UnidadesS;
    }

    public function getUnidadesS() {
        return $this->UnidadesS;
    }

    public function setPalesS($PalesS) {
        $this->PalesS = $PalesS;
    }

    public function getPalesS() {
        return $this->PalesS;
    }

    public function setCajasS($CajasS) {
        $this->CajasS = $CajasS;
    }

    public function getCajasS() {
        return $this->CajasS;
    }

    public function setObservaciones($Observaciones) {
        $this->Observaciones = trim($Observaciones);
    }

    public function getObservaciones() {
        return $this->Observaciones;
    }

}

// END class mvtos_almacen
?>