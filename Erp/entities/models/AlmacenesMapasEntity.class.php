<?php

/**
 * Mapas de Almacenes
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 23.06.2012 12:54:36
 */

/**
 * @orm:Entity(almacenes_mapas)
 */
class AlmacenesMapasEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="almacenes_mapas")
     */
    protected $IDUbicacion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="almacenes_mapas")
     * @var entities\Almacenes
     */
    protected $IDAlmacen;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="almacenes_mapas")
     */
    protected $Ubicacion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="almacenes_mapas")
     */
    protected $X = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="almacenes_mapas")
     */
    protected $Y = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="almacenes_mapas")
     */
    protected $Z = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="almacenes_mapas")
     */
    protected $Alto = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="almacenes_mapas")
     */
    protected $Ancho = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="almacenes_mapas")
     */
    protected $Fondo = '0.00';
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'empresas';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'almacenes_mapas';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDUbicacion';
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
        'Almacenes',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDUbicacion($IDUbicacion) {
        $this->IDUbicacion = $IDUbicacion;
    }

    public function getIDUbicacion() {
        return $this->IDUbicacion;
    }

    public function setIDAlmacen($IDAlmacen) {
        $this->IDAlmacen = $IDAlmacen;
    }

    public function getIDAlmacen() {
        if (!($this->IDAlmacen instanceof Almacenes))
            $this->IDAlmacen = new Almacenes($this->IDAlmacen);
        return $this->IDAlmacen;
    }

    public function setUbicacion($Ubicacion) {
        $this->Ubicacion = trim($Ubicacion);
    }

    public function getUbicacion() {
        return $this->Ubicacion;
    }

    public function setX($X) {
        $this->X = $X;
    }

    public function getX() {
        return $this->X;
    }

    public function setY($Y) {
        $this->Y = $Y;
    }

    public function getY() {
        return $this->Y;
    }

    public function setZ($Z) {
        $this->Z = $Z;
    }

    public function getZ() {
        return $this->Z;
    }

    public function setAlto($Alto) {
        $this->Alto = $Alto;
    }

    public function getAlto() {
        return $this->Alto;
    }

    public function setAncho($Ancho) {
        $this->Ancho = $Ancho;
    }

    public function getAncho() {
        return $this->Ancho;
    }

    public function setFondo($Fondo) {
        $this->Fondo = $Fondo;
    }

    public function getFondo() {
        return $this->Fondo;
    }

}

// END class almacenes_mapas
?>