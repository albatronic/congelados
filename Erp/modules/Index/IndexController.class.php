<?php

/**
 * Description of IndexController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 28-may-2011
 *
 */
class IndexController extends Controller {

    protected $entity = "Index";
    protected $parentEntity = "";

    /**
     * Muestra un template con los accesos favoritos del usuario
     * @return array
     */
    public function IndexAction() {
        // QUITAR LOS COMENTARIOS PARA MOSTAR LOS FAVORITOS
        //$this->values = array('favoritos' => $this->getTopFavoritos());
        // Poner en la sesión el id del Rol del usuario
        $usuario = new Agentes($_SESSION['USER']['user']['id']);
        $_SESSION['USER']['user']['IdRol'] = $usuario->getRol()->getIDTipo();

        // Poner en la sesión TRUE/FALSE para la posibilidad de cambiar
        // los precios unitarios de los albaranes dependieno del rol del usuraio
        // según el parámetro ROLCP
        $param = new Parametros();
        $param = $param->find("Codigo", "ROLCP");
        $rolesCambioPrecio = explode(",", trim($param->getValor()));
        $_SESSION['USER']['user']['cambioPrecios'] = in_array($_SESSION['USER']['user']['IdRol'], $rolesCambioPrecio);

        // Poner en la sesión la política de actualización de precios en base
        // al parámetro ACTU_PRECIOS
        $param = new Parametros();
        $param = $param->find("Codigo", "ACTU_PRECIOS");
        $_SESSION['USER']['user']['actuPrecios'] = strtoupper(trim($param->getValor()));

        unset($usuario);
        unset($param);

        $this->values['sucursal'] = new Sucursales($_SESSION['suc']);

        if ($_SESSION['tpv']) {
            $this->values['dashBoard'] = $this->getDashBoard();
        }

        return array('template' => 'Index/index.html.twig', 'values' => $this->values);
    }

    /**
     * Acciones a realizar cuando se selecciona otra empresa
     * Se cambia el valor de la variable de session 'emp'
     * y se recargan las sucursales de la nueva empresa.
     * @return
     */
    public function EmpresaAction() {
        //Activo la empresa nueva
        $_SESSION['emp'] = $this->request['Empresa'];

        //Buscar las sucursales de la nueva empresa seleccionada
        $user = new Agentes($_SESSION['USER']['user']['id']);
        $_SESSION['USER']['sucursales'] = $user->getSucursales($_SESSION['emp']);

        //Activo la sucursal nueva
        $_SESSION['suc'] = $_SESSION['USER']['sucursales'][0]['Id'];

        //Activo la version
        $empresa = new Empresas($_SESSION['emp']);
        $_SESSION['ver'] = $empresa->getIDVersion()->getIDTipo();
        unset($empresa);

        //Desactivo el tpv para forzar a la elección de un nuevo
        unset($_SESSION['tpv']);

        $this->values['sucursal'] = new Sucursales($_SESSION['suc']);
        return array('template' => 'Index/index.html.twig', 'values' => $this->values);
    }

    /**
     * Acciones a realizar cuando se selecciona otra sucursal
     * Se cambia el valor de la variable de session 'suc'
     * @return
     */
    public function SucursalAction() {

        $_SESSION['suc'] = $this->request['Sucursal'];

        //Desactivo el tpv para forzar a la elección de un nuevo
        unset($_SESSION['tpv']);

        $this->values['sucursal'] = new Sucursales($_SESSION['suc']);
        return array('template' => 'Index/index.html.twig', 'values' => $this->values);
    }

    /**
     * Activa el código de tpv utilizado
     * @return <type>
     */
    public function setTpvAction() {

        $_SESSION['tpv'] = $this->request['IDTpv'];
        return $this->IndexAction();
    }

    /**
     * Genera el array con la información para el dashboard
     * 
     * @return array
     */
    public function getDashBoard() {

        $idRol = $_SESSION['USER']['user']['IdRol'];

        $array = array();

        $array['presupuestos'] = DashBoard::getPresupuestos();

        // La tesoreria la muestro si el rol es admon o super
        if ($idRol == '0' or $idRol == '9')
            $array['tesoreria'] = DashBoard::getTesoreria();

        $array['logistica']['entradasRetrasadas'] = DashBoard::getEntradasRetrasadas();
        $array['logistica']['entradasHoy'] = DashBoard::getEntradasHoy();
        $array['logistica']['pendienteServir'] = DashBoard::getPteServir();
        $array['logistica']['roturasStock'] = DashBoard::getRoturasStock();

        $array['tops']['clientes'] = DashBoard::getTopNClientes();
        $array['tops']['articulos'] = DashBoard::getTopNArticulos();
        $array['tops']['familias'] = DashBoard::getTopNFamilias();

        // El top de comerciales se muestra si el rol es admon o super
        if ($idRol == '0' or $idRol == '9')
            $array['tops']['comerciales'] = DashBoard::getTopNComerciales();

        return $array;
    }

}

?>
