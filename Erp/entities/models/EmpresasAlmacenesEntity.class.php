<?php

/**
 * Relacion entre Empresas y Almacenes
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 13.06.2011 23:41:32
 */

/**
 * @orm:Entity(empresas_almacenes)
 */
class EmpresasAlmacenesEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="empresas_almacenes")
     */
    protected $Id;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="empresas_almacenes")
     */
    protected $IDEmpresa = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="empresas_almacenes")
     */
    protected $IDAlmacen = '0';
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'empresas';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'empresas_almacenes';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'Id';

    /**
     * GETTERS Y SETTERS
     */
    public function setId($Id) {
        $this->Id = $Id;
    }

    public function getId() {
        return $this->Id;
    }

    public function setIDEmpresa($IDEmpresa) {
        $this->IDEmpresa = $IDEmpresa;
    }

    public function getIDEmpresa() {
        if (!($this->IDEmpresa instanceof Empresas))
            $this->IDEmpresa = new Empresas($this->IDEmpresa);
        return $this->IDEmpresa;
    }

    public function setIDAlmacen($IDAlmacen) {
        $this->IDAlmacen = $IDAlmacen;
    }

    public function getIDAlmacen() {
        if (!($this->IDAlmacen instanceof Almacenes))
            $this->IDAlmacen = new Almacenes($this->IDAlmacen);
        return $this->IDAlmacen;
    }

}

// END class empresas_almacenes
?>