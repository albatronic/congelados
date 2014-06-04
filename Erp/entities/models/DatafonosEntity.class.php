<?php

/**
 * Datafonos
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 15.04.2012 00:54:30
 */

/**
 * @orm:Entity(datafonos)
 */
class DatafonosEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="datafonos")
     */
    protected $IDDatafono;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="datafonos")
     */
    protected $Datafono;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="datafonos")
     */
    protected $Comision = '0.00';
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'datafonos';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDDatafono';
    /**
     * Relacion de entidades que dependen de esta
     * @var string
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDDatafono', 'ParentEntity' => 'Tpvs', 'ParentColumn' => 'IDDataFono1'),
        array('SourceColumn' => 'IDDatafono', 'ParentEntity' => 'Tpvs', 'ParentColumn' => 'IDDataFono2'),
        array('SourceColumn' => 'IDDatafono', 'ParentEntity' => 'Tpvs', 'ParentColumn' => 'IDDataFono3'),
        array('SourceColumn' => 'IDDatafono', 'ParentEntity' => 'Tpvs', 'ParentColumn' => 'IDDataFono4'),
        array('SourceColumn' => 'IDDatafono', 'ParentEntity' => 'CajaLineas', 'ParentColumn' => 'IDDatafono'),
    );
    /**
     * Relacion de entidades de las que esta depende
     * @var string
     */
    protected $_childEntities = array(
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDDatafono($IDDatafono) {
        $this->IDDatafono = $IDDatafono;
    }

    public function getIDDatafono() {
        return $this->IDDatafono;
    }

    public function setDatafono($Datafono) {
        $this->Datafono = trim($Datafono);
    }

    public function getDatafono() {
        return $this->Datafono;
    }

    public function setComision($Comision) {
        $this->Comision = $Comision;
    }

    public function getComision() {
        return $this->Comision;
    }

}

// END class datafonos
?>