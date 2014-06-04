<?php

/**
 * Fabricantes
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(fabricantes)
 */
class FabricantesEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="fabricantes")
     */
    protected $IDFabricante;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="fabricantes")
     */
    protected $Fabricante = '';
    /**
     * @orm:Column(type="string")
     */
    protected $Telefono;
    /**
     * @orm:Column(type="string")
     */
    protected $Web;
    /**
     * @orm:Column(type="string")
     */
    protected $Email;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="fabricantes")
     */
    protected $Icono;
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'fabricantes';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDFabricante';
    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDFabricante', 'ParentEntity' => 'Articulos', 'ParentColumn' => 'IDFabricante'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDFabricante($IDFabricante) {
        $this->IDFabricante = $IDFabricante;
    }

    public function getIDFabricante() {
        return $this->IDFabricante;
    }

    public function setFabricante($Fabricante) {
        $this->Fabricante = trim($Fabricante);
    }

    public function getFabricante() {
        return $this->Fabricante;
    }

    public function setTelefono($Telefono) {
        $this->Telefono = trim($Telefono);
    }

    public function getTelefono() {
        return $this->Telefono;
    }

    public function setWeb($Web) {
        $this->Web = trim($Web);
    }

    public function getWeb() {
        return $this->Web;
    }

    public function setEmail($Email) {
        $this->Email = trim($Email);
    }

    public function getEmail() {
        return $this->Email;
    }

    public function setIcono($Icono) {
        $this->Icono = $Icono;
    }

    public function getIcono() {
        return $this->Icono;
    }

}

// END class fabricantes
?>