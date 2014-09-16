<?php

/**
 * CONTROLLER FOR RutasVentasClientes
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 28.07.2011 12:01:02

 * Extiende a la clase controller
 */
class RutasVentasClientesController extends Controller {

    protected $entity = "RutasVentasClientes";
    protected $parentEntity = "RutasVentas";

    /**
     * Genera array con los clientes del la ruta para el
     * comercial y dia indicado
     *
     * @param integer $idComercial
     * @param integer $dia
     * @return <type>
     */
    public function listAction($idComercial='', $dia='') {

        if ($idComercial == '')
            $idComercial = $this->request[2];

        if ($dia == '')
            $dia = $this->request[3];

        $this->values['listado']['dia'] = $dia;

        // Busco los clientes del comercial indicado y sucursal actual
        // que aun no estén asignados al día solicitado
        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "SELECT IDCliente as Id, RazonSocial as Value FROM clientes
                        WHERE IDComercial='{$idComercial}'
                        AND IDSucursal='{$_SESSION['suc']}'
                        AND Vigente='1'
                        AND IDCliente NOT IN
                            (SELECT IDCliente FROM rutas_ventas
                            WHERE IDComercial='{$idComercial}' AND Dia='{$dia}')
                        ORDER BY RazonSocial ASC";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();
        }
        unset($em);

        $this->values['listado']['clientes'] = $rows;
        array_unshift($this->values['listado']['clientes'], array('Id' => '', 'Value' => ':: Indique un cliente'));

        // Busco las zonas de los clientes del comercial indicado y sucursal actual
        // que aun no estén asignados al día solicitado
        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "SELECT DISTINCT t1.IDZona as Id, t2.Zona as Value FROM clientes as t1, zonas as t2
                        WHERE t1.IDZona=t2.IDZona
                        AND t1.IDComercial='{$idComercial}'
                        AND t1.IDSucursal='{$_SESSION['suc']}'
                        AND t1.Vigente='1'    
                        AND t1.IDCliente NOT IN
                            (SELECT IDCliente FROM rutas_ventas
                            WHERE IDComercial='{$idComercial}' AND Dia='{$dia}')
                        ORDER BY t2.Zona ASC";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();
        }
        unset($em);

        $this->values['listado']['zonas'] = $rows;
        array_unshift($this->values['listado']['zonas'], array('Id' => '', 'Value' => ':: Indique una Zona'));
        //-----------------------------------------------
        // Lleno los clientes asignados al comercial y día
        // ordenados por Zona
        // -----------------------------------------------
        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "SELECT t1.Id FROM rutas_ventas as t1, clientes as t2
                        WHERE t1.IDCliente=t2.IDCliente
                        AND t2.IDSucursal='{$_SESSION['suc']}'
                        AND t2.Vigente='1'
                        AND t1.IDComercial='{$idComercial}'
                        AND t1.Dia='{$dia}'
                        ORDER BY t1.OrdenCliente,t1.IDZona";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();
        }
        unset($em);

        foreach ($rows as $row) {
            $lineas[] = new $this->parentEntity($row['Id']);
        }
        //-----------------------------------------------

        $template = $this->entity . '/list.html.twig';

        $this->values['linkBy']['value'] = $idComercial;
        $this->values['listado']['data'] = $lineas;
        $this->values['listado']['nClientes'] = count($lineas);

        unset($lis);
        unset($lineas);

        return array('template' => $template, 'values' => $this->values);
    }

    /**
     * Inserta clientes a la ruta (comercial-dia).
     * Puede insertar un solo cliente o bien todos los de un
     * codigo postal dado. Esto depende de la 'accion' que venga en el request
     * @return <type>
     */
    public function newAction() {
        if ($this->values['permisos']['I']) {

            switch ($this->request['accion']) {
                case 'cliente': //AÑADIR UN CLIENTE NUEVO
                    if ($this->request['IDCliente'] != '') {
                        $cliente = new Clientes($this->request['IDCliente']);
                        $datos = new $this->parentEntity();
                        $datos->setIDComercial($this->request['IDComercial']);
                        $datos->setDia($this->request['dia']);
                        $datos->setIDCliente($this->request['IDCliente']);
                        $datos->setIDZona($cliente->getIDZona()->getIDZona());
                        $datos->create();
                        unset($cliente);
                        unset($datos);
                        //$this->values['datos'] = $datos;
                    }
                    break;

                case 'zona': //INSERTAR TODOS LOS CLIENTES DE ESA ZONA
                    if ($this->request['IDZona'] != '') {
                        $cliente = new Clientes();
                        $rows = $cliente->cargaCondicion("IDCliente", "IDZona='{$this->request['IDZona']}' AND IDComercial='{$this->request['IDComercial']}' AND Vigente='1'");                        
                        unset($cliente);
                        foreach ($rows as $key => $value) {
                            $datos = new $this->parentEntity();
                            $datos->setIDComercial($this->request['IDComercial']);
                            $datos->setDia($this->request['dia']);
                            $datos->setIDZona($this->request['IDZona']);
                            $datos->setIDCliente($value['IDCliente']);
                            $datos->create();
                        }
                        unset($datos);
                    }
                    break;
            }
            return $this->listAction($this->request['IDComercial'], $this->request['dia']);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Cambiar el orden de la zona:
     * hay que cambiar el orden en todos los registros de esa zona.
     * @return <type>
     */
    public function cambiarOrdenZonaAction() {
        if ($this->values['permisos']['A']) {
            $datos = new $this->parentEntity($this->request['Id']);
            $rows = $datos->cargaCondicion("Id", "IDZona='{$datos->getIDZona()->getIDZona()}'");
            foreach ($rows as $row) {
                $datos = new $this->parentEntity($row['Id']);
                $datos->setOrdenZona($this->request['OrdenZona']);
                $datos->save();
            }
            unset($datos);
            return $this->listAction($this->request['IDComercial'], $this->request['dia']);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Cambiar el orden del cliente dentro de su zona
     * @return <type>
     */
    public function cambiarOrdenClienteAction() {
        if ($this->values['permisos']['A']) {
            $datos = new $this->parentEntity($this->request['Id']);
            $datos->setOrdenCliente($this->request['OrdenCliente']);
            $datos->save();
            return $this->listAction($this->request['IDComercial'], $this->request['dia']);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Borra todas los clientes de una zona
     * @return <type>
     */
    public function borrarZonaAction() {
        if ($this->values['permisos']['B']) {
            $datos = new $this->parentEntity($this->request['Id']);

            $em = new EntityManager("datos" . $_SESSION['emp']);
            if ($em->getDbLink()) {
                $em->query("DELETE FROM rutas_ventas WHERE IDZona='{$datos->getIDZona()->getIDZona()}' and IDComercial='{$this->request['IDComercial']}' and Dia='{$this->request['dia']}'");
                $em->desConecta();
            }
            unset($em);

            return $this->listAction($this->request['IDComercial'], $this->request['dia']);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Borrar un registro cuyo Id viene en el request Id
     * @return <type>
     */
    public function borrarClienteAction() {
        if ($this->values['permisos']['B']) {
            $datos = new $this->parentEntity($this->request['Id']);
            $datos->erase();
            return $this->listAction($this->request['IDComercial'], $this->request['dia']);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

}

?>