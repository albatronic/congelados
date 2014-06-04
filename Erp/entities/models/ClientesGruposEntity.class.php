<?php

/**
 * Grupos de Clientes
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(clientes_grupos)
 */
class ClientesGruposEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="clientes_grupos")
     */
    protected $IDGrupo;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="clientes_grupos")
     */
    protected $Grupo;
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'clientes_grupos';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDGrupo';
    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDGrupo', 'ParentEntity' => 'Clientes', 'ParentColumn' => 'IDGrupo'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDGrupo($IDGrupo) {
        $this->IDGrupo = $IDGrupo;
    }

    public function getIDGrupo() {
        return $this->IDGrupo;
    }

    public function setGrupo($Grupo) {
        $this->Grupo = trim($Grupo);
    }

    public function getGrupo() {
        return $this->Grupo;
    }

}

// END class clientes_grupos
?>