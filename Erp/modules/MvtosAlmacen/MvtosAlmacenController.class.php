<?php

/**
 * CONTROLLER FOR MvtosAlmacen
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 17.08.2011 22:57:35

 * Extiende a la clase controller
 */
class MvtosAlmacenController extends Controller {

    protected $entity = "MvtosAlmacen";
    protected $parentEntity = "";

    public function __construct($request) {

        parent::__construct($request);

        $tipoMvto = new TiposMvtosAlmacen();
        $tiposMvtos = $tipoMvto->cargaCondicion("Id as Id, Descripcion as Value", "Uso='M'");
        $this->values['tiposMvtos'] = $tiposMvtos;
        unset($tipoMvto);
    }

    /**
     * Crea un registro nuevo
     *
     * Si viene por GET muestra un template vacio
     * Si viene por POST crea un registro
     *
     * @return array con el template y valores a renderizar
     */
    public function newAction() {

        if ($this->values['permisos']['I']) {
            switch ($this->request["METHOD"]) {
                case 'GET': //MOSTRAR FORMULARIO VACIO

                    $datos = new MvtosAlmacen();
                    $this->values['datos'] = $datos;
                    $this->values['errores'] = array();
                    return array('template' => $this->entity . '/new.html.twig', 'values' => $this->values);
                    break;

                case 'POST': //CREAR NUEVO REGISTRO

                    $datosRequest = $this->request['MvtosAlmacen'];

                    $valores = array(
                        'UM' => 'UMA',
                        'Reales' => $datosRequest['Unidades'],
                        'Pales' => $datosRequest['Pales'],
                        'Cajas' => $datosRequest['Cajas'],
                    );

                    $tipoMvtos = new TiposMvtosAlmacen($datosRequest['IDTipo']);
                    $signo = $tipoMvtos->getSigno()->getIDTipo();
                    $documento = $tipoMvtos->getTipoDocumento();
                    unset($tipoMvtos);

                    $idAlmacen = $datosRequest['IDAlmacen'];
                    $idArticulo = $datosRequest['IDArticulo'];
                    $idLote = $datosRequest['IDLote'];
                    $idUbicacion = $datosRequest['IDUbicacion'];


                    $datos = new MvtosAlmacen();
                    $datos->setDescripcion($datosRequest['Descripcion']);
                    $datos->setObservaciones($datosRequest['Observaciones']);

                    if ($datos->genera($documento, $signo, 0, $idAlmacen, $idArticulo, $idLote, $idUbicacion, 0, $valores)) {
                        $this->values['errores'] = $datos->getErrores();
                        $this->values['alertas'] = $datos->getAlertas();

                        //Recargo el objeto para refrescar las propiedas que
                        //hayan podido ser objeto de algun calculo durante el proceso
                        //de guardado.
                        $datos = new MvtosAlmacen($datos->getPrimaryKeyValue());
                        $this->values['datos'] = $datos;

                        if ($this->values['errores']) {
                            return array('template' => $this->entity . '/new.html.twig', 'values' => $this->values);
                        } else {
                            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
                        }
                    } else {
                        $this->values['datos'] = $datos;
                        $this->values['errores'] = $datos->getErrores();
                        $this->values['alertas'] = $datos->getAlertas();
                        return array('template' => $this->entity . '/new.html.twig', 'values' => $this->values);
                    }
                    break;
            }
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

}

?>