<?php

/**
 * Description of Agentes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Agentes extends AgentesEntity {

    public function __toString() {
        if ($this->Nombre)
            return $this->getNombre();
        else
            return "";
    }

    public function getNombreApellidos() {
        return $this->Nombre;
    }

    public function validaLogico() {

        if ($this->IDEmpresa == '')
            $this->IDEmpresa = NULL;
        if ($this->IDSucursal == '')
            $this->IDSucursal = NULL;
        if ($this->IDAlmacen == '')
            $this->IDAlmacen = NULL;

        $this->Password = md5($this->Password);
        $this->Quien = md5($this->Password . "Pablo");
    }

    /**
     * Devuelve un array con todas las empresas
     * a las que tiene acceso el usuario
     * @return array
     */
    public function getEmpresas() {

        if ($this->IDEmpresa < 1) { //Puede acceder a todas
            $empresa = $this->getIDEmpresa();
            $empresas = $empresa->fetchAll('RazonSocial');
        } else { //Puede acceder solo a una
            $empresa = $this->getIDEmpresa();
            $empresas[] = array(
                'Id' => $empresa->getIDEmpresa(),
                'Value' => $empresa->getRazonSocial(),
            );
        }
        return $empresas;
    }

    /**
     * Devuelve un array con todas las sucursales de la empresa indicada
     * a las que tiene acceso el usuario logeado.
     *
     * Si no se indica empresa, se toma la actual: $_SESSION['emp']
     *
     * Si el usuario puede acceder a todas las sucursales y se activa a TRUE
     * el parámetro $opcionTodas, se añade un valor más en el array
     * con la opcion '** Todas **'
     *
     * @param integer $idEmpresa
     * @param boolean $opcionTodas
     * @return array
     */
    public function getSucursales($idEmpresa = '', $opcionTodas = TRUE) {

        if ($idEmpresa == '')
            $idEmpresa = $_SESSION['emp'];

        if ($this->IDSucursal < 1) { //Puede acceder a todas
            $em = new EntityManager("empresas");
            $link = $em->getDbLink();

            if (is_resource($link)) {
                $query = "select IDSucursal as Id, Nombre as Value from sucursales where IDEmpresa='" . $idEmpresa . "'";
                $em->query($query);
                $sucursales = $em->fetchResult();
                $em->desConecta();
                if ($opcionTodas)
                    $sucursales[] = array('Id' => '', 'Value' => '** Todas **');
            }
        } else { //Puede acceder solo a una
            $sucursal = $this->getIDSucursal();
            $sucursales[] = array(
                'Id' => $sucursal->getIDSucursal(),
                'Value' => $sucursal->getNombre(),
            );
        }
        return $sucursales;
    }

    /**
     * Devuelve un array con todos los almacenes de la empresa a
     * los que tiene acceso el usuario.
     * Si no se indica empresa, se toma la actual: $_SESSION['emp']
     *
     * @param integer $idEmpresa EL id de Empresa
     * @return array
     */
    public function getAlmacenes($idEmpresa = '') {

        if ($idEmpresa == '')
            $idEmpresa = $_SESSION['emp'];

        if ($this->IDAlmacen < 1) { //Puede acceder a todos
            $almacen = new Almacenes();
            $almacenes = $almacen->fetchAll($idEmpresa, 'Nombre');
        } else { //Puede acceder solo a una
            $almacen = new Almacenes($this->IDAlmacen);
            $almacenes[] = array(
                'Id' => $almacen->getIDAlmacen(),
                'Value' => $almacen->getNombre(),
            );
        }
        unset($almacen);

        return $almacenes;
    }

    /**
     * Devuelve un array con los agentes que son COMERCIALES (ROL=1)
     * y están adscritos a la empresa y sucursal indicada.
     * Si el agente en curso es comercial, solo se mostrará el mismo.
     *
     * @param integer $idEmpresa Opcional
     * @param integer $idSucursal Opcional
     * @return array
     */
    public function getComerciales($idEmpresa = '', $idSucursal = '') {
        $usuario = new Agentes($_SESSION['USER']['user']['id']);
        $tableName = $usuario->getDataBaseName() . "." . $usuario->getTableName();

        switch ($usuario->getRol()->getIDTipo()) {
            case '1': // ROL COMERCIAL
                $comerciales[] = array('Id' => $usuario->getIDAgente(), 'Value' => $usuario->getNombre());
                break;

            default: // RESTO DE ROLES
                if ($idEmpresa == '')
                    $idEmpresa = $_SESSION['emp'];
                if ($idSucursal == '')
                    $idSucursal = $_SESSION['suc'];

                $em = new EntityManager("empresas");
                $link = $em->getDbLink();
                if (is_resource($link)) {
                    $query = "select IDAgente as Id, Nombre as Value from {$tableName} where " .
                            "(Rol='1') AND " .
                            "(Activo='1') AND ( " .
                            "(IDEmpresa='" . $idEmpresa . "' and IDSucursal='" . $idSucursal . "') OR " .
                            "isnull(IDEmpresa) OR " .
                            "(IDEmpresa='" . $idEmpresa . "' AND isnull(IDSucursal)) ) ORDER BY Nombre";
                    $em->query($query);
                    $comerciales = $em->fetchResult();
                    $em->desConecta();
                    $comerciales[] = array('Id' => '', 'Value' => '** Todos **');
                }
                unset($em);
                break;
        }
        unset($usuario);

        return $comerciales;
    }

    /**
     * Devuelve un array con los agentes que son REPARTIDORES (ROL=2)
     * y están adscritos a la empresa y sucursal indicada.
     * Si el agente en curso es repartidor, solo se mostrará el mismo.
     *
     * @param integer $idEmpresa Opcional
     * @param integer $idSucursal Opcional
     * @return array
     */
    public function getRepartidores($idEmpresa = '', $idSucursal = '') {
        $usuario = new Agentes($_SESSION['USER']['user']['id']);

        switch ($usuario->getRol()->getIDTipo()) {
            case '2': // ROLL REPARTIDOR
                $repartidores[] = array('Id' => $usuario->getIDAgente(), 'Value' => $usuario->getNombre());
                break;

            default: // RESTO DE ROLES
                if ($idEmpresa == '')
                    $idEmpresa = $_SESSION['emp'];
                if ($idSucursal == '')
                    $idSucursal = $_SESSION['suc'];

                $em = new EntityManager("empresas");
                $link = $em->getDbLink();
                if (is_resource($link)) {
                    $query = "select IDAgente as Id, Nombre as Value from agentes where " .
                            "(Rol='2') AND " .
                            "(Activo='1') AND ( " .
                            "(IDEmpresa='" . $idEmpresa . "' and IDSucursal='" . $idSucursal . "') OR " .
                            "isnull(IDEmpresa) OR " .
                            "(IDEmpresa='" . $idEmpresa . "' AND isnull(IDSucursal)) ) ORDER BY Nombre";
                    $em->query($query);
                    $repartidores = $em->fetchResult();
                    $em->desConecta();
                    $repartidores[] = array('Id' => '', 'Value' => '** Todos **');
                }
                unset($em);
                break;
        }
        unset($usuario);

        return $repartidores;
    }

    /**
     * Devuelve un array con los agentes que son CAMARISTAS (ROL=3)
     * y están adscritos a la empresa y sucursal indicada.
     * Si el agente en curso es camarista, solo se mostrará el mismo.
     *
     * @param integer $idEmpresa Opcional
     * @param integer $idSucursal Opcional
     * @return array
     */
    public function getCamaristas($idEmpresa = '', $idSucursal = '') {
        $usuario = new Agentes($_SESSION['USER']['user']['id']);

        switch ($usuario->getRol()->getIDTipo()) {
            case '3': // ROLL CAMARISTA
                $camaristas[] = array('Id' => $usuario->getIDAgente(), 'Value' => $usuario->getNombre());
                break;

            default: // RESTO DE ROLES
                if ($idEmpresa == '')
                    $idEmpresa = $_SESSION['emp'];
                if ($idSucursal == '')
                    $idSucursal = $_SESSION['suc'];

                $em = new EntityManager("empresas");
                $link = $em->getDbLink();
                if (is_resource($link)) {
                    $query = "select IDAgente as Id, Nombre as Value from agentes where " .
                            "(Rol='3') AND " .
                            "(Activo='1') AND ( " .
                            "(IDEmpresa='" . $idEmpresa . "' and IDSucursal='" . $idSucursal . "') OR " .
                            "isnull(IDEmpresa) OR " .
                            "(IDEmpresa='" . $idEmpresa . "' AND isnull(IDSucursal)) ) ORDER BY Nombre";
                    $em->query($query);
                    $camaristas = $em->fetchResult();
                    $em->desConecta();
                }
                unset($em);
                break;
        }
        unset($usuario);

        return $camaristas;
    }

    public function setRepitePassword($repitePassword) {
        $this->_repitePassword = $repitePassword;
    }

    public function getRepitePassword() {
        return $this->_repitePassword;
    }

    public function getEsComercial() {
        return ($this->Rol == '1');
    }

    public function getEsRepartidor() {
        return ($this->Rol == '2');
    }

    public function getEsAlmacenero() {
        return ($this->Rol == '3');
    }

    /**
     * Devuelve un array con las direcciones ips
     * permitidas para el usuario
     *
     * @return array Array con las ips
     */
    public function getArrayIps() {
        $array = array();
        $array = explode(",", $this->Ips);
        return $array;
    }

}

?>
