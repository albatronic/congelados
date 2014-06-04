<?php

/**
 * CONTROLLER FOR ExpedirLineas EXTIENDE DE Controller
 *
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 15.06.2012 00:45:13

 * Extiende a la clase controller
 */
class ExpedirLineasController extends Controller {

    protected $entity = "ExpedirLineas";
    protected $parentEntity = "";

    public function __construct($request) {

        // Cargar lo que viene en el request
        $this->request = $request;

        // Cargar la configuracion del modulo (modules/moduloName/config.yaml)
        $this->form = new Form($this->entity);

        $this->values['request'] = $this->request;
    }

    public function indexAction($entidad = '', $idEntidad = '', $idRepartidor = '') {

        if ($entidad == '')
            $entidad = $this->request[2];
        if ($idEntidad == '')
            $idEntidad = $this->request[3];
        if ($idRepartidor == '')
            $idRepartidor = $this->request[4];

        switch ($entidad) {
            case 'AlbaranesCab':
                $this->cargaLineasAlbaran($idEntidad, $idRepartidor);
                break;
            case 'ManufacCab':
                $this->cargaLineasManufac($idEntidad);
                break;
            case 'TraspasosCab':
                $this->cargaLineasTraspaso($idEntidad);
                break;
        }

        return $this->listAction($entidad, $idEntidad);
    }

    /**
     * Crea las líneas de expediciones relativas al albarán
     * 
     * @param integer $idAlbaran
     * @param integer $idRepartidor 
     */
    private function cargaLineasAlbaran($idAlbaran, $idRepartidor) {
        // Cargo las lineas del albarán que no están expedidas y cuyos artículos son inventariables.
        // Borro las eventuales líneas de expedición que no están expedidas.
        $rows = array();
        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "select l.IDLinea
                    from {$em->getDataBase()}.albaranes_lineas l, {$em->getDataBase()}.articulos a
                    where l.IDAlbaran = '{$idAlbaran}' and
                          l.IDEstado = '1' and
                          l.IDArticulo = a.IDArticulo and
                          a.Inventario = '1'
                    order by IDLinea ASC;";
            $em->query($query);
            $rows = $em->fetchResult();

            $query = "delete from expediciones where Entidad='AlbaranesCab' and IDEntidad='{$idAlbaran}' and Expedida='0'";
            $em->query($query);

            $em->desConecta();
        }
        unset($em);

        // Crea las líneas de expedición preasignando lotes y ubicaciones
        foreach ($rows as $row) {
            $this->preasignaLinea('AlbaranesCab', $idAlbaran, new AlbaranesLineas($row['IDLinea']), $idRepartidor);
        }
    }

    /**
     * Crea las líneas de expediciones relativas a la elaboración
     *
     * @param integer $idManufac
     */
    private function cargaLineasManufac($idManufac) {
        // Cargo las lineas de la elaboración que no están expedidas y cuyos artículos son inventariables.
        // Borro las eventuales líneas de expedición que no están expedidas.
        $rows = array();
        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "select l.IDLinea
                    from {$em->getDataBase()}.manufac_lineas l, {$em->getDataBase()}.articulos a
                    where l.IDManufac = '{$idManufac}' and
                          l.IDEstado = '1' and
                          l.Tipo = '0' and
                          l.IDArticulo = a.IDArticulo and
                          a.Inventario = '1'
                    order by IDLinea ASC;";
            $em->query($query);
            $rows = $em->fetchResult();

            $query = "delete from expediciones where Entidad='ManufacCab' and IDEntidad='{$idManufac}' and Expedida='0'";
            $em->query($query);

            $em->desConecta();
        }
        unset($em);

        // Crea las líneas de expedición preasignando lotes y ubicaciones
        foreach ($rows as $row) {
            $this->preasignaLinea('ManufacCab', $idManufac, new ManufacLineas($row['IDLinea']));
        }
    }

    /**
     * Crea las líneas de expediciones relativas al traspaso
     *
     * @param integer $idTraspaso
     */
    private function cargaLineasTraspaso($idTraspaso) {
        // Cargo las lineas del traspaso que no están expedidas y cuyos artículos son inventariables.
        // Borro las eventuales líneas de expedición que no están expedidas.
        $rows = array();
        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "select l.IDLinea
                    from {$em->getDataBase()}.traspasos_lineas l, {$em->getDataBase()}.articulos a
                    where l.IDTraspaso = '{$idTraspaso}' and
                          l.IDEstado = '1' and
                          l.Tipo = '0' and
                          l.IDArticulo = a.IDArticulo and
                          a.Inventario = '1'
                    order by IDLinea ASC;";
            $em->query($query);
            $rows = $em->fetchResult();

            $query = "delete from expediciones where Entidad='TraspasosCab' and IDEntidad='{$idTraspaso}' and Expedida='0'";
            $em->query($query);

            $em->desConecta();
        }
        unset($em);

        // Crea las líneas de expedición preasignando lotes y ubicaciones
        foreach ($rows as $row) {
            $this->preasignaLinea('TraspasosCab', $idTraspaso, new TraspasosLineas($row['IDLinea']));
        }
    }

    public function listAction($entidad = '', $idEntidad = '') {

        if ($entidad == '')
            $entidad = $this->request[2];
        if ($idEntidad == '')
            $idEntidad = $this->request[3];

        $objetoEntidad = new $entidad($idEntidad);

        $lis = new Expediciones();
        $rows = $lis->cargaCondicion('IDLinea', "Entidad='{$entidad}' AND IDEntidad='{$idEntidad}'", 'IDLineaEntidad,IDLinea ASC');
        unset($lis);
        foreach ($rows as $row) {
            $lineas[] = new Expediciones($row['IDLinea']);
        }

        if (($entidad == "AlbaranesCab") and (count($lineas) == 0)) {
            // En un albarán que no tiene nada que expedir, posiblemente esté vacío
            // o sus artículos no son inventariables. Lo expedido directamente
            $objetoEntidad->expide();
        }

        if ($objetoEntidad->getIDEstado()->getIDTipo() == '1') {
            $template = 'ExpedirLineas/form.html.twig';
        } else {
            $template = 'ExpedirLineas/list.html.twig';
        }

        $this->values['listado']['data'] = $lineas;
        $this->values['entidad'] = $entidad;
        $this->values['idEntidad'] = $idEntidad;
        $this->values['objetoEntidad'] = $objetoEntidad;

        unset($objetoEntidad);
        unset($lineas);

        return array('template' => $template, 'values' => $this->values);
    }

    /**
     * Edita, actualiza o borrar un registro
     *
     * Viene siempre por POST
     * Actualiza o Borrar según el valor de $this->request['accion']
     *
     * @return array con el template y valores a renderizar
     */
    public function editAction() {

        switch ($this->request['accion']) {
            case 'G': //GUARDAR DATOS
                $datos = new Expediciones($this->request['Expediciones']['IDLinea']);
                $datos->bind($this->request['Expediciones']);
                if ($datos->valida($this->form->getRules()))
                    $datos->save();
                $this->values['errores'] = $datos->getErrores();
                $this->values['alertas'] = $datos->getAlertas();
                unset($datos);
                return $this->listAction($this->request['entidad'], $this->request['idEntidad']);
                break;

            case 'B': //BORRAR DATOS
                $datos = new Expediciones($this->request['Expediciones']['IDLinea']);
                $datos->erase();
                $this->values['errores'] = $datos->getErrores();
                unset($datos);
                return $this->listAction($this->request['entidad'], $this->request['idEntidad']);
                break;
        }
    }

    /**
     * Expedir el documento (albaran,traspaso, manufactura, ...)
     * @return <type>
     */
    public function ExpedirAction() {

        $objeto = new $this->request['entidad']($this->request['idEntidad']);
        $objeto->expide();

        $this->values['errores'] = $objeto->getErrores();
        $this->values['alertas'] = $objeto->getAlertas();

        if (!$this->values['errores'])
            $this->values['mensajes'][] = "El documento " . $objeto->getNumeroDocumento() . " ha sido expedido";

        return $this->listAction($this->request['entidad'], $this->request['idEntidad']);
    }

    /**
     * Crear la/s linea/s de expedición para la línea de albarán, asignando lote y ubicación (si procede).
     *
     * Para cada línea de albarán se crean tantas líneas de expedición como sea
     * necesario. Es decir, si de un mismo lote-ubicación no hay suficiente stock como el solicitado,
     * hay que coger otro lote y/o ubicación, lo que supone crear otra línea de expedición.
     *
     * Si no hay stock suficiente, se propone el que hay. Y si no hay ningún stock y el
     * artículo no bloquea stock, se crea la línea de expedición con las unidades a 0.
     *
     * OJO!!!! No se hace la reserva de stock en el registro de existencias específico
     * para el lote-ubicacion.
     *
     * @param string $entidad El nombre de la entidad padre de la línea que hay que asignar (AlbaranesCab,ManufacCab, TraspasosCab)
     * @param integer $idEntidad El id de la entidad padre
     * @param Object $linea Objeto línea que hay que preasignar (albaran, traspaso, manufactura, etc)
     * @param integer $idRepartidor El id del repartidor que se le asigna a la expedición
     */
    private function preasignaLinea($entidad, $idEntidad, $linea, $idRepartidor = '') {

        // Hay que buscar los lotes y ubicaciones necesarios para servir la línea de albarán

        $controlTrazabilidad = ($linea->getIDArticulo()->getTrazabilidad()->getIDTipo() == '1');
        $controlUbicacion = ($linea->getIDAlmacen()->getControlUbicaciones()->getIDTipo() == '1');

        if ($controlUbicacion) {
            if ($controlTrazabilidad)
                $this->preasignaLotesUbicaciones($entidad, $idEntidad, $linea, $idRepartidor);
            else
                $this->preasignaUbicaciones($entidad, $idEntidad, $linea, $idRepartidor);
        } else {
            if ($controlTrazabilidad) {
                $this->preasignaLotes($entidad, $idEntidad, $linea, $idRepartidor);
            }else
                $this->preasignaStock($entidad, $idEntidad, $linea, $idRepartidor);
        }
    }

    /**
     * Genera tantas líneas de expedición como lotes y ubicaciones sean
     * necesarias para servir la cantidad indicada en la línea de albarán
     *
     * @param AlbaranesLineas $lineaAlbaran
     * @param integer $idRepartidor
     */
    private function preasignaLotesUbicaciones($entidad, $idEntidad, $linea, $idRepartidor = '') {

        $idLineaEntidad = $linea->getPrimaryKeyValue();
        $articulo = $linea->getIDArticulo();
        $idAlmacen = $linea->getIDAlmacen()->getIDAlmacen();
        $unidades = $linea->getUnidades();

        $lotesUbicaciones = $articulo->getLotesUbicaciones($idAlmacen, $unidadesAlmacen);
        $acumulado = 0;


        if ($lotesUbicaciones) {
            // Hay stock. Tomo los lotes y ubicaciones necesarias
            foreach ($lotesUbicaciones as $item) {
                $unidadesAlmacen = $articulo->convertUnit($linea->getUnidadMedida(), 'UMA', $unidades);

                $lineaExpedicion = new Expediciones();
                $lineaExpedicion->setEntidad($entidad);
                $lineaExpedicion->setIDEntidad($idEntidad);
                $lineaExpedicion->setIDLineaEntidad($idLineaEntidad);
                $lineaExpedicion->setIDAlmacen($idAlmacen);
                $lineaExpedicion->setIDAlmacenero($_SESSION['USER']['user']['id']);
                $lineaExpedicion->setIDRepartidor($idRepartidor);
                $lineaExpedicion->setIDArticulo($articulo->getIDArticulo());
                $asignado = ($item['Reales'] > $unidadesAlmacen) ? $unidadesAlmacen : $item['Reales'];
                $asignado = $articulo->convertUnit('UMA', $linea->getUnidadMedida(), $asignado);
                $lineaExpedicion->setUnidades($asignado);
                $lineaExpedicion->setUnidadMedida($linea->getUnidadMedida());
                $lineaExpedicion->setIDLote($item['IDLote']);
                $lineaExpedicion->setIDUbicacion($item['IDUbicacion']);
                $lineaExpedicion->setFlagTrazabilidad(1);
                $lineaExpedicion->setFlagUbicacion(1);
                $lineaExpedicion->setFlagSinStock(0);
                $lineaExpedicion->create();

                $acumulado = $acumulado + $asignado;
                if ($acumulado >= $unidades)
                    break;
                $unidades = $unidades - $asignado;
            }
        } else {
            // No hay stock, creo la línea de expedición a 0 y pongo el flagSinstock
            // a 1 si el artículo no bloquea stock o
            // a 2 si sí bloquea stock
            // De esta forma el artículo se mostrará en el parte de expedición
            // pero no se dejará expedir.
            $lineaExpedicion = new Expediciones();
            $lineaExpedicion->setEntidad($entidad);
            $lineaExpedicion->setIDEntidad($idEntidad);
            $lineaExpedicion->setIDLineaEntidad($idLineaEntidad);
            $lineaExpedicion->setIDAlmacen($idAlmacen);
            $lineaExpedicion->setIDAlmacenero($_SESSION['USER']['user']['id']);
            $lineaExpedicion->setIDRepartidor($idRepartidor);
            $lineaExpedicion->setIDArticulo($articulo->getIDArticulo());
            $lineaExpedicion->setUnidades(0);
            $lineaExpedicion->setUnidadMedida($linea->getUnidadMedida());
            $lineaExpedicion->setIDLote(0);
            $lineaExpedicion->setIDUbicacion(0);
            $lineaExpedicion->setFlagTrazabilidad(1);
            $lineaExpedicion->setFlagUbicacion(1);
            if ($articulo->getBloqueoStock()->getIDTipo() == '0')
                $lineaExpedicion->setFlagSinStock(1);
            else
                $lineaExpedicion->setFlagSinStock(2);
            $lineaExpedicion->create();
        }

        unset($articulo);
        unset($lineaExpedicion);
    }

    /**
     * Genera tantas líneas de expedición como lotes  sean necesarios
     * para servir la cantidad indicada en la línea de albarán
     *
     * @param AlbaranesLineas $lineaAlbaran
     * @param integer $idRepartidor
     */
    private function preasignaLotes($entidad, $idEntidad, $linea, $idRepartidor = '') {

        $idLineaEntidad = $linea->getPrimaryKeyValue();
        $articulo = $linea->getIDArticulo();
        $idAlmacen = $linea->getIDAlmacen()->getIDAlmacen();
        $unidades = $linea->getUnidades();

        $lotes = $articulo->getLotesDisponibles($idAlmacen);
        $acumulado = 0;

        if ($lotes) {
            // Hay stock. Tomo los lotes necesarios
            foreach ($lotes as $item) {
                $unidadesAlmacen = $articulo->convertUnit($linea->getUnidadMedida(), 'UMA', $unidades);

                $lineaExpedicion = new Expediciones();
                $lineaExpedicion->setEntidad($entidad);
                $lineaExpedicion->setIDEntidad($idEntidad);
                $lineaExpedicion->setIDLineaEntidad($idLineaEntidad);
                $lineaExpedicion->setIDAlmacen($idAlmacen);
                $lineaExpedicion->setIDAlmacenero($_SESSION['USER']['user']['id']);
                $lineaExpedicion->setIDRepartidor($idRepartidor);
                $lineaExpedicion->setIDArticulo($articulo->getIDArticulo());
                ($item['Reales'] > $unidadesAlmacen) ? $asignado = $unidadesAlmacen : $asignado = $item['Reales'];
                $asignado = $articulo->convertUnit('UMA', $linea->getUnidadMedida(), $asignado);
                $lineaExpedicion->setUnidades($asignado);
                $lineaExpedicion->setUnidadMedida($linea->getUnidadMedida());
                $lineaExpedicion->setIDLote($item['Id']);
                $lineaExpedicion->setIDUbicacion(0);
                $lineaExpedicion->setFlagTrazabilidad(1);
                $lineaExpedicion->setFlagUbicacion(0);
                $lineaExpedicion->setFlagSinStock(0);
                $lineaExpedicion->create();

                $acumulado = $acumulado + $asignado;
                if ($acumulado >= $unidades)
                    break;
                $unidades = $unidades - $asignado;
            }
        } else if ($articulo->getBloqueoStock()->getIDTipo() == '0') {
            // No hay stock, pero el artículo no bloquea stock; creo la linea de expedición a cero
            $lineaExpedicion = new Expediciones();
            $lineaExpedicion->setEntidad($entidad);
            $lineaExpedicion->setIDEntidad($idEntidad);
            $lineaExpedicion->setIDLineaEntidad($idLineaEntidad);
            $lineaExpedicion->setIDAlmacen($idAlmacen);
            $lineaExpedicion->setIDAlmacenero($_SESSION['USER']['user']['id']);
            $lineaExpedicion->setIDRepartidor($idRepartidor);
            $lineaExpedicion->setIDArticulo($articulo->getIDArticulo());
            $lineaExpedicion->setUnidades(0);
            $lineaExpedicion->setUnidadMedida($linea->getUnidadMedida());
            $lineaExpedicion->setIDLote(0);
            $lineaExpedicion->setIDUbicacion(0);
            $lineaExpedicion->setFlagTrazabilidad(1);
            $lineaExpedicion->setFlagUbicacion(0);
            $lineaExpedicion->setFlagSinStock(1);
            $lineaExpedicion->create();
        }

        unset($articulo);
        unset($lineaExpedicion);
    }

    /**
     * Preasigna stock proponiendo ubicaciones donde haya stock del artículo
     *
     * Si hay stock pero no es suficiente, se crea la línea de expedición proponiendo el que hay.
     *
     * Si NO hay stock:
     *
     *   Caso 1) El artículo bloquea stock: no se crea la línea de expedición
     *   Caso 2) El artículo NO bloquea stock: se crea la línea de expedición proponiendo 0 unidades
     * 
     * @param AlbaranesLineas $lineaAlbaran
     * @param <type> $idRepartidor
     */
    private function preasignaUbicaciones($entidad, $idEntidad, $linea, $idRepartidor = '') {

        $idLineaEntidad = $linea->getPrimaryKeyValue();
        $articulo = $linea->getIDArticulo();
        $idAlmacen = $linea->getIDAlmacen()->getIDAlmacen();
        $unidades = $linea->getUnidades();

        $ubicaciones = $articulo->getUbicaciones($idAlmacen, '', FALSE);
        $acumulado = 0;

        if ($ubicaciones) {
            // Hay stock. Tomo las ubicaciones necesarias
            foreach ($ubicaciones as $item) {
                $unidadesAlmacen = $articulo->convertUnit($linea->getUnidadMedida(), 'UMA', $unidades);

                $lineaExpedicion = new Expediciones();
                $lineaExpedicion->setEntidad($entidad);
                $lineaExpedicion->setIDEntidad($idEntidad);
                $lineaExpedicion->setIDLineaEntidad($idLineaEntidad);
                $lineaExpedicion->setIDAlmacen($idAlmacen);
                $lineaExpedicion->setIDAlmacenero($_SESSION['USER']['user']['id']);
                $lineaExpedicion->setIDRepartidor($idRepartidor);
                $lineaExpedicion->setIDArticulo($articulo->getIDArticulo());
                ($item['Reales'] > $unidadesAlmacen) ? $asignado = $unidadesAlmacen : $asignado = $item['Reales'];
                $asignado = $articulo->convertUnit('UMA', $linea->getUnidadMedida(), $asignado);
                $lineaExpedicion->setUnidades($asignado);
                $lineaExpedicion->setUnidadMedida($linea->getUnidadMedida());
                $lineaExpedicion->setIDLote(0);
                $lineaExpedicion->setIDUbicacion($item['Id']);
                $lineaExpedicion->setFlagTrazabilidad(0);
                $lineaExpedicion->setFlagUbicacion(1);
                $lineaExpedicion->setFlagSinStock(0);
                $lineaExpedicion->create();

                $acumulado = $acumulado + $asignado;
                if ($acumulado >= $unidades)
                    break;
                $unidades = $unidades - $asignado;
            }
        } else if ($articulo->getBloqueoStock()->getIDTipo() == '0') {
            // No hay stock, pero el artículo no bloquea stock; creo la linea de expedición a cero
            $lineaExpedicion = new Expediciones();
            $lineaExpedicion->setEntidad($entidad);
            $lineaExpedicion->setIDEntidad($idEntidad);
            $lineaExpedicion->setIDLineaEntidad($idLineaEntidad);
            $lineaExpedicion->setIDAlmacen($idAlmacen);
            $lineaExpedicion->setIDAlmacenero($_SESSION['USER']['user']['id']);
            $lineaExpedicion->setIDRepartidor($idRepartidor);
            $lineaExpedicion->setIDArticulo($articulo->getIDArticulo());
            $lineaExpedicion->setUnidades(0);
            $lineaExpedicion->setUnidadMedida($linea->getUnidadMedida());
            $lineaExpedicion->setIDLote(0);
            $lineaExpedicion->setIDUbicacion(0);
            $lineaExpedicion->setFlagTrazabilidad(0);
            $lineaExpedicion->setFlagUbicacion(1);
            $lineaExpedicion->setFlagSinStock(1);
            $lineaExpedicion->create();
        }

        unset($articulo);
        unset($lineaExpedicion);
    }

    /**
     * Preasigna stock sin control de trazabilidad ni de ubicación
     *
     * Si hay stock pero no es suficiente, se crea la línea de expedición proponiendo el que hay.
     *
     * Si NO hay stock:
     *
     *   Caso 1) El artículo bloquea stock: no se crea la línea de expedición
     *   Caso 2) El artículo NO bloquea stock: se crea la línea de expedición proponiendo 0 unidades
     * 
     * @param AlbaranesLineas $lineaAlbaran
     * @param <type> $idRepartidor
     */
    private function preasignaStock($entidad, $idEntidad, $linea, $idRepartidor = '') {

        $idLineaEntidad = $linea->getPrimaryKeyValue();
        $articulo = $linea->getIDArticulo();
        $idAlmacen = $linea->getIDAlmacen()->getIDAlmacen();
        $unidades = $linea->getUnidades();
        $unidadMedidaOrigen = $linea->getUnidadMedida();
        $unidadesAlmacen = $articulo->convertUnit($unidadMedidaOrigen, 'UMA', $unidades);
        $bloqueoStock = ( $articulo->getBloqueoStock()->getIDTipo() == '1');

        $exi = new Existencias();
        $existencias = $exi->getStock($articulo->getIDArticulo(), $idAlmacen);
        unset($exit);

        if ($existencias['RE'] >= $unidadesAlmacen) {
            $asignado = $articulo->convertUnit('UMA', $unidadMedidaOrigen, $unidadesAlmacen);
            $stockInsuficiente = false;
        } else {
            $stockInsuficiente = true;
            $asignado = $articulo->convertUnit('UMA', $unidadMedidaOrigen, $existencias['RE']);
        }

        if (($asignado <= 0) and (!$bloqueoStock))
            $asignado = 0;

        $lineaExpedicion = new Expediciones();
        $lineaExpedicion->setEntidad($entidad);
        $lineaExpedicion->setIDEntidad($idEntidad);
        $lineaExpedicion->setIDLineaEntidad($idLineaEntidad);
        $lineaExpedicion->setIDAlmacen($idAlmacen);
        $lineaExpedicion->setIDAlmacenero($_SESSION['USER']['user']['id']);
        $lineaExpedicion->setIDRepartidor($idRepartidor);
        $lineaExpedicion->setIDArticulo($articulo->getIDArticulo());
        if ($existencias['RE'] > 0) {
            ($existencias['RE'] > $unidadesAlmacen) ? $asignado = $unidadesAlmacen : $asignado = $existencias['RE'];
        } else {
            $asignado = 0;
        }
        $asignado = $articulo->convertUnit('UMA', $unidadMedidaOrigen, $asignado);
        $lineaExpedicion->setUnidades($asignado);
        $lineaExpedicion->setUnidadMedida($unidadMedidaOrigen);
        $lineaExpedicion->setIDLote(0);
        $lineaExpedicion->setIDUbicacion(0);
        $lineaExpedicion->setFlagTrazabilidad(0);
        $lineaExpedicion->setFlagUbicacion(0);
        $lineaExpedicion->setFlagSinStock($stockInsuficiente);
        $lineaExpedicion->create();
    }

}

?>