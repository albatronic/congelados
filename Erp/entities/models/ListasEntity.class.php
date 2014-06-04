<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(listas)
 */
class ListasEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="listas")
     */
    protected $IDLista;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="listas")
     */
    protected $Descripcion;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="listas")
     */
    protected $Vigencia;
    /**
     * @orm:Column(type="integer")
     */
    protected $Periodicidad;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="listas")
     */
    protected $UltimoEnvio;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="listas")
     */
    protected $ProximoEnvio;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="listas")
     */
    protected $Tabla;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="listas")
     */
    protected $Condiciones;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="listas")
     */
    protected $Columnas;
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'listas';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDLista';

    /**
     * GETTERS Y SETTERS
     */
    public function setIDLista($IDLista) {
        $this->IDLista = $IDLista;
    }

    public function getIDLista() {
        return $this->IDLista;
    }

    public function setDescripcion($Descripcion) {
        $this->Descripcion = trim($Descripcion);
    }

    public function getDescripcion() {
        return $this->Descripcion;
    }

    public function setVigencia($Vigencia) {
        $this->Vigencia = $Vigencia;
    }

    public function getVigencia() {
        return $this->Vigencia;
    }

    public function setPeriodicidad($Periodicidad) {
        $this->Periodicidad = $Periodicidad;
    }

    public function getPeriodicidad() {
        return $this->Periodicidad;
    }

    public function setUltimoEnvio($UltimoEnvio) {
        $this->UltimoEnvio = $UltimoEnvio;
    }

    public function getUltimoEnvio() {
        return $this->UltimoEnvio;
    }

    public function setProximoEnvio($ProximoEnvio) {
        $this->ProximoEnvio = $ProximoEnvio;
    }

    public function getProximoEnvio() {
        return $this->ProximoEnvio;
    }

    public function setTabla($Tabla) {
        $this->Tabla = trim($Tabla);
    }

    public function getTabla() {
        return $this->Tabla;
    }

    public function setCondiciones($Condiciones) {
        $this->Condiciones = trim($Condiciones);
    }

    public function getCondiciones() {
        return $this->Condiciones;
    }

    public function setColumnas($Columnas) {
        $this->Columnas = $Columnas;
    }

    public function getColumnas() {
        return $this->Columnas;
    }

}

// END class listas
?>