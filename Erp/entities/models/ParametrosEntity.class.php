<?php
/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.02.2012 18:52:41
 */

/**
 * @orm:Entity(parametros)
 */
class ParametrosEntity extends Entity {
	/**
	 * @orm:GeneratedValue
	 * @orm:Id
	 * @orm:Column(type="integer")
	 * @assert:NotBlank(groups="parametros")
	 */
	protected $IDParametro;
	/**
	 * @orm:Column(type="string")
	 * @assert:NotBlank(groups="parametros")
	 */
	protected $Codigo;
	/**
	 * @orm:Column(type="string")
	 * @assert:NotBlank(groups="parametros")
	 */
	protected $Descripcion;
	/**
	 * @orm:Column(type="string")
	 */
	protected $Valor;
	/**
	 * Nombre de la conexion a la BD
	 * @var string
	 */
	protected $_conectionName = 'datos#';
	/**
	 * Nombre de la tabla física
	 * @var string
	 */
	protected $_tableName = 'parametros';
	/**
	 * Nombre de la PrimaryKey
	 * @var string
	 */
	protected $_primaryKeyName = 'IDParametro';
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
		);
	/**
	 * GETTERS Y SETTERS
	 */
	public function setIDParametro($IDParametro){
		$this->IDParametro = $IDParametro;
	}
	public function getIDParametro(){
		return $this->IDParametro;
	}

	public function setCodigo($Codigo){
		$this->Codigo = trim($Codigo);
	}
	public function getCodigo(){
		return $this->Codigo;
	}

	public function setDescripcion($Descripcion){
		$this->Descripcion = trim($Descripcion);
	}
	public function getDescripcion(){
		return $this->Descripcion;
	}

	public function setValor($Valor){
		$this->Valor = trim($Valor);
	}
	public function getValor(){
		return $this->Valor;
	}

} // END class parametros

?>