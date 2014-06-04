<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(lotes)
 */
class LotesEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="lotes")
     */
    protected $IDLote;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="lotes")
     */
    protected $IDArticulo = '';

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="lotes")
     */
    protected $Lote = '';

    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="lotes")
     */
    protected $FechaFabricacion = '0000-00-00';

    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="lotes")
     */
    protected $FechaCaducidad = '0000-00-00';

    /**
     * @orm:Column(type="")
     * @assert:NotBlank(groups="lotes")
     */
    protected $Vigente = '1';

    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';

    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'lotes';

    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDLote';

    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDLote', 'ParentEntity' => 'Existencias', 'ParentColumn' => 'IDLote'),
        array('SourceColumn' => 'IDLote', 'ParentEntity' => 'InventariosLineas', 'ParentColumn' => 'IDLote'),
        array('SourceColumn' => 'IDLote', 'ParentEntity' => 'TraspasosLineas', 'ParentColumn' => 'IDLote'),
        array('SourceColumn' => 'IDLote', 'ParentEntity' => 'MvtosAlmacen', 'ParentColumn' => 'IDLote'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDLote($IDLote) {
        $this->IDLote = $IDLote;
    }

    public function getIDLote() {
        return $this->IDLote;
    }

    public function setIDArticulo($IDArticulo) {
        $this->IDArticulo = $IDArticulo;
    }

    public function getIDArticulo() {
        if (!($this->IDArticulo instanceof Articulos))
            $this->IDArticulo = new Articulos($this->IDArticulo);
        return $this->IDArticulo;
    }

    public function setLote($Lote) {
        $this->Lote = $Lote;
    }

    public function getLote() {
        return $this->Lote;
    }

    public function setFechaFabricacion($FechaFabricacion) {
        $date = new Fecha($FechaFabricacion);
        $this->FechaFabricacion = $date->getFecha();
        unset($date);
    }

    public function getFechaFabricacion() {
        $date = new Fecha($this->FechaFabricacion);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setFechaCaducidad($FechaCaducidad) {
        $date = new Fecha($FechaCaducidad);
        $this->FechaCaducidad = $date->getFecha();
        unset($date);
    }

    public function getFechaCaducidad() {
        $date = new Fecha($this->FechaCaducidad);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setVigente($Vigente) {
        $this->Vigente = $Vigente;
    }

    public function getVigente() {
        if (!($this->Vigente instanceof ValoresSN))
            $this->Vigente = new ValoresSN($this->Vigente);
        return $this->Vigente;
    }

}

// END class lotes
?>