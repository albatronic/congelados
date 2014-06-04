<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 24.08.2011 16:51:13
 */

/**
 * @orm:Entity(zonas)
 */
class ZonasEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="zonas")
     */
    protected $IDZona;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="zonas")
     */
    protected $IDSucursal;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="zonas")
     */
    protected $Zona;
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'zonas';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDZona';
    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDZona', 'ParentEntity' => 'Clientes', 'ParentColumn' => 'IDZona'),
        array('SourceColumn' => 'IDZona', 'ParentEntity' => 'ClientesDentrega', 'ParentColumn' => 'IDZona'),
        array('SourceColumn' => 'IDZona', 'ParentEntity' => 'RutasReparto', 'ParentColumn' => 'IDZona'),
        array('SourceColumn' => 'IDZona', 'ParentEntity' => 'RutasVentas', 'ParentColumn' => 'IDZona'),
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

    public function setIDSucursal($IDSucursal) {
        $this->IDSucursal = $IDSucursal;
    }

    public function getIDSucursal() {
        if (!($this->IDSucursal instanceof Sucursales))
            $this->IDSucursal = new Sucursales($this->IDSucursal);
        return $this->IDSucursal;
    }

    public function setZona($Zona) {
        $this->Zona = trim($Zona);
    }

    public function getZona() {
        return $this->Zona;
    }

}

// END class zonas
?>