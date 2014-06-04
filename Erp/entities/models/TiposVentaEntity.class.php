<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:48
 */

/**
 * @orm:Entity(tipos_venta)
 */
class TiposVentaEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="tipos_venta")
     */
    protected $IDVenta;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="tipos_venta")
     */
    protected $TipoVenta;
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'tipos_venta';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDVenta';
    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDVenta', 'ParentEntity' => 'AlbaranesLineas', 'ParentColumn' => 'IDVenta'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDVenta($IDVenta) {
        $this->IDVenta = $IDVenta;
    }

    public function getIDVenta() {
        return $this->IDVenta;
    }

    public function setTipoVenta($TipoVenta) {
        $this->TipoVenta = trim($TipoVenta);
    }

    public function getTipoVenta() {
        return $this->TipoVenta;
    }

}

// END class tipos_venta
?>