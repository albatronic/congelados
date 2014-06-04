<?php

/**
 * Estados de Articulos
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:46
 */

/**
 * @orm:Entity(articulos_estados)
 */
class ArticulosEstadosEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos_estados")
     */
    protected $IDEstado;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="articulos_estados")
     */
    protected $Estado;
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'articulos_estados';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDEstado';
    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDEstado', 'ParentEntity' => 'Articulos', 'ParentColumn' => 'IDEstado'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDEstado($IDEstado) {
        $this->IDEstado = $IDEstado;
    }
    public function getIDEstado() {
        return $this->IDEstado;
    }

    public function setEstado($Estado) {
        $this->Estado = $Estado;
    }
    public function getEstado() {
        return $this->Estado;
    }

}

// END class articulos_estados
?>