<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 14.05.2012 18:12:47
 */

/**
 * @orm:Entity(rutas_reparto_detalle)
 */
class RutasRepartoDetalleEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="rutas_reparto_detalle")
     */
    protected $Id;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="rutas_reparto_detalle")
     * @var entities\RutasReparto
     */
    protected $IDRuta;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="rutas_reparto_detalle")
     * @var entities\Agentes
     */
    protected $IDRepartidor;
    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="rutas_reparto_detalle")
     */
    protected $Dia;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="rutas_reparto_detalle")
     */
    protected $OrdenZona = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="rutas_reparto_detalle")
     * @var entities\Zonas
     */
    protected $IDZona = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="rutas_reparto_detalle")
     */
    protected $OrdenDirec = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="rutas_reparto_detalle")
     * @var entities\ClientesDentrega
     */
    protected $IDDirec;
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'rutas_reparto_detalle';
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
        'RutasReparto',
        'Agentes',
        'Zonas',
        'ClientesDentrega',
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

    public function setIDRuta($IDRuta) {
        $this->IDRuta = $IDRuta;
    }

    public function getIDRuta() {
        if (!($this->IDRuta instanceof RutasReparto))
            $this->IDRuta = new RutasReparto($this->IDRuta);
        return $this->IDRuta;
    }

    public function setIDRepartidor($IDRepartidor) {
        $this->IDRepartidor = $IDRepartidor;
    }

    public function getIDRepartidor() {
        if (!($this->IDRepartidor instanceof Agentes))
            $this->IDRepartidor = new Agentes($this->IDRepartidor);
        return $this->IDRepartidor;
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

    public function setOrdenDirec($OrdenDirec) {
        $this->OrdenDirec = $OrdenDirec;
    }

    public function getOrdenDirec() {
        return $this->OrdenDirec;
    }

    public function setIDDirec($IDDirec) {
        $this->IDDirec = $IDDirec;
    }

    public function getIDDirec() {
        if (!($this->IDDirec instanceof ClientesDentrega))
            $this->IDDirec = new ClientesDentrega($this->IDDirec);
        return $this->IDDirec;
    }

}

// END class rutas_reparto_detalle
?>