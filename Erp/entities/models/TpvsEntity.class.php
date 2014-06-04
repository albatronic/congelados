<?php
/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 15.04.2012 01:20:43
 */

/**
 * @orm:Entity(tpvs)
 */
class TpvsEntity extends Entity {
	/**
	 * @orm:GeneratedValue
	 * @orm:Id
	 * @orm:Column(type="integer")
	 * @assert:NotBlank(groups="tpvs")
	 */
	protected $IDTpv;
	/**
	 * @orm:Column(type="integer")
	 * @assert:NotBlank(groups="tpvs")
	 * @var entities\Sucursales
	 */
	protected $IDSucursal;
	/**
	 * @orm:Column(type="string")
	 * @assert:NotBlank(groups="tpvs")
	 */
	protected $Nombre;
	/**
	 * @orm:Column(type="string")
	 */
	protected $AperturaCajon;
	/**
	 * @orm:Column(type="tinyint")
	 * @assert:NotBlank(groups="tpvs")
	 * @var entities\ValoresSN
	 */
	protected $SaltoPagina = '1';
	/**
	 * @orm:Column(type="string")
	 */
	protected $Estilo;
	/**
	 * @orm:Column(type="integer")
	 * @assert:NotBlank(groups="tpvs")
	 * @var entities\Datafonos
	 */
	protected $IDDataFono1 = '0';
	/**
	 * @orm:Column(type="integer")
	 * @assert:NotBlank(groups="tpvs")
	 * @var entities\Datafonos
	 */
	protected $IDDataFono2 = '0';
	/**
	 * @orm:Column(type="integer")
	 * @assert:NotBlank(groups="tpvs")
	 * @var entities\Datafonos
	 */
	protected $IDDataFono3 = '0';
	/**
	 * @orm:Column(type="integer")
	 * @assert:NotBlank(groups="tpvs")
	 * @var entities\Datafonos
	 */
	protected $IDDataFono4 = '0';
	/**
	 * Nombre de la conexion a la BD
	 * @var string
	 */
	protected $_conectionName = 'datos#';
	/**
	 * Nombre de la tabla física
	 * @var string
	 */
	protected $_tableName = 'tpvs';
	/**
	 * Nombre de la PrimaryKey
	 * @var string
	 */
	protected $_primaryKeyName = 'IDTpv';
	/**
	 * Relacion de entidades que dependen de esta
	 * @var string
	 */
	protected $_parentEntities = array(
			array('SourceColumn' => 'IDTpv', 'ParentEntity' => 'CajaArqueos', 'ParentColumn' => 'IDTpv'),
		);
	/**
	 * Relacion de entidades de las que esta depende
	 * @var string
	 */
	protected $_childEntities = array(
			'Sucursales',
			'ValoresSN',
			'Datafonos',
		);
	/**
	 * GETTERS Y SETTERS
	 */
	public function setIDTpv($IDTpv){
		$this->IDTpv = $IDTpv;
	}
	public function getIDTpv(){
		return $this->IDTpv;
	}

	public function setIDSucursal($IDSucursal){
		$this->IDSucursal = $IDSucursal;
	}
	public function getIDSucursal(){
		if (!($this->IDSucursal instanceof Sucursales))
			$this->IDSucursal = new Sucursales($this->IDSucursal);
		return $this->IDSucursal;
	}

	public function setNombre($Nombre){
		$this->Nombre = trim($Nombre);
	}
	public function getNombre(){
		return $this->Nombre;
	}

	public function setAperturaCajon($AperturaCajon){
		$this->AperturaCajon = trim($AperturaCajon);
	}
	public function getAperturaCajon(){
		return $this->AperturaCajon;
	}

	public function setSaltoPagina($SaltoPagina){
		$this->SaltoPagina = $SaltoPagina;
	}
	public function getSaltoPagina(){
		if (!($this->SaltoPagina instanceof ValoresSN))
			$this->SaltoPagina = new ValoresSN($this->SaltoPagina);
		return $this->SaltoPagina;
	}

	public function setEstilo($Estilo){
		$this->Estilo = trim($Estilo);
	}
	public function getEstilo(){
		return $this->Estilo;
	}

	public function setIDDataFono1($IDDataFono1){
		$this->IDDataFono1 = $IDDataFono1;
	}
	public function getIDDataFono1(){
		if (!($this->IDDataFono1 instanceof Datafonos))
			$this->IDDataFono1 = new Datafonos($this->IDDataFono1);
		return $this->IDDataFono1;
	}

	public function setIDDataFono2($IDDataFono2){
		$this->IDDataFono2 = $IDDataFono2;
	}
	public function getIDDataFono2(){
		if (!($this->IDDataFono2 instanceof Datafonos))
			$this->IDDataFono2 = new Datafonos($this->IDDataFono2);
		return $this->IDDataFono2;
	}

	public function setIDDataFono3($IDDataFono3){
		$this->IDDataFono3 = $IDDataFono3;
	}
	public function getIDDataFono3(){
		if (!($this->IDDataFono3 instanceof Datafonos))
			$this->IDDataFono3 = new Datafonos($this->IDDataFono3);
		return $this->IDDataFono3;
	}

	public function setIDDataFono4($IDDataFono4){
		$this->IDDataFono4 = $IDDataFono4;
	}
	public function getIDDataFono4(){
		if (!($this->IDDataFono4 instanceof Datafonos))
			$this->IDDataFono4 = new Datafonos($this->IDDataFono4);
		return $this->IDDataFono4;
	}

} // END class tpvs

?>