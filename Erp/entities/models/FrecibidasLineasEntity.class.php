<?php

/**
 * Lineas de Facturas Recibidas
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(frecibidas_lineas)
 */
class FrecibidasLineasEntity extends Entity {

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="frecibidas_lineas")
     */
    protected $IDFactura = '0';
    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="frecibidas_lineas")
     */
    protected $IDLinea;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="frecibidas_lineas")
     */
    protected $IDArticulo;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="frecibidas_lineas")
     */
    protected $Descripcion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="frecibidas_lineas")
     */
    protected $Unidades = '1.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="frecibidas_lineas")
     */
    protected $Precio = '0.000';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="frecibidas_lineas")
     */
    protected $Descuento = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="frecibidas_lineas")
     */
    protected $Importe = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="frecibidas_lineas")
     */
    protected $Iva = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="frecibidas_lineas")
     */
    protected $IDPedido;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="frecibidas_lineas")
     */
    protected $IDLineaPedido;
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'frecibidas_lineas';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDLinea';

    /**
     * GETTERS Y SETTERS
     */
    public function setIDFactura($IDFactura) {
        $this->IDFactura = $IDFactura;
    }

    public function getIDFactura() {
        if (!($this->IDFactura instanceof FrecibidasCab))
            $this->IDFactura = new FrecibidasCab($this->IDFactura);
        return $this->IDFactura;
    }

    public function setIDLinea($IDLinea) {
        $this->IDLinea = $IDLinea;
    }

    public function getIDLinea() {
        return $this->IDLinea;
    }

    public function setIDArticulo($IDArticulo) {
        $this->IDArticulo = trim($IDArticulo);
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

    public function setIDPedido($IDPedido) {
        $this->IDPedido = $IDPedido;
    }

    public function getIDPedido() {
        if (!($this->IDPedido instanceof PedidosCab))
            $this->IDPedido = new PedidosCab($this->IDPedido);
        return $this->IDPedido;
    }

    public function setIDLineaPedido($IDLineaPedido) {
        $this->IDLineaPedido = $IDLineaPedido;
    }

    public function getIDLineaPedido() {
        if (!($this->IDLineaPedido instanceof PedidosLineas))
            $this->IDLineaPedido = new PedidosLineas($this->IDLineaPedido);
        return $this->IDLineaPedido;
    }

}

// END class frecibidas_lineas
?>