<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 14.05.2012 17:19:59
 */

/**
 * @orm:Entity(rutas_reparto)
 */
class RutasRepartoEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="rutas_reparto")
     */
    protected $IDRuta;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="zonas")
     */
    protected $IDSucursal;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="rutas_reparto")
     */
    protected $Descripcion;
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'rutas_reparto';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDRuta';
    /**
     * Relacion de entidades que dependen de esta
     * @var string
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDRuta', 'ParentEntity' => 'RutasRepartoDetalle', 'ParentColumn' => 'IDRuta'),
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
    public function setIDRuta($IDRuta) {
        $this->IDRuta = $IDRuta;
    }

    public function getIDRuta() {
        return $this->IDRuta;
    }

    public function setIDSucursal($IDSucursal) {
        $this->IDSucursal = $IDSucursal;
    }

    public function getIDSucursal() {
        if (!($this->IDSucursal instanceof Sucursales))
            $this->IDSucursal = new Sucursales($this->IDSucursal);
        return $this->IDSucursal;
    }

    public function setDescripcion($Descripcion) {
        $this->Descripcion = trim($Descripcion);
    }

    public function getDescripcion() {
        return $this->Descripcion;
    }

}

// END class rutas_reparto
?>