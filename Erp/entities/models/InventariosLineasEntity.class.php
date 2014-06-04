<?php

/**
 * Lineas de Inventarios
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 01.06.2012 00:59:34
 */

/**
 * @orm:Entity(inventarios_lineas)
 */
class InventariosLineasEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="inventarios_lineas")
     */
    protected $IDLinea;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="inventarios_lineas")
     * @var entities\InventariosCab
     */
    protected $IDInventario;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="inventarios_lineas")
     * @var entities\Articulos
     */
    protected $IDArticulo;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="inventarios_lineas")
     */
    protected $Descripcion;
    /**
     * @orm:Column(type="integer")
     * @var entities\Lotes
     */
    protected $IDLote;
    /**
     * @orm:Column(type="integer")
     * @var entities\AlmacenesMapas
     */
    protected $IDUbicacion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="inventarios_lineas")
     */
    protected $Stock = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="inventarios_lineas")
     */
    protected $Cajas = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="inventarios_lineas")
     */
    protected $Pales = '0.00';
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'inventarios_lineas';
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
        'InventariosCab',
        'Articulos',
        'Lotes',
        'AlmacenesMapas',
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

    public function setIDInventario($IDInventario) {
        $this->IDInventario = $IDInventario;
    }

    public function getIDInventario() {
        if (!($this->IDInventario instanceof InventariosCab))
            $this->IDInventario = new InventariosCab($this->IDInventario);
        return $this->IDInventario;
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

    public function setStock($Stock) {
        $this->Stock = $Stock;
    }

    public function getStock() {
        return $this->Stock;
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

}

// END class inventarios_lineas
?>