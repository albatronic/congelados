<?php

/**
 * Description of RecalculaStockController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 22-Septiembre-2014
 *
 */
class RecalculaStockController extends Controller {

    protected $entity = "RecalculaStock";
    protected $parentEntity = "";

    public function __construct($request) {

        // Cargar lo que viene en el request
        $this->request = $request;

        // Cargar la configuracion del modulo (modules/moduloName/config.yaml)
        $this->form = new Form($this->entity);

        // Cargar los permisos.
        // Si la entidad no está sujeta a control de permisos, se habilitan todos
        if ($this->form->getPermissionControl()) {
            if ($this->parentEntity == '')
                $this->permisos = new ControlAcceso($this->entity);
            else
                $this->permisos = new ControlAcceso($this->parentEntity);
        } else
            $this->permisos = new ControlAcceso();

        $this->values['titulo'] = $this->form->getTitle();
        $this->values['ayuda'] = $this->form->getHelpFile();
        $this->values['permisos'] = $this->permisos->getPermisos();
        $this->values['request'] = $this->request;
        $this->values['linkBy'] = array(
            'id' => $this->form->getLinkBy(),
            'value' => '',
        );

        // Si se ha indicado una entidad en el config.yml del controlador
        // pero no se ha definido la conexion, se muestra un error
        if (($this->form->getEntity()) and ( !$this->form->getConection())) {
            echo "No se ha definido la conexión para la entidad: " . $this->entity;
        }
    }

    public function IndexAction() {

        $almacenes = new Almacenes();
        $this->values['almacenes'] = $almacenes->fetchAll('', 'Nombre', false);
        unset($almacenes);

        return array(
            'template' => $this->entity . '/index.html.twig',
            'values' => $this->values
        );
    }

    public function RecalcularAction() {

        switch ($this->request["METHOD"]) {
            case 'POST':
                if ($this->values['permisos']['permisosModulo']['UP']) {
                    $this->values['resultado'] = $this->recalcula($this->request['idAlmacen']);

                    return $this->IndexAction();
                } else {
                    $template = "_global/forbiden.html.twig";
                }

                break;

            case 'GET':
                $template = "_global/forbiden.html.twig";
                break;
        }

        return array('template' => $template, 'values' => $this->values);
    }

    private function recalcula($idAlmacen) {

        // Poner las reservadas y el entrando a cero
        $stock = new Existencias();
        $stock->queryUpdate(array("Reservadas" => 0, "Entrando" => 0), "IDAlmacen='{$idAlmacen}'");

        // Borrar los registros de stock que estén a cero
        $stock->queryDelete("IDAlmacen='{$idAlmacen}' and Reales=0 and Pales=0 and Cajas=0 and Reservadas=0 and Entrando=0");

        // Calcular las reservas de los artículos inventariables
        $lineas = new AlbaranesLineas();
        $articulos = new Articulos();
        $tablaLineas = $lineas->getDataBaseName() . "." . $lineas->getTableName();
        $tablaArticulos = $articulos->getDataBaseName() . "." . $articulos->getTableName();
        unset($lineas);
        unset($articulos);

        $query = "select l.IDArticulo,a.Codigo,l.Descripcion, sum(l.Unidades) Unidades,l.UnidadMedida ";
        $query .= "from {$tablaLineas} as l ";
        $query .= "join {$tablaArticulos} as a on l.IDArticulo=a.IDArticulo ";
        $query .= "where l.IDEstado='1' and a.Inventario='1' ";
        $query .= "group by l.IDArticulo";
        $em = new EntityManager($_SESSION['project']['conection']);
        $em->query($query);
        $rows = $em->fetchResult();
        $em->desConecta();
        unset($em);
        $resultado['reservas'] = $rows;

        foreach ($rows as $row) {
            $stock = new Existencias();
            $stock->hazReserva($idAlmacen, $row['IDArticulo'], $row['Unidades'], $row['UnidadMedida']);
        }

        // Calcular el entrando de los artículos inventariables
        $lineas = new PedidosLineas();
        $articulos = new Articulos();
        $tablaLineas = $lineas->getDataBaseName() . "." . $lineas->getTableName();
        $tablaArticulos = $articulos->getDataBaseName() . "." . $articulos->getTableName();
        unset($lineas);
        unset($articulos);

        $query = "select l.IDArticulo,a.Codigo,l.Descripcion,sum(l.Unidades) Unidades,l.UnidadMedida ";
        $query .= "from {$tablaLineas} as l ";
        $query .= "join {$tablaArticulos} as a on l.IDArticulo=a.IDArticulo ";
        $query .= "where l.IDEstado='1' and a.Inventario='1' ";
        $query .= "group by l.IDArticulo";
        $em = new EntityManager($_SESSION['project']['conection']);
        $em->query($query);
        $rows = $em->fetchResult();
        $em->desConecta();
        unset($em);
        $resultado['entrando'] = $rows;
        
        foreach ($rows as $row) {
            $stock = new Existencias();
            $stock->hazEntrando($idAlmacen, $row['IDArticulo'], $row['Unidades'], $row['UnidadMedida']);
        }
        
        return $resultado;
    }

}
