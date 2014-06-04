<?php

/**
 * Almacenes
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 19:20:35
 */

/**
 * @orm:Entity(almacenes)
 */
class AlmacenesEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="almacenes")
     */
    protected $IDAlmacen;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="almacenes")
     */
    protected $Nombre = '';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="almacenes")
     */
    protected $Direccion;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="almacenes")
     */
    protected $Poblacion;
    /**
     * @orm:Column(type="")
     * @assert:NotBlank(groups="almacenes")
     */
    protected $IDProvincia = '18';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="almacenes")
     */
    protected $CodigoPostal = '0000000000';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="almacenes")
     */
    protected $Telefono;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="almacenes")
     */
    protected $Fax;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="almacenes")
     */
    protected $EMail;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="almacenes")
     */
    protected $Tipo = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="almacenes")
     */
    protected $ControlUbicaciones = '0';
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'empresas';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'almacenes';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDAlmacen';
    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDAlmacen', 'ParentEntity' => 'EmpresasAlmacenes', 'ParentColumn' => 'IDAlmacen'),
        array('SourceColumn' => 'IDAlmacen', 'ParentEntity' => 'AlmacenesMapas', 'ParentColumn' => 'IDAlmacen'),
        array('SourceColumn' => 'IDAlmacen', 'ParentEntity' => 'Existencias', 'ParentColumn' => 'IDAlmacen'),
        array('SourceColumn' => 'IDAlmacen', 'ParentEntity' => 'AlbaranesCab', 'ParentColumn' => 'IDAlmacen'),
        array('SourceColumn' => 'IDAlmacen', 'ParentEntity' => 'PedidosCab', 'ParentColumn' => 'IDAlmacen'),
        array('SourceColumn' => 'IDAlmacen', 'ParentEntity' => 'TraspasosCab', 'ParentColumn' => 'IDAlmacenOrigen'),
        array('SourceColumn' => 'IDAlmacen', 'ParentEntity' => 'TraspasosCab', 'ParentColumn' => 'IDAlmacenDestino'),
        array('SourceColumn' => 'IDAlmacen', 'ParentEntity' => 'MvtosAlmacen', 'ParentColumn' => 'IDAlmacen'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDAlmacen($IDAlmacen) {
        $this->IDAlmacen = $IDAlmacen;
    }

    public function getIDAlmacen() {
        return $this->IDAlmacen;
    }

    public function setNombre($Nombre) {
        $this->Nombre = $Nombre;
    }

    public function getNombre() {
        return $this->Nombre;
    }

    public function setDireccion($Direccion) {
        $this->Direccion = $Direccion;
    }

    public function getDireccion() {
        return $this->Direccion;
    }

    public function setPoblacion($Poblacion) {
        $this->Poblacion = $Poblacion;
    }

    public function getPoblacion() {
        return $this->Poblacion;
    }

    public function setIDProvincia($IDProvincia) {
        $this->IDProvincia = $IDProvincia;
    }

    public function getIDProvincia() {
        if (!($this->IDProvincia instanceof Provincias))
            $this->IDProvincia = new Provincias($this->IDProvincia);
        return $this->IDProvincia;
    }

    public function setCodigoPostal($CodigoPostal) {
        $this->CodigoPostal = $CodigoPostal;
    }

    public function getCodigoPostal() {
        return $this->CodigoPostal;
    }

    public function setTelefono($Telefono) {
        $this->Telefono = $Telefono;
    }

    public function getTelefono() {
        return $this->Telefono;
    }

    public function setFax($Fax) {
        $this->Fax = $Fax;
    }

    public function getFax() {
        return $this->Fax;
    }

    public function setEMail($EMail) {
        $this->EMail = $EMail;
    }

    public function getEMail() {
        return $this->EMail;
    }

    public function setTipo($Tipo) {
        $this->Tipo = $Tipo;
    }

    public function getTipo() {
        if (!($this->Tipo instanceof AlmacenesTipos))
            $this->Tipo = new AlmacenesTipos($this->Tipo);
        return $this->Tipo;
    }

    public function setControlUbicaciones($ControlUbicaciones) {
        $this->ControlUbicaciones = $ControlUbicaciones;
    }

    public function getControlUbicaciones() {
        if (!$this->ControlUbicaciones instanceof ValoresSN)
            $this->ControlUbicaciones = new ValoresSN($this->ControlUbicaciones);
        return $this->ControlUbicaciones;
    }

}

// END class almacenes
?>