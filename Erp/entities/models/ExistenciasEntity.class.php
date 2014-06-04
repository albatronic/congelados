<?php

/**
 * Existencias
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 18.12.2011 00:27:53
 */

/**
 * @orm:Entity(existencias)
 */
class ExistenciasEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="existencias")
     */
    protected $Id;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="existencias")
     * @var entities\Almacenes
     */
    protected $IDAlmacen = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="existencias")
     * @var entities\AlmacenesMapas
     */
    protected $IDUbicacion = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="existencias")
     * @var entities\Articulos
     */
    protected $IDArticulo;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="existencias")
     * @var entities\Lotes
     */
    protected $IDLote = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="existencias")
     */
    protected $Reales = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="existencias")
     */
    protected $Pales = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="existencias")
     */
    protected $Cajas = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="existencias")
     */
    protected $Reservadas = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="existencias")
     */
    protected $Entrando = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="existencias")
     */
    protected $Maximo = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="existencias")
     */
    protected $Minimo = '0.00';
    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="existencias")
     */
    protected $EnDeposito = '0';
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'existencias';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'Id';
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
        'Almacenes',
        'AlmacenesMapas',
        'Articulos',
        'Lotes',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setId($Id) {
        $this->Id = $Id;
    }

    public function getId() {
        return $this->Id;
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
            $this->IDUbicacion = new AlmacenesMapas($this->IDUbicacion);
        return $this->IDUbicacion;
    }

    public function setIDArticulo($IDArticulo) {
        $this->IDArticulo = $IDArticulo;
    }

    public function getIDArticulo() {
        if (!($this->IDArticulo instanceof Articulos))
            $this->IDArticulo = new Articulos($this->IDArticulo);
        return $this->IDArticulo;
    }

    public function setIDLote($IDLote) {
        $this->IDLote = $IDLote;
    }

    public function getIDLote() {
        if (!($this->IDLote instanceof Lotes))
            $this->IDLote = new Lotes($this->IDLote);
        return $this->IDLote;
    }

    public function setReales($Reales) {
        $this->Reales = $Reales;
    }

    public function getReales() {
        return $this->Reales;
    }

    public function setPales($Pales) {
        $this->Pales = $Pales;
    }

    public function getPales() {
        return $this->Pales;
    }

    public function setCajas($Cajas) {
        $this->Cajas = $Cajas;
    }

    public function getCajas() {
        return $this->Cajas;
    }

    public function setReservadas($Reservadas) {
        $this->Reservadas = $Reservadas;
    }

    public function getReservadas() {
        return $this->Reservadas;
    }

    public function setEntrando($Entrando) {
        $this->Entrando = $Entrando;
    }

    public function getEntrando() {
        return $this->Entrando;
    }

    public function setMaximo($Maximo) {
        $this->Maximo = $Maximo;
    }

    public function getMaximo() {
        return $this->Maximo;
    }

    public function setMinimo($Minimo) {
        $this->Minimo = $Minimo;
    }

    public function getMinimo() {
        return $this->Minimo;
    }

    public function setEnDeposito($EnDeposito) {
        $this->EnDeposito = $EnDeposito;
    }

    public function getEnDeposito() {
        if (!($this->EnDeposito instanceof ValoresSN))
            $this->EnDeposito = new ValoresSN($this->EnDeposito);
        return $this->EnDeposito;
    }

}

// END class existencias
?>