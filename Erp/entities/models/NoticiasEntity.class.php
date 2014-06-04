<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(noticias)
 */
class NoticiasEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="noticias")
     */
    protected $IDNoticia;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="noticias")
     */
    protected $IDSucursal = '000';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="noticias")
     */
    protected $Noticia;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="noticias")
     */
    protected $Vigencia = '0000-00-00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="noticias")
     */
    protected $Emergente = '0';
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'noticias';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDNoticia';

    /**
     * GETTERS Y SETTERS
     */
    public function setIDNoticia($IDNoticia) {
        $this->IDNoticia = $IDNoticia;
    }

    public function getIDNoticia() {
        return $this->IDNoticia;
    }

    public function setIDSucursal($IDSucursal) {
        $this->IDSucursal = $IDSucursal;
    }

    public function getIDSucursal() {
        if (!($this->IDSucursal instanceof Sucursales))
            $this->IDSucursal = new Sucursales($this->IDSucursal);
        return $this->IDSucursal;
    }

    public function setNoticia($Noticia) {
        $this->Noticia = trim($Noticia);
    }

    public function getNoticia() {
        return $this->Noticia;
    }

    public function setVigencia($Vigencia) {
        $date = new Fecha($Vigencia);
        $this->Vigencia = $date->getFecha();
        unset($date);
    }

    public function getVigencia() {
        $date = new Fecha($this->Vigencia);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setEmergente($Emergente) {
        $this->Emergente = $Emergente;
    }

    public function getEmergente() {
        if (!($this->Emergente instanceof ValoresSN))
            $this->Emergente = new ValoresSN($this->Emergente);
        return $this->Emergente;
    }

}

// END class noticias
?>