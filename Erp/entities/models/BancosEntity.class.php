<?php

/**
 * Entidades Bancarias
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 19:20:36
 */

/**
 * @orm:Entity(bancos)
 */
class BancosEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="bancos")
     */
    protected $IDBanco;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="bancos")
     */
    protected $CodigoBanco;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="bancos")
     */
    protected $Banco = '';
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'empresas';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'bancos';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDBanco';

    /**
     * GETTERS Y SETTERS
     */
    public function setIDBanco($IDBanco) {
        $this->IDBanco = $IDBanco;
    }
    public function getIDBanco() {
        return $this->IDBanco;
    }

    public function setCodigoBanco($CodigoBanco) {
        $this->CodigoBanco = $CodigoBanco;
    }
    public function getCodigoBanco() {
        return $this->CodigoBanco;
    }

    public function setBanco($Banco) {
        $this->Banco = $Banco;
    }
    public function getBanco() {
        return $this->Banco;
    }

}

// END class bancos
?>