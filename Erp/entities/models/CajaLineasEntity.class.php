<?php

/**
 * Apuntes de Caja
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 24.04.2012 16:52:57
 */

/**
 * @orm:Entity(caja_lineas)
 */
class CajaLineasEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_lineas")
     */
    protected $IDApunte;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_lineas")
     * @var entities\CajaArqueos
     */
    protected $IDArqueo;
    /**
     * @orm:Column(type="datetime")
     * @assert:NotBlank(groups="caja_lineas")
     */
    protected $Fecha = '0000-00-00 00:00:00';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="caja_lineas")
     */
    protected $Concepto;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_lineas")
     * @var entities\FormasPago
     */
    protected $IDFP;
    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="caja_lineas")
     * @var entities\OrigenesApunteCaja
     */
    protected $Origen = '0';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="caja_lineas")
     */
    protected $Entidad;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_lineas")
     */
    protected $IDEntidad = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_lineas")
     */
    protected $Importe = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_lineas")
     */
    protected $Entregado = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_lineas")
     */
    protected $Cambio = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_lineas")
     */
    protected $Asiento = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_lineas")
     * @var entities\Datafonos
     */
    protected $IDDatafono = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="caja_lineas")
     * @var entities\Agentes
     */
    protected $IDAgente;
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'caja_lineas';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDApunte';
    /**
     * Relacion de entidades que dependen de esta
     * @var string
     */
    protected $_parentEntities = array(
    );
    /**
     * Relacion de entidades de las que esta depende
     * @var string
     */
    protected $_childEntities = array(
        'CajaArqueos',
        'FormasPago',
        'OrigenesApunteCaja',
        'Datafonos',
        'Agentes',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDApunte($IDApunte) {
        $this->IDApunte = $IDApunte;
    }

    public function getIDApunte() {
        return $this->IDApunte;
    }

    public function setIDArqueo($IDArqueo) {
        $this->IDArqueo = $IDArqueo;
    }

    public function getIDArqueo() {
        if (!($this->IDArqueo instanceof CajaArqueos))
            $this->IDArqueo = new CajaArqueos($this->IDArqueo);
        return $this->IDArqueo;
    }

    public function setFecha($Fecha) {
        $this->Fecha = $Fecha;
    }

    public function getFecha() {
        return $this->Fecha;
    }

    public function setConcepto($Concepto) {
        $this->Concepto = trim($Concepto);
    }

    public function getConcepto() {
        return $this->Concepto;
    }

    public function setIDFP($IDFP) {
        $this->IDFP = $IDFP;
    }

    public function getIDFP() {
        if (!($this->IDFP instanceof FormasPago))
            $this->IDFP = new FormasPago($this->IDFP);
        return $this->IDFP;
    }

    public function setOrigen($Origen) {
        $this->Origen = $Origen;
    }

    public function getOrigen() {
        if (!($this->Origen instanceof OrigenesApunteCaja))
            $this->Origen = new OrigenesApunteCaja($this->Origen);
        return $this->Origen;
    }

    public function setEntidad($Entidad) {
        $this->Entidad = trim($Entidad);
    }

    public function getEntidad() {
        return $this->Entidad;
    }

    public function setIDEntidad($IDEntidad) {
        $this->IDEntidad = $IDEntidad;
    }

    public function getIDEntidad() {
        return $this->IDEntidad;
    }

    public function setImporte($Importe) {
        $this->Importe = $Importe;
    }

    public function getImporte() {
        return $this->Importe;
    }

    public function setEntregado($Entregado) {
        $this->Entregado = $Entregado;
    }

    public function getEntregado() {
        return $this->Entregado;
    }

    public function setCambio($Cambio) {
        $this->Cambio = $Cambio;
    }

    public function getCambio() {
        return $this->Cambio;
    }

    public function setAsiento($Asiento) {
        $this->Asiento = $Asiento;
    }

    public function getAsiento() {
        return $this->Asiento;
    }

    public function setIDDatafono($IDDatafono) {
        $this->IDDatafono = $IDDatafono;
    }

    public function getIDDatafono() {
        if (!($this->IDDatafono instanceof Datafonos))
            $this->IDDatafono = new Datafonos($this->IDDatafono);
        return $this->IDDatafono;
    }

    public function setIDAgente($IDAgente) {
        $this->IDAgente = $IDAgente;
    }

    public function getIDAgente() {
        if (!($this->IDAgente instanceof Agentes))
            $this->IDAgente = new Agentes($this->IDAgente);
        return $this->IDAgente;
    }

}

// END class caja_lineas
?>