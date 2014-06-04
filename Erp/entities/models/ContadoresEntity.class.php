<?php

/**
 * Contadores
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 15.02.2012 12:41:19
 */

/**
 * @orm:Entity(contadores)
 */
class ContadoresEntity extends Entity {

    /**
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contadores")
     */
    protected $IDContador;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contadores")
     * @var entities\Sucursales
     */
    protected $IDSucursal;
    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="contadores")
     * @var entities\TiposContadores
     */
    protected $IDTipo;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="contadores")
     */
    protected $Serie;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contadores")
     */
    protected $Contador = '0';
    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="contadores")
     * @var entities\ValoresSN
     */
    protected $Predefinido = '0';
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'contadores';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDContador';
    /**
     * Relacion de entidades que dependen de esta
     * @var string
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDTipo', 'ParentEntity' => 'Contadores', 'ParentColumn' => 'IDTipo'),
    );
    /**
     * Relacion de entidades de las que esta depende
     * @var string
     */
    protected $_childEntities = array(
        'Sucursales',
        'TiposContadores',
        'ValoresSN',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDContador($IDContador) {
        $this->IDContador = $IDContador;
    }

    public function getIDContador() {
        return $this->IDContador;
    }

    public function setIDSucursal($IDSucursal) {
        $this->IDSucursal = $IDSucursal;
    }

    public function getIDSucursal() {
        if (!($this->IDSucursal instanceof Sucursales))
            $this->IDSucursal = new Sucursales($this->IDSucursal);
        return $this->IDSucursal;
    }

    public function setIDTipo($IDTipo) {
        $this->IDTipo = $IDTipo;
    }

    public function getIDTipo() {
        if (!($this->IDTipo instanceof TiposContadores))
            $this->IDTipo = new TiposContadores($this->IDTipo);
        return $this->IDTipo;
    }

    public function setSerie($Serie) {
        $this->Serie = trim($Serie);
    }

    public function getSerie() {
        return $this->Serie;
    }

    public function setContador($Contador) {
        $this->Contador = $Contador;
    }

    public function getContador() {
        return $this->Contador;
    }

    public function setPredefinido($Predefinido) {
        $this->Predefinido = $Predefinido;
    }

    public function getPredefinido() {
        if (!($this->Predefinido instanceof ValoresSN))
            $this->Predefinido = new ValoresSN($this->Predefinido);
        return $this->Predefinido;
    }

}

// END class contadores
?>