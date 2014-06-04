<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:48
 */

/**
 * @orm:Entity(tarifas)
 */
class TarifasEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="tarifas")
     */
    protected $IDTarifa;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="tarifas")
     */
    protected $Descripcion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="tarifas")
     */
    protected $Tipo = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="tarifas")
     */
    protected $Valor = '0.000';
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'tarifas';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDTarifa';
    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDTarifa', 'ParentEntity' => 'Clientes', 'ParentColumn' => 'IDTarifa'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDTarifa($IDTarifa) {
        $this->IDTarifa = $IDTarifa;
    }

    public function getIDTarifa() {
        return $this->IDTarifa;
    }

    public function setDescripcion($Descripcion) {
        $this->Descripcion = trim($Descripcion);
    }

    public function getDescripcion() {
        return $this->Descripcion;
    }

    public function setTipo($Tipo) {
        $this->Tipo = $Tipo;
    }

    public function getTipo() {
        if (!($this->Tipo instanceof TarifasTipos))
            $this->Tipo = new TarifasTipos($this->Tipo);
        return $this->Tipo;
    }

    public function setValor($Valor) {
        $this->Valor = $Valor;
    }

    public function getValor() {
        return $this->Valor;
    }

}

// END class tarifas
?>