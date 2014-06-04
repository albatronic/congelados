<?php

/**
 * Agencias de Transporte
 *
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:46
 */

/**
 * @orm:Entity(agencias)
 */
class AgenciasEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="agencias")
     */
    protected $IDAgencia;

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="agencias")
     */
    protected $Agencia;

    /**
     * @orm:Column(type="string")
     */
    protected $Telefono;

    /**
     * @orm:Column(type="string")
     */
    protected $Fax;

    /**
     * @orm:Column(type="string")
     */
    protected $Web;

    /**
     * @orm:Column(type="string")
     */
    protected $EMail;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="agencias")
     */
    protected $CosteEstandar = '0.00';

    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';

    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'agencias';

    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDAgencia';
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDAgencia', 'ParentEntity' => 'TablaPortes', 'ParentColumn' => 'IDAgencia'),
        array('SourceColumn' => 'IDAgencia', 'ParentEntity' => 'AlbaranesCab', 'ParentColumn' => 'IDAgencia'),
        array('SourceColumn' => 'IDAgencia', 'ParentEntity' => 'FemitidasCab', 'ParentColumn' => 'IDAgencia'),
        array('SourceColumn' => 'IDAgencia', 'ParentEntity' => 'PedidosCab', 'ParentColumn' => 'IDAgencia'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDAgencia($IDAgencia) {
        $this->IDAgencia = $IDAgencia;
    }

    public function getIDAgencia() {
        return $this->IDAgencia;
    }

    public function setAgencia($Agencia) {
        $this->Agencia = trim($Agencia);
    }

    public function getAgencia() {
        return $this->Agencia;
    }

    public function setTelefono($Telefono) {
        $this->Telefono = trim($Telefono);
    }

    public function getTelefono() {
        return $this->Telefono;
    }

    public function setFax($Fax) {
        $this->Fax = trim($Fax);
    }

    public function getFax() {
        return $this->Fax;
    }

    public function setWeb($Web) {
        $this->Web = trim($Web);
    }

    public function getWeb() {
        return $this->Web;
    }

    public function setEMail($EMail) {
        $this->EMail = trim($EMail);
    }

    public function getEMail() {
        return $this->EMail;
    }

    public function setCosteEstandar($CosteEstandar) {
        $this->CosteEstandar = $CosteEstandar;
    }

    public function getCosteEstandar() {
        return $this->CosteEstandar;
    }

}

// END class agencias
?>