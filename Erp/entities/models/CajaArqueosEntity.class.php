<?php

/**
 * Cabecera de Arqueos de Caja
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 15.04.2012 00:47:17
 */

/**
 * @orm:Entity(caja_arqueos)
 */
class CajaArqueosEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_arqueos")
     */
    protected $IDArqueo;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_arqueos")
     * @var entities\Sucursales
     */
    protected $IDSucursal = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_arqueos")
     * @var entities\Tpvs
     */
    protected $IDTpv = '0';
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="caja_arqueos")
     */
    protected $Dia;
    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="caja_arqueos")
     * @var entities\ValoresSN
     */
    protected $CajaCerrada = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_arqueos")
     */
    protected $SaldoApertura = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_arqueos")
     */
    protected $SumaMvtos = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_arqueos")
     */
    protected $SaldoCierre = '0.00';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="caja_arqueos")
     */
    protected $Observaciones;
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'caja_arqueos';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDArqueo';
    /**
     * Relacion de entidades que dependen de esta
     * @var string
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDArqueo', 'ParentEntity' => 'CajaLineas', 'ParentColumn' => 'IDArqueo'),
    );
    /**
     * Relacion de entidades de las que esta depende
     * @var string
     */
    protected $_childEntities = array(
        'Sucursales',
        'Tpvs',
        'ValoresSN',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDArqueo($IDArqueo) {
        $this->IDArqueo = $IDArqueo;
    }

    public function getIDArqueo() {
        return $this->IDArqueo;
    }

    public function setIDSucursal($IDSucursal) {
        $this->IDSucursal = $IDSucursal;
    }

    public function getIDSucursal() {
        if (!($this->IDSucursal instanceof Sucursales))
            $this->IDSucursal = new Sucursales($this->IDSucursal);
        return $this->IDSucursal;
    }

    public function setIDTpv($IDTpv) {
        $this->IDTpv = $IDTpv;
    }

    public function getIDTpv() {
        if (!($this->IDTpv instanceof Tpvs))
            $this->IDTpv = new Tpvs($this->IDTpv);
        return $this->IDTpv;
    }

    public function setDia($Dia) {
        $date = new Fecha($Dia);
        $this->Dia = $date->getFecha();
        unset($date);
    }

    public function getDia() {
        $date = new Fecha($this->Dia);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setCajaCerrada($CajaCerrada) {
        $this->CajaCerrada = $CajaCerrada;
    }

    public function getCajaCerrada() {
        if (!($this->CajaCerrada instanceof ValoresSN))
            $this->CajaCerrada = new ValoresSN($this->CajaCerrada);
        return $this->CajaCerrada;
    }

    public function setSaldoApertura($SaldoApertura) {
        $this->SaldoApertura = $SaldoApertura;
    }

    public function getSaldoApertura() {
        return $this->SaldoApertura;
    }

    public function setSumaMvtos($SumaMvtos) {
        $this->SumaMvtos = $SumaMvtos;
    }

    public function getSumaMvtos() {
        return $this->SumaMvtos;
    }

    public function setSaldoCierre($SaldoCierre) {
        $this->SaldoCierre = $SaldoCierre;
    }

    public function getSaldoCierre() {
        return $this->SaldoCierre;
    }

    public function setObservaciones($Observaciones) {
        $this->Observaciones = trim($Observaciones);
    }

    public function getObservaciones() {
        return $this->Observaciones;
    }

}

// END class caja_arqueos
?>