<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:48
 */

/**
 * @orm:Entity(tabla_portes)
 */
class TablaPortesEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="tabla_portes")
     */
    protected $Id;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="tabla_portes")
     */
    protected $IDZona;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="tabla_portes")
     */
    protected $IDAgencia;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="tabla_portes")
     */
    protected $Kilos;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="tabla_portes")
     */
    protected $Importe;
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'tabla_portes';
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

    public function setIDZona($IDZona) {
        $this->IDZona = $IDZona;
    }

    public function getIDZona() {
        if (!($this->IDZona instanceof ZonasTransporte))
            $this->IDZona = new ZonasTransporte($this->IDZona);
        return $this->IDZona;
    }

    public function setIDAgencia($IDAgencia) {
        $this->IDAgencia = $IDAgencia;
    }

    public function getIDAgencia() {
        if (!($this->IDAgencia instanceof Agencias))
            $this->IDAgencia = new Agencias($this->IDAgencia);
        return $this->IDAgencia;
    }

    public function setKilos($Kilos) {
        $this->Kilos = $Kilos;
    }

    public function getKilos() {
        return $this->Kilos;
    }

    public function setImporte($Importe) {
        $this->Importe = $Importe;
    }

    public function getImporte() {
        return $this->Importe;
    }

}

// END class tabla_portes
?>