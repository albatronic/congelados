<?php

/**
 * Personas de Contacto de Clientes
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(clientes_contactos)
 */
class ClientesContactosEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="clientes_contactos")
     */
    protected $IDContacto;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="clientes_contactos")
     */
    protected $IDCliente;
    /**
     * @orm:Column(type="string")
     */
    protected $Cargo;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="clientes_contactos")
     */
    protected $Nombre;
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
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'clientes_contactos';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDContacto';

    /**
     * GETTERS Y SETTERS
     */
    public function setIDContacto($IDContacto) {
        $this->IDContacto = $IDContacto;
    }

    public function getIDContacto() {
        return $this->IDContacto;
    }

    public function setIDCliente($IDCliente) {
        $this->IDCliente = $IDCliente;
    }

    public function getIDCliente() {
        return $this->IDCliente;
    }

    public function setCargo($Cargo) {
        $this->Cargo = $Cargo;
    }

    public function getCargo() {
        return $this->Cargo;
    }

    public function setNombre($Nombre) {
        $this->Nombre = $Nombre;
    }

    public function getNombre() {
        return $this->Nombre;
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

    public function setMovil($Movil) {
        $this->Movil = $Movil;
    }

    public function getMovil() {
        return $this->Movil;
    }

    public function setEMail($EMail) {
        $this->EMail = $EMail;
    }

    public function getEMail() {
        return $this->EMail;
    }

}

// END class clientes_contactos
?>