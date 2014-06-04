<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 16.08.2012 19:38:58
 */

/**
 * @orm:Entity(documentos)
 */
class DocumentosEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="documentos")
     */
    protected $Id;

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="documentos")
     */
    protected $Entidad;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="documentos")
     */
    protected $IdEntidad;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="documentos")
     */
    protected $Orden = '0';

    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="documentos")
     * @var entities\ValoresSN
     */
    protected $EsGaleria = '0';

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="documentos")
     */
    protected $PathName;

    /**
     * @orm:Column(type="string")
     */
    protected $TextoPie;

    /**
     * @orm:Column(type="string")
     */
    protected $Title;

    /**
     * @orm:Column(type="string")
     */
    protected $Alt;

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="documentos")
     */
    protected $Clave;

    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';

    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'documentos';

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
        'ValoresSN',
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

    public function setEntidad($Entidad) {
        $this->Entidad = trim($Entidad);
    }

    public function getEntidad() {
        return $this->Entidad;
    }

    public function setIdEntidad($IdEntidad) {
        $this->IdEntidad = $IdEntidad;
    }

    public function getIdEntidad() {
        return $this->IdEntidad;
    }

    public function setOrden($Orden) {
        $this->Orden = $Orden;
    }

    public function getOrden() {
        return $this->Orden;
    }

    public function setEsGaleria($EsGaleria) {
        $this->EsGaleria = $EsGaleria;
    }

    public function getEsGaleria() {
        if (!($this->EsGaleria instanceof ValoresSN))
            $this->EsGaleria = new ValoresSN($this->EsGaleria);
        return $this->EsGaleria;
    }

    public function setPathName($PathName) {
        $this->PathName = trim($PathName);
    }

    public function getPathName() {
        return $this->PathName;
    }

    public function setTextoPie($TextoPie) {
        $this->TextoPie = trim($TextoPie);
    }

    public function getTextoPie() {
        return $this->TextoPie;
    }

    public function setTitle($Title) {
        $this->Title = trim($Title);
    }

    public function getTitle() {
        return $this->Title;
    }

    public function setAlt($Alt) {
        $this->Alt = trim($Alt);
    }

    public function getAlt() {
        return $this->Alt;
    }

    public function setClave($Clave) {
        $this->Clave = trim($Clave);
    }

    public function getClave() {
        return $this->Clave;
    }

}

// END class documentos
?>