<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 26.12.2011 21:18:36
 */

/**
 * @orm:Entity(pedidos_lineas)
 */
class PedidosLineasEntity extends Entity {

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_lineas")
     * @var entities\PedidosCab
     */
    protected $IDPedido = '0';
    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_lineas")
     */
    protected $IDLinea;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_lineas")
     * @var entities\Articulos
     */
    protected $IDArticulo;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="pedidos_lineas")
     */
    protected $Descripcion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_lineas")
     */
    protected $Unidades = '1.00';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="pedidos_lineas")
     */
    protected $UnidadMedida = 'UMC';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_lineas")
     */
    protected $Precio = '';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_lineas")
     */
    protected $Descuento = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_lineas")
     */
    protected $Importe = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_lineas")
     */
    protected $Iva = '';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_lineas")
     */
    protected $Recargo = '';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_lineas")
     */
    protected $UnidadesRecibidas = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_lineas")
     */
    protected $UnidadesPtesFacturar = '0.00';
    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="pedidos_lineas")
     * @var entities\Abstract\EstadosLineasPedidos
     */
    protected $IDEstado = '';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_lineas")
     */
    protected $IDAgente = '';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="pedidos_lineas")
     * @var entities\Almacenes
     */
    protected $IDAlmacen = '0';
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'pedidos_lineas';
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
        'PedidosCab',
        'Articulos',
        'EstadosLineasPedidos',
        'Almacenes',
        'Agentes',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDPedido($IDPedido) {
        $this->IDPedido = $IDPedido;
    }

    public function getIDPedido() {
        if (!($this->IDPedido instanceof PedidosCab))
            $this->IDPedido = new PedidosCab($this->IDPedido);
        return $this->IDPedido;
    }

    public function setIDLinea($IDLinea) {
        $this->IDLinea = $IDLinea;
    }

    public function getIDLinea() {
        return $this->IDLinea;
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
        $this->UnidadMedida = $UnidadMedida;
    }

    public function getUnidadMedida() {
        return $this->UnidadMedida;
    }

    public function setPrecio($Precio) {
        $this->Precio = $Precio;
    }

    public function getPrecio() {
        return $this->Precio;
    }

    public function setDescuento($Descuento) {
        $this->Descuento = $Descuento;
    }

    public function getDescuento() {
        return $this->Descuento;
    }

    public function setImporte($Importe) {
        $this->Importe = $Importe;
    }

    public function getImporte() {
        return $this->Importe;
    }

    public function setIva($Iva) {
        $this->Iva = $Iva;
    }

    public function getIva() {
        return $this->Iva;
    }

    public function setRecargo($Recargo) {
        $this->Recargo = $Recargo;
    }

    public function getRecargo() {
        return $this->Recargo;
    }

    public function setUnidadesRecibidas($UnidadesRecibidas) {
        $this->UnidadesRecibidas = $UnidadesRecibidas;
    }

    public function getUnidadesRecibidas() {
        return $this->UnidadesRecibidas;
    }

    public function setUnidadesPtesFacturar($UnidadesPtesFacturar) {
        $this->UnidadesPtesFacturar = $UnidadesPtesFacturar;
    }

    public function getUnidadesPtesFacturar() {
        return $this->UnidadesPtesFacturar;
    }

    public function setIDEstado($IDEstado) {
        $this->IDEstado = $IDEstado;
    }

    public function getIDEstado() {
        if (!($this->IDEstado instanceof EstadosLineasPedidos))
            $this->IDEstado = new EstadosLineasPedidos($this->IDEstado);
        return $this->IDEstado;
    }

    public function setIDAgente($IDAgente) {
        $this->IDAgente = $IDAgente;
    }

    public function getIDAgente() {
        return $this->IDAgente;
    }

    public function setIDAlmacen($IDAlmacen) {
        $this->IDAlmacen = $IDAlmacen;
    }

    public function getIDAlmacen() {
        if (!($this->IDAlmacen instanceof Almacenes))
            $this->IDAlmacen = new Almacenes($this->IDAlmacen);
        return $this->IDAlmacen;
    }

}

// END class pedidos_lineas
?>