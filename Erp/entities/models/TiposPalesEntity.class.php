<?php
/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 06.12.2011 16:59:34
 */

/**
 * @orm:Entity(tipos_pales)
 */
class TiposPalesEntity extends Entity {
	/**
	 * @orm:GeneratedValue
	 * @orm:Id
	 * @orm:Column(type="integer")
	 * @assert:NotBlank(groups="tipos_pales")
	 */
	protected $Id;
	/**
	 * @orm:Column(type="string")
	 * @assert:NotBlank(groups="tipos_pales")
	 */
	protected $Descripcion;
	/**
	 * @orm:Column(type="integer")
	 * @assert:NotBlank(groups="tipos_pales")
	 */
	protected $Ancho = '0.00';
	/**
	 * @orm:Column(type="integer")
	 * @assert:NotBlank(groups="tipos_pales")
	 */
	protected $Fondo = '0.00';
	/**
	 * @orm:Column(type="integer")
	 * @assert:NotBlank(groups="tipos_pales")
	 */
	protected $Alto = '0.00';
	/**
	 * @orm:Column(type="integer")
	 * @assert:NotBlank(groups="tipos_pales")
	 */
	protected $Cubicaje = '0.00';
	/**
	 * @orm:Column(type="integer")
	 * @assert:NotBlank(groups="tipos_pales")
	 */
	protected $Tara = '0.00';
	/**
	 * Nombre de la conexion a la BD
	 * @var string
	 */
	protected $_conectionName = 'datos#';
	/**
	 * Nombre de la tabla física
	 * @var string
	 */
	protected $_tableName = 'tipos_pales';
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
		);
	/**
	 * GETTERS Y SETTERS
	 */
	public function setId($Id){
		$this->Id = $Id;
	}
	public function getId(){
		return $this->Id;
	}

	public function setDescripcion($Descripcion){
		$this->Descripcion = trim($Descripcion);
	}
	public function getDescripcion(){
		return $this->Descripcion;
	}

	public function setAncho($Ancho){
		$this->Ancho = $Ancho;
	}
	public function getAncho(){
		return $this->Ancho;
	}

	public function setFondo($Fondo){
		$this->Fondo = $Fondo;
	}
	public function getFondo(){
		return $this->Fondo;
	}

	public function setAlto($Alto){
		$this->Alto = $Alto;
	}
	public function getAlto(){
		return $this->Alto;
	}

	public function setCubicaje($Cubicaje){
		$this->Cubicaje = $Cubicaje;
	}
	public function getCubicaje(){
		return $this->Cubicaje;
	}

	public function setTara($Tara){
		$this->Tara = $Tara;
	}
	public function getTara(){
		return $this->Tara;
	}

} // END class tipos_pales

?>