<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 10.07.2011 18:54:38
 */

/**
 * @orm:Entity(unidades_medida)
 */
class UnidadesMedidaEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="unidades_medida")
     */
    protected $Id;
    /**
     * @orm:Column(type="string")
     */
    protected $UnidadMedida = '';
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'empresas';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'unidades_medida';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'Id';
    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'Id', 'ParentEntity' => 'Articulos', 'ParentColumn' => 'UMB'),
        array('SourceColumn' => 'Id', 'ParentEntity' => 'Articulos', 'ParentColumn' => 'UMC'),
        array('SourceColumn' => 'Id', 'ParentEntity' => 'Articulos', 'ParentColumn' => 'UMA'),
        array('SourceColumn' => 'Id', 'ParentEntity' => 'Articulos', 'ParentColumn' => 'UMV'),
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

    public function setUnidadMedida($UnidadMedida) {
        $this->UnidadMedida = trim($UnidadMedida);
    }

    public function getUnidadMedida() {
        return $this->UnidadMedida;
    }

}

// END class unidades_medida
?>