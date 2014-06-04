<?php

/**
 * Direcciones de Entrega de Clientes
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(clientes_dentrega)
 */
class ClientesDentregaEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $IDDirec = '';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $IDCliente;

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $Nombre;

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $Direccion;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $IDPais = '73';

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $Poblacion;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $IDProvincia = '18';

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $CodPostal = '0000000000';

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $Telefono = '';

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $Movil = '';

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $Fax = '';

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $EMail = '';

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $Responsable = '';

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $Horario = '';

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $HorarioLlamadas = '';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $IDComercial;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="clientes_dentrega")
     */
    protected $IDZona;

    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';

    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'clientes_dentrega';

    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDDirec';

    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDDirec', 'ParentEntity' => 'AlbaranesCab', 'ParentColumn' => 'IDDirec'),
        array('SourceColumn' => 'IDDirec', 'ParentEntity' => 'PstoCab', 'ParentColumn' => 'IDDirec'),
        array('SourceColumn' => 'IDDirec', 'ParentEntity' => 'RutasReparto', 'ParentColumn' => 'IDDirec'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDDirec($IDDirec) {
        $this->IDDirec = $IDDirec;
    }

    public function getIDDirec() {
        return $this->IDDirec;
    }

    public function setIDCliente($IDCliente) {
        $this->IDCliente = $IDCliente;
    }

    public function getIDCliente() {
        if (!($this->IDClientes instanceof Clientes))
            $this->IDCliente = new Clientes($this->IDCliente);
        return $this->IDCliente;
    }

    public function setNombre($Nombre) {
        $this->Nombre = trim($Nombre);
    }

    public function getNombre() {
        return $this->Nombre;
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

    public function setCodPostal($CodPostal) {
        $this->CodPostal = trim($CodPostal);
    }

    public function getCodPostal() {
        return $this->CodPostal;
    }

    public function setTelefono($Telefono) {
        $this->Telefono = trim($Telefono);
    }

    public function getTelefono() {
        return $this->Telefono;
    }

    public function setMovil($Movil) {
        $this->Movil = trim($Movil);
    }

    public function getMovil() {
        return $this->Movil;
    }

    public function setFax($Fax) {
        $this->Fax = trim($Fax);
    }

    public function getFax() {
        return $this->Fax;
    }

    public function setEMail($EMail) {
        $this->EMail = trim($EMail);
    }

    public function getEMail() {
        return $this->EMail;
    }

    public function setResponsable($Responsable) {
        $this->Responsable = trim($Responsable);
    }

    public function getResponsable() {
        return $this->Responsable;
    }

    public function setHorario($Horario) {
        $this->Horario = trim($Horario);
    }

    public function getHorario() {
        return $this->Horario;
    }

    public function setHorarioLlamadas($HorarioLlamadas) {
        $this->HorarioLlamadas = trim($HorarioLlamadas);
    }

    public function getHorarioLlamadas() {
        return $this->HorarioLlamadas;
    }

    public function setIDComercial($IDComercial) {
        $this->IDComercial = $IDComercial;
    }

    public function getIDComercial() {
        if (!($this->IDComercial instanceof Agentes))
            $this->IDComercial = new Agentes($this->IDComercial);
        return $this->IDComercial;
    }

    public function setIDZona($IDZona) {
        $this->IDZona = $IDZona;
    }

    public function getIDZona() {
        if (!($this->IDZona instanceof Zonas))
            $this->IDZona = new Zonas($this->IDZona);
        return $this->IDZona;
    }

}

// END class clientes_dentrega
?>