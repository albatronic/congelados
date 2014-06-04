<?php

/**
 * Favoritos
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 17.01.2012 01:04:42
 */

/**
 * @orm:Entity(favoritos)
 */
class FavoritosEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="favoritos")
     */
    protected $Id;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="favoritos")
     * @var entities\Agentes
     */
    protected $IDAgente;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="favoritos")
     */
    protected $Titulo;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="favoritos")
     */
    protected $Url;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="favoritos")
     */
    protected $Accesos = '0';
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'favoritos';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'Id';
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
        'Agentes',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setId($Id) {
        $this->Id = $Id;
    }

    public function getId() {
        return $this->Id;
    }

    public function setIDAgente($IDAgente) {
        $this->IDAgente = $IDAgente;
    }

    public function getIDAgente() {
        if (!($this->IDAgente instanceof Agentes))
            $this->IDAgente = new Agentes($this->IDAgente);
        return $this->IDAgente;
    }

    public function setTitulo($Titulo) {
        $this->Titulo = trim($Titulo);
    }

    public function getTitulo() {
        return $this->Titulo;
    }

    public function setUrl($Url) {
        $this->Url = trim($Url);
    }

    public function getUrl() {
        return $this->Url;
    }

    public function setAccesos($Accesos) {
        $this->Accesos = $Accesos;
    }

    public function getAccesos() {
        return $this->Accesos;
    }

}

// END class favoritos
?>