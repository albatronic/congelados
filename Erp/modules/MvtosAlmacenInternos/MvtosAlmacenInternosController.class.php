<?php

/**
 * Description of MvtosAlmacenInternosController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 22-jun-2012
 *
 */
class MvtosAlmacenInternosController extends Controller {

    protected $entity = "MvtosAlmacenInternos";
    protected $errores = array();

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

        // QUITAR LOS COMENTARIOS PARA Actualizar los favoritos para el usuario
        //if ($this->form->getFavouriteControl())
        //    $this->actualizaFavoritos();
    }

    public function IndexAction() {

        $usuario = new Agentes($_SESSION['USER']['user']['id']);
        $this->values['almacenes'] = $usuario->getAlmacenes();
        unset($usuario);

        return array('template' => $this->entity . "/index.html.twig", 'values' => $this->values);
    }

    public function newAction() {

        if ($this->request['METHOD'] == 'POST') {
            $datosRequest = $this->request['MvtosAlmacen'];
            if ($this->valida($datosRequest)) {
                $valores = array(
                    'UM' => 'UMA',
                    'Reales' => $datosRequest['Unidades'],
                    'Pales' => $datosRequest['Pales'],
                    'Cajas' => $datosRequest['Cajas'],
                    'Reservadas' => 0,
                    'Entrando' => 0,
                );
                
                $mvtoSalida = new MvtosAlmacen();
                $mvtoEntrada = new MvtosAlmacen();
                
                // Movimiento de salida
                $ok = $mvtoSalida->genera('MvtoInterno', 'S', 0, $datosRequest['IDAlmacen'], $datosRequest['IDArticulo'], $datosRequest['IDLote'], $datosRequest['IDUbicacion'], 0, $valores);
                // Movimiento de entrada en la ubicacion destino
                if ($ok) {
                    $mvtoEntrada->genera('MvtoInterno', 'E', 0, $datosRequest['IDAlmacen'], $datosRequest['IDArticulo'], $datosRequest['IDLote'], $datosRequest['IDUbicacionDestino'], 0, $valores);
                }
                unset($mvtoEntrada);
                unset($mvtoSalida);
            } else
                $this->values['errores'] = $this->errores;
        }

        return $this->IndexAction();
    }

    private function valida($datos) {

        if ($datos['IDAlmacen'] == '')
            $this->errores[] = "No se ha indicado el almacén";
        if ($datos['IDUbicacion'] == $datos['IDUbicacionDestino'])
            $this->errores[] = "Las dos ubicaciones son iguales";
        if ($datos['Unidades'] <= 0)
            $this->errores[] = "Las unidades indicadas no son válidas";
        if ($datos['IDArticulo'] == '')
            $this->errores[] = "No se ha indicado el artículo";
        if ($datos['IDLote'] == '')
            $this->errores[] = "No se ha indicado lote";
        if ($datos['IDUbicacion'] == '')
            $this->errores[] = "No se ha indicado la ubicación";

        return (count($this->errores) == 0);
    }

}
?>

