<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:48
 */

/**
 * @orm:Entity(zonas_transporte)
 */
class ZonasTransporteEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="zonas_transporte")
     */
    protected $IDZona;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="zonas_transporte")
     */
    protected $Zona = '';
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'zonas_transporte';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDZona';
    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDZona', 'ParentEntity' => 'Provincias', 'ParentColumn' => 'IDZona'),
        array('SourceColumn' => 'IDZona', 'ParentEntity' => 'TablaPortes', 'ParentColumn' => 'IDZona'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDZona($IDZona) {
        $this->IDZona = $IDZona;
    }

    public function getIDZona() {
        return $this->IDZona;
    }

    public function setZona($Zona) {
        $this->Zona = trim($Zona);
    }

    public function getZona() {
        return $this->Zona;
    }

}

// END class zonas_transporte
?>