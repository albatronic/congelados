<?php

/**
 * Familias
 *
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(familias)
 */
class FamiliasEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="familias")
     */
    protected $IDFamilia;

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="familias")
     */
    protected $Familia = '';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="familias")
     */
    protected $Inventario = '1';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="familias")
     */
    protected $Trazabilidad = '1';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="familias")
     */
    protected $MargenMinimo = '0';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="familias")
     */
    protected $PublicarWeb = '0';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="familias")
     */
    protected $MargenWeb = '0';

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="familias")
     */
    protected $Caducidad = '0';

    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';

    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'familias';

    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDFamilia';

    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDFamilia', 'ParentEntity' => 'Subfamilias', 'ParentColumn' => 'IDFamilia'),
        array('SourceColumn' => 'IDFamilia', 'ParentEntity' => 'Articulos', 'ParentColumn' => 'IDFamilia'),
        array('SourceColumn' => 'IDFamilia', 'ParentEntity' => 'Promociones', 'ParentColumn' => 'IDFamilia'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDFamilia($IDFamilia) {
        $this->IDFamilia = $IDFamilia;
    }

    public function getIDFamilia() {
        return $this->IDFamilia;
    }

    public function setFamilia($Familia) {
        $this->Familia = trim($Familia);
    }

    public function getFamilia() {
        return $this->Familia;
    }

    public function setInventario($Inventario) {
        $this->Inventario = $Inventario;
    }

    public function getInventario() {
        if (!($this->Inventario instanceof ValoresSN))
            $this->Inventario = new ValoresSN($this->Inventario);
        return $this->Inventario;
    }

    public function setTrazabilidad($Trazabilidad) {
        $this->Trazabilidad = $Trazabilidad;
    }

    public function getTrazabilidad() {
        if (!($this->Trazabilidad instanceof ValoresSN))
            $this->Trazabilidad = new ValoresSN($this->Trazabilidad);
        return $this->Trazabilidad;
    }

    public function setMargenMinimo($MargenMinimo) {
        $this->MargenMinimo = $MargenMinimo;
    }

    public function getMargenMinimo() {
        return $this->MargenMinimo;
    }

    public function setPublicarWeb($PublicarWeb) {
        $this->PublicarWeb = $PublicarWeb;
    }

    public function getPublicarWeb() {
        if (!($this->PublicarWeb instanceof ValoresSN))
            $this->PublicarWeb = new ValoresSN($this->PublicarWeb);
        return $this->PublicarWeb;
    }

    public function setMargenWeb($MargenWeb) {
        $this->MargenWeb = $MargenWeb;
    }

    public function getMargenWeb() {
        return $this->MargenWeb;
    }

    public function setCaducidad($Caducidad) {
        $this->Caducidad = $Caducidad;
    }

    public function getCaducidad() {
        return $this->Caducidad;
    }

}

// END class familias
?>