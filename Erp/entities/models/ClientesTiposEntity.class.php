<?php

/**
 * Tipos de Clientes
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(clientes_tipos)
 */
class ClientesTiposEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="clientes_tipos")
     */
    protected $IDTipo;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="clientes_tipos")
     */
    protected $Tipo;
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'clientes_tipos';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDTipo';
    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDTipo', 'ParentEntity' => 'Clientes', 'ParentColumn' => 'IDTipo'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDTipo($IDTipo) {
        $this->IDTipo = $IDTipo;
    }

    public function getIDTipo() {
        return $this->IDTipo;
    }

    public function setTipo($Tipo) {
        $this->Tipo = $Tipo;
    }

    public function getTipo() {
        return $this->Tipo;
    }

}

// END class clientes_tipos
?>