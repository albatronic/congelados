<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(log)
 */
class LogEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="log")
     */
    protected $IDEvento;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="log")
     */
    protected $Ip;
    /**
     * @orm:Column(type="datetime")
     * @assert:NotBlank(groups="log")
     */
    protected $Fecha = '0000-00-00 00:00:00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="log")
     */
    protected $IDAgente = '0';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="log")
     */
    protected $Evento;
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'log';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDEvento';

    /**
     * GETTERS Y SETTERS
     */
    public function setIDEvento($IDEvento) {
        $this->IDEvento = $IDEvento;
    }

    public function getIDEvento() {
        return $this->IDEvento;
    }

    public function setIp($Ip) {
        $this->Ip = $Ip;
    }

    public function getIp() {
        return $this->Ip;
    }

    public function setFecha($Fecha) {
        $date = new Fecha($Fecha);
        $this->Fecha = $date->getFecha();
        unset($date);
    }

    public function getFecha() {
        $date = new Fecha($this->Fecha);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setIDAgente($IDAgente) {
        $this->IDAgente = $IDAgente;
    }

    public function getIDAgente() {
        if (!($this->IDAgente instanceof Agentes))
            $this->IDAgente = new Agentes($this->IDAgente);
        return $this->IDAgente;
    }

    public function setEvento($Evento) {
        $this->Evento = $Evento;
    }

    public function getEvento() {
        return $this->Evento;
    }

}

// END class log
?>