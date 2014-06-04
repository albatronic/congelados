<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 25.02.2012 20:14:14
 */

/**
 * @orm:Entity(recepciones)
 */
class RecepcionesEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     */
    protected $IDLinea;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="expediciones")
     */
    protected $Entidad = '';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     * @var entities\PedidosCab
     */
    protected $IDEntidad = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     * @var entities\PedidosLineas
     */
    protected $IDLineaEntidad = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     * @var entities\Almacenes
     */
    protected $IDAlmacen = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     * @var entities\Agentes
     */
    protected $IDAlmacenero = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     * @var entities\Articulos
     */
    protected $IDArticulo = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     */
    protected $Unidades = '0.00';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="recepciones")
     */
    protected $UnidadMedida = '';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     */
    protected $UnidadesBrutas = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     */
    protected $Cajas = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     */
    protected $Pales = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     */
    protected $DestareCaja = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     */
    protected $DestarePale = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     */
    protected $UnidadesNetas = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     * @var entities\Lotes
     */
    protected $IDLote = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     * @var entities\AlmacenesMapas
     */
    protected $IDUbicacion = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     */
    protected $Temperatura = '-20.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="recepciones")
     */
    protected $Recepcionada = '0';
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'recepciones';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDLinea';
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
        'Almacenes',
        'Agentes',
        'Lotes',
        'AlmacenesMapas',
        'Articulos',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDLinea($IDLinea) {
        $this->IDLinea = $IDLinea;
    }

    public function getIDLinea() {
        return $this->IDLinea;
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
        if (!($this->IDEntidad instanceof $this->Entidad))
            $this->IDEntidad = new $this->Entidad($this->IDEntidad);
        return $this->IDEntidad;
    }

    public function setIDLineaEntidad($IDLineaEntidad) {
        $this->IDLineaEntidad = $IDLineaEntidad;
    }

    public function getIDLineaEntidad() {

        switch ($this->Entidad) {
            case 'PedidosCab':
                if (!($this->IDLineaEntidad instanceof PedidosLineas))
                    $this->IDLineaEntidad = new PedidosLineas ($this->IDLineaEntidad);
                break;
            case 'TraspasosCab':
                if (!($this->IDLineaEntidad instanceof TraspasosLineas))
                    $this->IDLineaEntidad = new TraspasosLineas($this->IDLineaEntidad);
                break;
            case 'ManufacCab':
                if (!($this->IDLineaEntidad instanceof ManufacLineas))
                    $this->IDLineaEntidad = new ManufacLineas($this->IDLineaEntidad);
                break;
        }
        return $this->IDLineaEntidad;
    }

    public function setIDAlmacen($IDAlmacen) {
        $this->IDAlmacen = $IDAlmacen;
    }

    public function getIDAlmacen() {
        if (!($this->IDAlmacen instanceof Almacenes))
            $this->IDAlmacen = new Almacenes($this->IDAlmacen);
        return $this->IDAlmacen;
    }

    public function setIDAlmacenero($IDAlmacenero) {
        $this->IDAlmacenero = $IDAlmacenero;
    }

    public function getIDAlmacenero() {
        if (!($this->IDAlmacenero instanceof Agentes))
            $this->IDAlmacenero = new Agentes($this->IDAlmacenero);
        return $this->IDAlmacenero;
    }

    public function setIDArticulo($IDArticulo) {
        $this->IDArticulo = $IDArticulo;
    }

    public function getIDArticulo() {
        if (!($this->IDArticulo instanceof Articulos))
            $this->IDArticulo = new Articulos($this->IDArticulo);
        return $this->IDArticulo;
    }

    public function setUnidades($Unidades) {
        $this->Unidades = $Unidades;
    }

    public function getUnidades() {
        return $this->Unidades;
    }

    public function setUnidadMedida($UnidadMedida) {
        $this->UnidadMedida = $UnidadMedida;
    }

    public function getUnidadMedida() {
        return $this->UnidadMedida;
    }

    public function setUnidadesBrutas($UnidadesBrutas) {
        $this->UnidadesBrutas = $UnidadesBrutas;
    }

    public function getUnidadesBrutas() {
        return $this->UnidadesBrutas;
    }

    public function setCajas($Cajas) {
        $this->Cajas = $Cajas;
    }

    public function getCajas() {
        return $this->Cajas;
    }

    public function setPales($Pales) {
        $this->Pales = $Pales;
    }

    public function getPales() {
        return $this->Pales;
    }

    public function setDestareCaja($DestareCaja) {
        $this->DestareCaja = $DestareCaja;
    }

    public function getDestareCaja() {
        return $this->DestareCaja;
    }

    public function setDestarePale($DestarePale) {
        $this->DestarePale = $DestarePale;
    }

    public function getDestarePale() {
        return $this->DestarePale;
    }

    public function setUnidadesNetas($UnidadesNetas) {
        $this->UnidadesNetas = $UnidadesNetas;
    }

    public function getUnidadesNetas() {
        return $this->UnidadesNetas;
    }

    public function setIDLote($IDLote) {
        $this->IDLote = $IDLote;
    }

    public function getIDLote() {
        if (!($this->IDLote instanceof Lotes))
            $this->IDLote = new Lotes($this->IDLote);
        return $this->IDLote;
    }

    public function setIDUbicacion($IDUbicacion) {
        $this->IDUbicacion = $IDUbicacion;
    }

    public function getIDUbicacion() {
        if (!($this->IDUbicacion instanceof AlmacenesMapas))
            $this->IDUbicacion = new AlmacenesMapas($this->IDUbicacion);
        return $this->IDUbicacion;
    }

    public function setTemperatura($Temperatura) {
        $this->Temperatura = $Temperatura;
    }

    public function getTemperatura() {
        return $this->Temperatura;
    }

    public function setRecepcionada($Recepcionada) {
        $this->Recepcionada = $Recepcionada;
    }

    public function getRecepcionada() {
        return $this->Recepcionada;
    }

}

// END class recepciones
?>