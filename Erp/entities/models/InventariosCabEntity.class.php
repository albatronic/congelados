<?php

/**
 * Cabeceras de Inventarios
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 31.05.2012 23:22:38
 */

/**
 * @orm:Entity(inventarios_cab)
 */
class InventariosCabEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="inventarios_cab")
     */
    protected $IDInventario;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="inventarios_cab")
     * @var entities\Almacenes
     */
    protected $IDAlmacen;

    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="inventarios_cab")
     */
    protected $Fecha;

    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="inventarios_cab")
     * @var entities\ValoresSN
     */
    protected $Cerrado = '0';

    /**
     * @orm:Column(type="string")
     */
    protected $Comentarios;

    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';

    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'inventarios_cab';

    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDInventario';

    /**
     * Relacion de entidades que dependen de esta
     * @var string
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDVentario', 'ParentEntity' => 'InventariosIneas', 'ParentColumn' => 'IDInventario'),
    );

    /**
     * Relacion de entidades de las que esta depende
     * @var string
     */
    protected $_childEntities = array(
        'Almacenes',
        'ValoresSN',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDInventario($IDInventario) {
        $this->IDInventario = $IDInventario;
    }

    public function getIDInventario() {
        return $this->IDInventario;
    }

    public function setIDAlmacen($IDAlmacen) {
        $this->IDAlmacen = $IDAlmacen;
    }

    public function getIDAlmacen() {
        if (!($this->IDAlmacen instanceof Almacenes))
            $this->IDAlmacen = new Almacenes($this->IDAlmacen);
        return $this->IDAlmacen;
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

    public function setCerrado($Cerrado) {
        $this->Cerrado = $Cerrado;
    }

    public function getCerrado() {
        if (!($this->Cerrado instanceof ValoresSN))
            $this->Cerrado = new ValoresSN($this->Cerrado);
        return $this->Cerrado;
    }

    public function setComentarios($Comentarios) {
        $this->Comentarios = trim($Comentarios);
    }

    public function getComentarios() {
        return $this->Comentarios;
    }

}

// END class inventarios_cab
?>