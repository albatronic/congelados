<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:48
 */

/**
 * @orm:Entity(sms)
 */
class SmsEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="sms")
     */
    protected $IDSms;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="sms")
     */
    protected $CuentaSms;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="sms")
     */
    protected $IDUsuario;
    /**
     * @orm:Column(type="datetime")
     * @assert:NotBlank(groups="sms")
     */
    protected $Fecha;
    /**
     * @orm:Column(type="string")
     */
    protected $Remite;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="sms")
     */
    protected $Para;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="sms")
     */
    protected $Mensaje;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="sms")
     */
    protected $EnviadoOk;
    /**
     * @orm:Column(type="string")
     */
    protected $Resultado;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="sms")
     */
    protected $NDestinatarios = '0';
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'sms';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDSms';

    /**
     * GETTERS Y SETTERS
     */
    public function setIDSms($IDSms) {
        $this->IDSms = $IDSms;
    }

    public function getIDSms() {
        return $this->IDSms;
    }

    public function setCuentaSms($CuentaSms) {
        $this->CuentaSms = trim($CuentaSms);
    }

    public function getCuentaSms() {
        return $this->CuentaSms;
    }

    public function setIDUsuario($IDUsuario) {
        $this->IDUsuario = $IDUsuario;
    }

    public function getIDUsuario() {
        if (!($this->IDUsuario instanceof Agentes))
            $this->IDUsuario = new Agentes($this->IDUsuario);
        return $this->IDUsuario;
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

    public function setRemite($Remite) {
        $this->Remite = trim($Remite);
    }

    public function getRemite() {
        return $this->Remite;
    }

    public function setPara($Para) {
        $this->Para = trim($Para);
    }

    public function getPara() {
        return $this->Para;
    }

    public function setMensaje($Mensaje) {
        $this->Mensaje = trim($Mensaje);
    }

    public function getMensaje() {
        return $this->Mensaje;
    }

    public function setEnviadoOk($EnviadoOk) {
        $this->EnviadoOk = $EnviadoOk;
    }

    public function getEnviadoOk() {
        if (!($this->EnviadoOK instanceof ValoresSN))
            $this->EnviadoOK = new ValoresSN($this->EnviadoOK);
        return $this->EnviadoOK;
    }

    public function setResultado($Resultado) {
        $this->Resultado = trim($Resultado);
    }

    public function getResultado() {
        return $this->Resultado;
    }

    public function setNDestinatarios($NDestinatarios) {
        $this->NDestinatarios = $NDestinatarios;
    }

    public function getNDestinatarios() {
        return $this->NDestinatarios;
    }

}

// END class sms
?>