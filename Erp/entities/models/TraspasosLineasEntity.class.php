<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 20.06.2012 09:26:28
 */

/**
 * @orm:Entity(traspasos_lineas)
 */
class TraspasosLineasEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_lineas")
     */
    protected $IDLinea;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_lineas")
     * @var entities\TraspasosCab
     */
    protected $IDTraspaso;
    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="traspasos_lineas")
     */
    protected $Tipo = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_lineas")
     * @var entities\Articulos
     */
    protected $IDArticulo = '0';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="traspasos_lineas")
     */
    protected $Descripcion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_lineas")
     */
    protected $Unidades = '1.00';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="traspasos_lineas")
     */
    protected $UnidadMedida = 'UMA';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_lineas")
     * @var entities\Almacenes
     */
    protected $IDAlmacen = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_lineas")
     */
    protected $Precio = '';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_lineas")
     */
    protected $Importe = '0.00';
    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="traspasos_lineas")
     * @var entities\EstadosManufac
     */
    protected $IDEstado = '0';
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'traspasos_lineas';
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
        'TraspasosCab',
        'Articulos',
        'Almacenes',
        'EstadosManufac',
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

    public function setIDTraspaso($IDTraspaso) {
        $this->IDTraspaso = $IDTraspaso;
    }

    public function getIDTraspaso() {
        if (!($this->IDTraspaso instanceof TraspasosCab))
            $this->IDTraspaso = new TraspasosCab($this->IDTraspaso);
        return $this->IDTraspaso;
    }

    public function setTipo($Tipo) {
        $this->Tipo = $Tipo;
    }

    public function getTipo() {
        return $this->Tipo;
    }

    public function setIDArticulo($IDArticulo) {
        $this->IDArticulo = $IDArticulo;
    }

    public function getIDArticulo() {
        if (!($this->IDArticulo instanceof Articulos))
            $this->IDArticulo = new Articulos($this->IDArticulo);
        return $this->IDArticulo;
    }

    public function setDescripcion($Descripcion) {
        $this->Descripcion = trim($Descripcion);
    }

    public function getDescripcion() {
        return $this->Descripcion;
    }

    public function setUnidades($Unidades) {
        $this->Unidades = $Unidades;
    }

    public function getUnidades() {
        return $this->Unidades;
    }

    public function setUnidadMedida($UnidadMedida) {
        $this->UnidadMedida = trim($UnidadMedida);
    }

    public function getUnidadMedida() {
        return $this->UnidadMedida;
    }

    public function setIDAlmacen($IDAlmacen) {
        $this->IDAlmacen = $IDAlmacen;
    }

    public function getIDAlmacen() {
        if (!($this->IDAlmacen instanceof Almacenes))
            $this->IDAlmacen = new Almacenes($this->IDAlmacen);
        return $this->IDAlmacen;
    }

    public function setPrecio($Precio) {
        $this->Precio = $Precio;
    }

    public function getPrecio() {
        return $this->Precio;
    }

    public function setImporte($Importe) {
        $this->Importe = $Importe;
    }

    public function getImporte() {
        return $this->Importe;
    }

    public function setIDEstado($IDEstado) {
        $this->IDEstado = $IDEstado;
    }

    public function getIDEstado() {
        if (!($this->IDEstado instanceof EstadosManufac))
            $this->IDEstado = new EstadosManufac($this->IDEstado);
        return $this->IDEstado;
    }

}

// END class traspasos_lineas
?>