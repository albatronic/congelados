<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(promociones_clientes)
 */
class PromocionesClientesEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="promociones_clientes")
     */
    protected $Id;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="promociones_clientes")
     */
    protected $IDPromocion = '0';
    /**
     * @orm:Column(type="integer")
     */
    protected $IDGrupo;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="promociones_clientes")
     */
    protected $IDCliente;
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'promociones_clientes';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'Id';
    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
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

    public function setIDPromocion($IDPromocion) {
        $this->IDPromocion = $IDPromocion;
    }

    public function getIDPromocion() {
        if (!($this->IDPromocion instanceof Promociones))
            $this->IDPromocion = new Promociones($this->IDPromocion);
        return $this->IDPromocion;
    }

    public function setIDGrupo($IDGrupo) {
        $this->IDGrupo = $IDGrupo;
        if ($this->IDGrupo == '')
            $this->IDGrupo = NULL;
    }

    public function getIDGrupo() {
        if (!($this->IDGrupo instanceof ClientesGrupos))
            $this->IDGrupo = new ClientesGrupos($this->IDGrupo);
        return $this->IDGrupo;
    }

    public function setIDCliente($IDCliente) {
        $this->IDCliente = $IDCliente;
        if ($this->IDCliente == '')
            $this->IDCliente = NULL;
    }

    public function getIDCliente() {
        if (!($this->IDCliente instanceof Clientes))
            $this->IDCliente = new Clientes($this->IDCliente);
        return $this->IDCliente;
    }

}

// END class promociones_clientes
?>