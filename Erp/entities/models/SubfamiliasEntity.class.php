<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:48
 */

/**
 * @orm:Entity(subfamilias)
 */
class SubfamiliasEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="subfamilias")
     */
    protected $IDSubfamilia;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="subfamilias")
     */
    protected $IDFamilia;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="subfamilias")
     */
    protected $Subfamilia = '';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="subfamilias")
     */
    protected $PublicarWeb = '0';
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'subfamilias';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDSubfamilia';
    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDSubfamilia', 'ParentEntity' => 'Articulos', 'ParentColumn' => 'IDSubfamilia'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDFamilia($IDFamilia) {
        $this->IDFamilia = $IDFamilia;
    }

    public function getIDFamilia() {
        if (!$this->IDFamilia instanceof Familias)
            $this->IDFamilia = new Familias($this->IDFamilia);
        return $this->IDFamilia;
    }

    public function setIDSubfamilia($IDSubfamilia) {
        $this->IDSubfamilia = $IDSubfamilia;
    }

    public function getIDSubfamilia() {
        return $this->IDSubfamilia;
    }

    public function setSubfamilia($Subfamilia) {
        $this->Subfamilia = $Subfamilia;
    }

    public function getSubfamilia() {
        return $this->Subfamilia;
    }

    public function setPublicarWeb($PublicarWeb) {
        $this->PublicarWeb = $PublicarWeb;
    }

    public function getPublicarWeb() {
        if (!($this->PublicarWeb instanceof ValoresSN))
            $this->PublicarWeb = new ValoresSN($this->PublicarWeb);
        return $this->PublicarWeb;
    }

}

// END class subfamilias
?>