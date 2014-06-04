<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 23.11.2011 17:25:14
 */

/**
 * @orm:Entity(rutas_ventas)
 */
class RutasVentasEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="rutas_ventas")
     */
    protected $Id;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="rutas_ventas")
     * @var entities\Agentes
     */
    protected $IDComercial;
    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="rutas_ventas")
     */
    protected $Dia;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="rutas_ventas")
     */
    protected $OrdenZona = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="rutas_ventas")
     * @var entities\Zonas
     */
    protected $IDZona = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="rutas_ventas")
     */
    protected $OrdenCliente = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="rutas_ventas")
     * @var entities\Clientes
     */
    protected $IDCliente;
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'rutas_ventas';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'Id';
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
        'Agentes',
        'Zonas',
        'Clientes',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setId($Id) {
        $this->Id = $Id;
    }

    public function getId() {
        return $this->Id;
    }

    public function setIDComercial($IDComercial) {
        $this->IDComercial = $IDComercial;
    }

    public function getIDComercial() {
        if (!($this->IDComercial instanceof Agentes))
            $this->IDComercial = new Agentes($this->IDComercial);
        return $this->IDComercial;
    }

    public function setDia($Dia) {
        $this->Dia = $Dia;
    }

    public function getDia() {
        if (!($this->Dia instanceof DiasSemana))
            $this->Dia = new DiasSemana($this->Dia);
        return $this->Dia;
    }

    public function setOrdenZona($OrdenZona) {
        $this->OrdenZona = $OrdenZona;
    }

    public function getOrdenZona() {
        return $this->OrdenZona;
    }

    public function setIDZona($IDZona) {
        $this->IDZona = $IDZona;
    }

    public function getIDZona() {
        if (!($this->IDZona instanceof Zonas))
            $this->IDZona = new Zonas($this->IDZona);
        return $this->IDZona;
    }

    public function setOrdenCliente($OrdenCliente) {
        $this->OrdenCliente = $OrdenCliente;
    }

    public function getOrdenCliente() {
        return $this->OrdenCliente;
    }

    public function setIDCliente($IDCliente) {
        $this->IDCliente = $IDCliente;
    }

    public function getIDCliente() {
        if (!($this->IDCliente instanceof Clientes))
            $this->IDCliente = new Clientes($this->IDCliente);
        return $this->IDCliente;
    }

}

// END class rutas_ventas
?>