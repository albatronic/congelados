<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(proveedores)
 */
class ProveedoresEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="proveedores")
     */
    protected $IDProveedor;

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="proveedores")
     */
    protected $RazonSocial;

    /**
     * @orm:Column(type="string")
     */
    protected $NombreComercial;

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="proveedores")
     */
    protected $Cif;

    /**
     * @orm:Column(type="string")
     */
    protected $Direccion;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="proveedores")
     */
    protected $IDPais = '73';

    /**
     * @orm:Column(type="string")
     */
    protected $Poblacion;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="proveedores")
     */
    protected $IDProvincia = '18';

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="proveedores")
     */
    protected $CodigoPostal = '0000000000';

    /**
     * @orm:Column(type="string")
     */
    protected $Telefono;

    /**
     * @orm:Column(type="string")
     */
    protected $Fax;

    /**
     * @orm:Column(type="string")
     */
    protected $Movil;

    /**
     * @orm:Column(type="string")
     */
    protected $EMail;

    /**
     * @orm:Column(type="string")
     */
    protected $Web;

    /**
     * @orm:Column(type="string")
     */
    protected $CContable = '0000000000';

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="proveedores")
     */
    protected $IDBanco = '0000';

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="proveedores")
     */
    protected $IDOficina = '0000';

    /**
     * @orm:Column(type="")
     * @assert:NotBlank(groups="proveedores")
     */
    protected $Digito = '00';

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="proveedores")
     */
    protected $Cuenta = '0000000000';

    /**
     * @orm:Column(type="string")
     */
    protected $Observaciones = '';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="proveedores")
     */
    protected $IDFP = '0';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="proveedores")
     */
    protected $Iva = '1';

    /**
     * @orm:Column(type="")
     * @assert:NotBlank(groups="clientes")
     */
    protected $Vigente = '1';

    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';

    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'proveedores';

    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDProveedor';

    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDProveedor', 'ParentEntity' => 'PedidosCab', 'ParentColumn' => 'IDProveedor'),
        array('SourceColumn' => 'IDProveedor', 'ParentEntity' => 'FrecibidasCab', 'ParentColumn' => 'IDProveedor'),
        array('SourceColumn' => 'IDProveedor', 'ParentEntity' => 'RecibosProveedores', 'ParentColumn' => 'IDProveedor'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDProveedor($IDProveedor) {
        $this->IDProveedor = $IDProveedor;
    }

    public function getIDProveedor() {
        return $this->IDProveedor;
    }

    public function setRazonSocial($RazonSocial) {
        $this->RazonSocial = trim($RazonSocial);
    }

    public function getRazonSocial() {
        return $this->RazonSocial;
    }

    public function setNombreComercial($NombreComercial) {
        $this->NombreComercial = trim($NombreComercial);
    }

    public function getNombreComercial() {
        return $this->NombreComercial;
    }

    public function setCif($Cif) {
        $this->Cif = trim($Cif);
    }

    public function getCif() {
        return $this->Cif;
    }

    public function setDireccion($Direccion) {
        $this->Direccion = trim($Direccion);
    }

    public function getDireccion() {
        return $this->Direccion;
    }

    public function setIDPais($IDPais) {
        $this->IDPais = $IDPais;
    }

    public function getIDPais() {
        if (!($this->IDPais instanceof Paises))
            $this->IDPais = new Paises($this->IDPais);
        return $this->IDPais;
    }

    public function setPoblacion($Poblacion) {
        $this->Poblacion = trim($Poblacion);
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
        $this->CodigoPostal = trim($CodigoPostal);
    }

    public function getCodigoPostal() {
        return $this->CodigoPostal;
    }

    public function setTelefono($Telefono) {
        $this->Telefono = trim($Telefono);
    }

    public function getTelefono() {
        return $this->Telefono;
    }

    public function setFax($Fax) {
        $this->Fax = trim($Fax);
    }

    public function getFax() {
        return $this->Fax;
    }

    public function setMovil($Movil) {
        $this->Movil = trim($Movil);
    }

    public function getMovil() {
        return $this->Movil;
    }

    public function setEMail($EMail) {
        $this->EMail = trim($EMail);
    }

    public function getEMail() {
        return $this->EMail;
    }

    public function setWeb($Web) {
        $this->Web = trim($Web);
    }

    public function getWeb() {
        return $this->Web;
    }

    public function setCContable($CContable) {
        $this->CContable = trim($CContable);
    }

    public function getCContable() {
        return $this->CContable;
    }

    public function setIDBanco($IDBanco) {
        $this->IDBanco = $IDBanco;
    }

    public function getIDBanco() {
        return $this->IDBanco;
    }

    public function setIDOficina($IDOficina) {
        $this->IDOficina = $IDOficina;
    }

    public function getIDOficina() {
        return $this->IDOficina;
    }

    public function setDigito($Digito) {
        $this->Digito = $Digito;
    }

    public function getDigito() {
        return $this->Digito;
    }

    public function setCuenta($Cuenta) {
        $this->Cuenta = $Cuenta;
    }

    public function getCuenta() {
        return $this->Cuenta;
    }

    public function setObservaciones($Observaciones) {
        $this->Observaciones = trim($Observaciones);
    }

    public function getObservaciones() {
        return $this->Observaciones;
    }

    public function setIDFP($IDFP) {
        $this->IDFP = $IDFP;
    }

    public function getIDFP() {
        if (!($this->IDFP instanceof FormasPago))
            $this->IDFP = new FormasPago($this->IDFP);
        return $this->IDFP;
    }

    public function setIva($Iva) {
        $this->Iva = $Iva;
    }

    public function getIva() {
        if (!($this->Iva instanceof ValoresSN))
            $this->Iva = new ValoresSN($this->Iva);
        return $this->Iva;
    }

    public function setVigente($Vigente) {
        $this->Vigente = $Vigente;
    }

    public function getVigente() {
        if (!($this->Vigente instanceof ValoresSN))
            $this->Vigente = new ValoresSN($this->Vigente);
        return $this->Vigente;
    }

}

// END class proveedores
?>