<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(promociones)
 */
class PromocionesEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="promociones")
     */
    protected $IDPromocion;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="promociones")
     */
    protected $Titulo;
    /**
     * @orm:Column(type="integer")
     */
    protected $IDFamilia;
    /**
     * @orm:Column(type="integer")
     */
    protected $IDArticulo;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="promociones")
     */
    protected $FinPromocion = '0000-00-00';
    /**
     * @orm:Column(type="integer")
     */
    protected $CantidadMinima = '1.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="promociones")
     */
    protected $TipoPromocion = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="promociones")
     */
    protected $Valor = '0.000';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="promociones")
     */
    protected $IDFP = '0';
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'promociones';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDPromocion';
    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDPromocion', 'ParentEntity' => 'PromocionesClientes', 'ParentColumn' => 'IDPromocion'),
        array('SourceColumn' => 'IDPromocion', 'ParentEntity' => 'AlbaranesLineas', 'ParentColumn' => 'IDPromocion'),
        array('SourceColumn' => 'IDPromocion', 'ParentEntity' => 'PstoLineas', 'ParentColumn' => 'IDPromocion'),
        array('SourceColumn' => 'IDPromocion', 'ParentEntity' => 'FemitidasLineas', 'ParentColumn' => 'IDPromocion'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDPromocion($IDPromocion) {
        $this->IDPromocion = $IDPromocion;
    }

    public function getIDPromocion() {
        return $this->IDPromocion;
    }

    public function setTitulo($Titulo) {
        $this->Titulo = trim($Titulo);
    }

    public function getTitulo() {
        return $this->Titulo;
    }

    public function setIDFamilia($IDFamilia) {
        $this->IDFamilia = trim($IDFamilia);
        if ($this->IDFamilia == '')
            $this->IDFamilia = NULL;
    }

    public function getIDFamilia() {
        if (!($this->IDFamilia instanceof Familias))
            $this->IDFamilia = new Familias($this->IDFamilia);
        return $this->IDFamilia;
    }

    public function setIDArticulo($IDArticulo) {
        $this->IDArticulo = trim($IDArticulo);
        if ($this->IDArticulo == '')
            $this->IDArticulo = NULL;
    }

    public function getIDArticulo() {
        if (!($this->IDArticulo instanceof Articulos))
            $this->IDArticulo = new Articulos($this->IDArticulo);
        return $this->IDArticulo;
    }

    public function setFinPromocion($FinPromocion) {
        $date = new Fecha($FinPromocion);
        $this->FinPromocion = $date->getFecha();
        unset($date);
    }

    public function getFinPromocion() {
        $date = new Fecha($this->FinPromocion);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setCantidadMinima($CantidadMinima) {
        $this->CantidadMinima = $CantidadMinima;
    }

    public function getCantidadMinima() {
        return $this->CantidadMinima;
    }

    public function setTipoPromocion($TipoPromocion) {
        $this->TipoPromocion = $TipoPromocion;
    }

    public function getTipoPromocion() {
        if (!($this->TipoPromocion instanceof PromocionesTipos))
            $this->TipoPromocion = new PromocionesTipos($this->TipoPromocion);
        return $this->TipoPromocion;
    }

    public function setValor($Valor) {
        $this->Valor = $Valor;
    }

    public function getValor() {
        return $this->Valor;
    }

    public function setIDFP($IDFP) {
        $this->IDFP = $IDFP;
    }

    public function getIDFP() {
        if (!$this->IDFP instanceof FormasPago)
            $this->IDFP = new FormasPago($this->IDFP);
        return $this->IDFP;
    }

}

// END class promociones
?>