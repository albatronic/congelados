<?php

/**
 * CONTROLLER FOR RecepcionarLineas EXTIENDE DE Controller
 *
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 15.06.2012 00:45:13

 * Extiende a la clase controller
 */
class RecepcionarLineasController extends Controller {

    protected $entity = "RecepcionarLineas";
    protected $parentEntity = "";

    public function __construct($request) {

        // Cargar lo que viene en el request
        $this->request = $request;

        // Cargar la configuracion del modulo (modules/moduloName/config.yaml)
        $this->form = new Form($this->entity);

        $this->values['request'] = $this->request;
    }

    public function indexAction($entidad = '', $idEntidad = '') {

        if ($entidad == '')
            $entidad = $this->request[2];
        if ($idEntidad == '')
            $idEntidad = $this->request[3];

        switch ($entidad) {
            case 'PedidosCab':
                $this->cargaLineasPedido($idEntidad);
                break;
            case 'ManufacCab':
                $this->cargaLineasManufac($idEntidad);
                break;
            case 'TraspasosCab':
                // NO SE CREAN LAS LÍNEAS DE RECEPCIÓN DE TRASPASO PORQUE
                // HAN SIDO CREADAS EN EL MOMENTO DE LA EXPEDICIÓN DEL TRASPASO
                //$this->cargaLineasTraspaso($idEntidad);
                break;
        }

        return $this->listAction($entidad, $idEntidad);
    }

    /**
     * Crea las líneas de recepción relativas al pedido
     *
     * @param integer $idPedido
     */
    private function cargaLineasPedido($idPedido) {

        $rows = array();
        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "select l.IDLinea
                    from {$em->getDataBase()}.pedidos_lineas l, {$em->getDataBase()}.articulos a
                    where l.IDPedido='{$idPedido}' and
                          l.IDEstado='1' and
                          l.IDArticulo=a.IDArticulo and
                          a.Inventario='1'
                    order by IDLinea ASC;";
            $em->query($query);
            $rows = $em->fetchResult();

            $query = "delete from recepciones where Entidad='PedidosCab' and IDEntidad='{$idPedido}' and Recepcionada='0'";
            $em->query($query);

            $em->desConecta();
        }

        // Por cada línea de pedido crea una línea de recepción
        foreach ($rows as $row) {
            $this->creaLineaRecepcion('PedidosCab', $idPedido, new PedidosLineas($row['IDLinea']));
        };
    }

    /**
     * Crea las líneas de recepciones relativas a cada línea de elaboración
     *
     * @param integer $idManufac
     */
    private function cargaLineasManufac($idManufac) {
        // Cargo las lineas de la elaboración de tipo 1 que no están recepcionadas y cuyos artículos son inventariables.
        // Borro las eventuales líneas de recepción que no están recepcionadas.
        $rows = array();
        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "select l.IDLinea
                    from {$em->getDataBase()}.manufac_lineas l, {$em->getDataBase()}.articulos a
                    where l.IDManufac = '{$idManufac}' and
                          l.IDEstado = '0' and
                          l.Tipo = '1' and
                          l.IDArticulo = a.IDArticulo and
                          a.Inventario = '1'
                    order by IDLinea ASC;";
            $em->query($query);
            $rows = $em->fetchResult();

            $query = "delete from recepciones where Entidad='ManufacCab' and IDEntidad='{$idManufac}' and Recepcionada='0'";
            $em->query($query);

            $em->desConecta();
        }
        unset($em);

        // Por cada línea de elaboración crea una línea de recepción
        foreach ($rows as $row) {
            $this->creaLineaRecepcion('ManufacCab', $idManufac, new ManufacLineas($row['IDLinea']));
        }
    }

    /**
     * Crea las líneas de recepciones relativas al traspaso
     *
     * @param integer $idTraspaso
     */
    public function cargaLineasTraspaso($idTraspaso) {
        // Cargo las lineas dl trapaso que no están recibidas y cuyos artículos son inventariables.
        // Borro las eventuales líneas de recepción que no están recibidas
        $rows = array();
        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "select l.IDLinea
                    from {$em->getDataBase()}.traspasos_lineas l, {$em->getDataBase()}.articulos a
                    where l.IDTraspaso = '{$idTraspaso}' and
                          l.IDEstado = '2' and
                          l.Tipo = '0' and
                          l.IDArticulo = a.IDArticulo and
                          a.Inventario = '1'
                    order by IDLinea ASC;";
            $em->query($query);
            $rows = $em->fetchResult();

            $query = "delete from recepciones where Entidad='TraspasosCab' and IDEntidad='{$idTraspaso}' and Recepcionada='0'";
            $em->query($query);

            $em->desConecta();
        }
        unset($em);

        // Por cada lína de traspaso crea una línea de recepción
        foreach ($rows as $row) {
            $this->creaLineaRecepcion('TraspasosCab', $idTraspaso, new TraspasosLineas($row['IDLinea']));
        }
    }

    public function listAction($entidad = '', $idEntidad = '') {

        if ($entidad == '')
            $entidad = $this->request[2];
        if ($idEntidad == '')
            $idEntidad = $this->request[3];

        $objetoEntidad = new $entidad($idEntidad);

        $lis = new Recepciones();
        $rows = $lis->cargaCondicion('IDLinea', "Entidad='{$entidad}' AND IDEntidad='{$idEntidad}'", 'IDLineaEntidad,IDLinea ASC');
        unset($lis);
        foreach ($rows as $row) {
            $lineas[] = new Recepciones($row['IDLinea']);
        }

        switch ($entidad) {
            case 'AlbaranesCab':
            case 'PedidosCab':
                $this->values['idAlmacenDestino'] = $objetoEntidad->getIDAlmacen()->getIDAlmacen();
                if ($objetoEntidad->getIDEstado()->getIDTipo() == '1') {
                    $template = 'RecepcionarLineas/form.html.twig';
                } else {
                    $template = 'RecepcionarLineas/list.html.twig';
                }
                break;
            case 'ManufacCab':
            case 'TraspasosCab':
                $this->values['idAlmacenDestino'] = $objetoEntidad->getIDAlmacenDestino()->getIDAlmacen();
                if ($objetoEntidad->getIDEstado()->getIDTipo() == '2') {
                    $template = 'RecepcionarLineas/form.html.twig';
                } else {
                    $template = 'RecepcionarLineas/list.html.twig';
                }
                break;
        }

        $this->values['puedoCambiarLotes'] = ($entidad != 'TraspasosCab');
        $this->values['anadirProducto'] = ($entidad == 'PedidosCab') or ($entidad == 'ManufacCab');
        $this->values['listado']['data'] = $lineas;
        $this->values['entidad'] = $entidad;
        $this->values['idEntidad'] = $idEntidad;
        $this->values['objetoEntidad'] = $objetoEntidad;

        unset($objetoEntidad);
        unset($lineas);

        return array('template' => $template, 'values' => $this->values);
        ;
    }

    /**
     * Crea un registro nuevo en la entidad correspondiente
     * y su correspondiente registro en las líneas de recepción
     * 
     * @return array con el template y valores a renderizar
     */
    public function newAction() {

        switch ($this->request["entidad"]) {

            //CREAR UNA LINEA DE PEDIDO NUEVA Y SU CORRESPONDIENTE LINEA DE RECEPCION
            case 'PedidosCab':
                $pedido = new PedidosCab($this->request['idEntidad']);

                $lineaPedido = new PedidosLineas();
                $lineaPedido->setIDPedido($this->request['idEntidad']);
                $lineaPedido->setIDArticulo($this->request['idArticulo']);
                $lineaPedido->setDescripcion($this->request['Descripcion']);
                $lineaPedido->setUnidades(0);
                $lineaPedido->setIDEstado(1);
                $lineaPedido->setIDAgente($_SESSION['USER']['user']['id']);
                $lineaPedido->setIDAlmacen($pedido->getIDAlmacen()->getIDAlmacen());
                if ($lineaPedido->valida()) {
                    if ($lineaPedido->create()) {
                        $this->creaLineaRecepcion($this->request['entidad'], $this->request['idEntidad'], $lineaPedido);
                        $pedido->setIDAlmacen($pedido->getIDAlmacen()->getIDAlmacen());
                        $pedido->setIncidencias($pedido->getIncidencias() . "\nFuera de pedido: " . $lineaPedido->getDescripcion());
                        $pedido->save();
                    }
                }

                $this->values['errores'] = $lineaPedido->getErrores();

                unset($pedido);
                unset($lineaPedido);
                break;
                
            //CREAR UNA LINEA DE PEDIDO NUEVA Y SU CORRESPONDIENTE LINEA DE RECEPCION                
            case 'ManufacCab':
                $manufac = new ManufacCab($this->request['idEntidad']);

                $lineaManufac = new ManufacLineas();
                $lineaManufac->setIDManufac($this->request['idEntidad']);
                $lineaManufac->setTipo(1);
                $lineaManufac->setIDArticulo($this->request['idArticulo']);
                $lineaManufac->setDescripcion($this->request['Descripcion']);
                $lineaManufac->setUnidades(1);
                $lineaManufac->setIDEstado(0);
                $lineaManufac->setIDAlmacen($manufac->getIDAlmacenDestino()->getIDAlmacen());
                if ($lineaManufac->valida()) {
                    if ($lineaManufac->create()) {
                        $this->creaLineaRecepcion($this->request['entidad'], $this->request['idEntidad'], $lineaManufac);
                        $manufac->setIDAlmacenDestino($manufac->getIDAlmacenDestino()->getIDAlmacen());
                        $manufac->setIncidencias($manufac->getIncidencias() . "\nFuera de pedido: " . $lineaManufac->getDescripcion());
                        $manufac->save();
                    }
                }

                $this->values['errores'] = $lineaManufac->getErrores();

                unset($manufac);
                unset($lineaManufac);
                break;
                
            //CREAR UNA LINEA DE TRASPASO NUEVA Y SU CORRESPONDIENTE LINEA DE RECEPCION                
            case 'TraspasosCab':
                $traspaso = new TraspasosCab($this->request['idEntidad']);

                $lineaTraspaso = new TraspasosLineas();
                $lineaTraspaso->setIDTraspaso($this->request['idEntidad']);
                $lineaTraspaso->setTipo(1);
                $lineaTraspaso->setIDArticulo($this->request['idArticulo']);
                $lineaTraspaso->setDescripcion($this->request['Descripcion']);
                $lineaTraspaso->setUnidades(1);
                $lineaTraspaso->setIDEstado(0);
                $lineaTraspaso->setIDAlmacen($traspaso->getIDAlmacenDestino()->getIDAlmacen());
                if ($lineaTraspaso->valida()) {
                    if ($lineaTraspaso->create()) {
                        $this->creaLineaRecepcion($this->request['entidad'], $this->request['idEntidad'], $lineaTraspaso);
                        $traspaso->setIDAlmacenDestino($traspaso->getIDAlmacenDestino()->getIDAlmacen());
                        $traspaso->setIncidencias($traspaso->getIncidencias() . "\nFuera de traspaso: " . $lineaTraspaso->getDescripcion());
                        $traspaso->save();
                    }
                }

                $this->values['errores'] = $lineaTraspaso->getErrores();

                unset($traspaso);
                unset($lineaTraspaso);
                break;                
        }

        return $this->listAction($this->request['entidad'], $this->request['idEntidad']);
    }

    /**
     * Edita, actualiza o borrar un registro
     * @return array con el template y valores a renderizar
     */
    public function editAction() {

        $entidad = $this->request['Recepciones']['Entidad'];
        $idEntidad = $this->request['Recepciones']['IDEntidad'];
        $idLineaEntidad = $this->request['Recepciones']['IDLineaEntidad'];

        switch ($this->request['accion']) {
            case 'G': //GUARDAR DATOS
                $datos = new Recepciones($this->request['Recepciones']['IDLinea']);
                $datos->bind($this->request['Recepciones']);
                if ($datos->valida($this->form->getRules()))
                    $datos->save();

                $this->values['errores'] = $datos->getErrores();
                $this->values['alertas'] = $datos->getAlertas();
                unset($datos);
                return $this->listAction($entidad, $idEntidad);
                break;

            case 'B': //BORRAR LINEA DE RECEPCION
                $datos = new Recepciones($this->request['Recepciones']['IDLinea']);
                $datos->erase();
                $this->values['errores'] = $datos->getErrores();
                unset($datos);
                return $this->listAction($entidad, $idEntidad);
                break;

            case 'Varios': //CREA OTRA LINEA DE RECEPCION, SI PROVIENE DE TRASPASO OBLIGO EL MISMO LOTE
                // Calculo las unidades totales que llevo entre todas las eventuales
                // lineas de recepción para la linea de pedido/traspaso/elaboración en curso
                $lineaRecepcion = new Recepciones();
                $rows = $lineaRecepcion->cargaCondicion("sum(UnidadesNetas) as UnidadesNetas", "Entidad='{$entidad}' and IDEntidad='{$idEntidad}' and IDLineaEntidad='{$idLineaEntidad}'");
                $suma = $rows[0]['UnidadesNetas'];

                // Propongo las unidades que faltan para la nueva linea de recepción
                $lineaRecepcion = new Recepciones($this->request['Recepciones']['IDLinea']);
                $unidades = $lineaRecepcion->getIDLineaEntidad()->getUnidades() - $suma;

                // Crea la linea nueva de recepción, si proviene de traspaso obligo el mismo lote
                $datos = new Recepciones();
                $datos->setEntidad($entidad);
                $datos->setIDEntidad($idEntidad);
                $datos->setIDLineaEntidad($idLineaEntidad);
                $datos->setIDAlmacen($lineaRecepcion->getIDAlmacen()->getIDAlmacen());
                $datos->setIDAlmacenero($lineaRecepcion->getIDAlmacenero()->getIDAgente());
                $datos->setIDArticulo($lineaRecepcion->getIDArticulo()->getIDArticulo());
                $datos->setUnidades($unidades);
                $datos->setUnidadMedida($lineaRecepcion->getUnidadMedida());
                $datos->setUnidadesBrutas($unidades);
                $datos->setUnidadesNetas($unidades);
                if ($entidad == 'TraspasosCab')
                    $datos->setIDLote($lineaRecepcion->getIDLote()->getIDLote());
                $datos->create();

                $this->values['errores'] = $datos->getErrores();
                $this->values['alertas'] = $datos->getAlertas();
                unset($datos);
                unset($lineaRecepcion);

                return $this->listAction($entidad, $idEntidad);
                break;
        }
    }

    /**
     * Crea una línea de recepcion en base al objetoLinea pasado
     *
     * @param string $entidad El nombre de la entidad padre (PedidosCab, ManufacCab, TraspasosCab, ...)
     * @param integer $idEntidad El id de la entidad padre
     * @param object $objetoLinea Objeto linea hija de la entidad padre correspondiente (linea de pedido, manufactura, traspaso, ...)
     */
    private function creaLineaRecepcion($entidad, $idEntidad, $objetoLinea) {

        $linea = new Recepciones();
        $linea->setEntidad($entidad);
        $linea->setIDEntidad($idEntidad);
        $linea->setIDLineaEntidad($objetoLinea->getIDLinea());
        $linea->setIDAlmacen($objetoLinea->getIDAlmacen()->getIDAlmacen());
        $linea->setIDAlmacenero($_SESSION['USER']['user']['id']);
        $linea->setIDArticulo($objetoLinea->getIDArticulo()->getIDArticulo());
        $linea->setUnidades($objetoLinea->getUnidades());
        $linea->setUnidadMedida($objetoLinea->getUnidadMedida());
        $linea->setUnidadesBrutas($objetoLinea->getUnidades());
        $linea->setUnidadesNetas($objetoLinea->getUnidades());
        $linea->create();
        unset($linea);
    }

    /**
     * Recepcionar el documento (pedido, traspaso, manufactura, ...)
     * @return <type>
     */
    public function RecepcionarAction() {

        $objeto = new $this->request['entidad']($this->request['idEntidad']);
        $objeto->recepciona($this->request['Incidencias']);

        $this->values['errores'] = $objeto->getErrores();
        $this->values['alertas'] = $objeto->getAlertas();

        if (!$this->values['errores'])
            $this->values['mensajes'][] = "El documento " . $objeto->getNumeroDocumento() . " ha sido recepcionado";

        return $this->listAction($this->request['entidad'], $this->request['idEntidad']);
    }

}

?>