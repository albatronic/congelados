<?php

/**
 * CONTROLADOR MULTIPROPOSITO PARA REDENRIZAR INFORMACION EN PANTALLAS EMERGENTES
 *
 * LA INFORMACION QUE SE MOSTRARA DEPENDE DEL METODO LLAMADO
 *
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 27.07.2011 11:10:13

 */
class _EmergenteController {

    protected $request;
    protected $values;
    protected $periodos = array(
        array('Id' => '0', 'Value' => 'Último Mes'),
        array('Id' => '1', 'Value' => 'Último Trimestre'),
        array('Id' => '2', 'Value' => 'Último Año'),
        array('Id' => '3', 'Value' => 'Todo'),
    );

    public function __construct($request) {
        // Cargar lo que viene en el request
        $this->request = $request;
        $this->values['request'] = $this->request;
    }

    /**
     * Muestra las líneas de albarán que están reservadas para el almacén y artículo indicado
     * @return array Con el template y values
     */
    public function reservasAction() {
        switch ($this->request["METHOD"]) {
            case 'GET':
                $idArticulo = $this->request['2'];
                $idAlmacen = $this->request['3'];
                $condicion = " JOIN albaranes_cab USING(IDAlbaran) where t1.IDAlmacen='{$idAlmacen}' and t1.IDArticulo='{$idArticulo}' and t1.IDEstado='1' ";
                $query = "select IDLinea from albaranes_lineas as t1 {$condicion} order by albaranes_cab.Fecha ASC";
                $em = new EntityManager("datos" . $_SESSION['emp']);
                $em->query($query);
                $rows = $em->fetchResult();
                $em->desConecta();
                unset($em);

                foreach ($rows as $key => $value) {
                    $data[] = new AlbaranesLineas($value['IDLinea']);
                }
                $this->values['data'] = $data;
                unset($data);
                break;

            case 'POST':
                break;
        }

        return array('template' => '_Emergente/reservasAlbaranes.html.twig', 'values' => $this->values);
    }

    /**
     * Muestra las líneas de pedido que están pendiente de recepción para el almacén y artículo indicado
     * @return array Con el template y values
     */
    public function entradasAction() {
        switch ($this->request["METHOD"]) {
            case 'GET':
                $idArticulo = $this->request['2'];
                $idAlmacen = $this->request['3'];
                $condicion = " JOIN pedidos_cab USING(IDPedido) where t1.IDAlmacen='{$idAlmacen}' and t1.IDArticulo='{$idArticulo}' and t1.IDEstado='1' ";
                $query = "select IDLinea from pedidos_lineas as t1 {$condicion} order by pedidos_cab.FechaEntrega ASC";
                $em = new EntityManager("datos" . $_SESSION['emp']);
                $em->query($query);
                $rows = $em->fetchResult();
                $em->desConecta();
                unset($em);

                foreach ($rows as $key => $value) {
                    $data[] = new PedidosLineas($value['IDLinea']);
                }
                $this->values['data'] = $data;
                unset($data);
                break;

            case 'POST':
                break;
        }

        return array('template' => '_Emergente/entradasPedidos.html.twig', 'values' => $this->values);
    }

    /**
     * Devuelve array con los posibles formatos de documentos para
     * el tipo de documento indicado como parámetro o en la posicion 2 del request
     *
     * El controlador que generará el documento viene en la posicion 3 del request
     * El id del objeto a imprimir viene en la posicion 4 del request
     *
     * Los formatos están definidos en  docs/docsXXX/tipoDocumento.yml
     * Se mostrarán solo aquellos que el perfil del usuario tenga acceso.
     *
     * En el nodo <idPerfil> se indican los IDs (separados por comas) de los perfiles que tendrán acceso
     * al documento. Si el nodo está vacio, se entiende que pueden acceder todos.
     *
     * @param string Tipo de Documento
     * @return array Con el template y values
     */
    public function formatosDocumentosAction($controlador = '') {

        switch ($this->request["METHOD"]) {
            case 'GET':
                if ($controlador == '')
                    $controlador = $this->request['2'];

                $idDocumento = $this->request['3'];

                $documento = new DocumentoPdf();
                $formatos = $documento->getFormatos($controlador);
                unset($documento);

                $this->values = array(
                    'numeroDeFormatos' => count($formatos),
                    'tipoDocumento' => $controlador,
                    'formatos' => $formatos,
                    'controlador' => $controlador,
                    'idDocumento' => $idDocumento,
                );
                break;

            case 'POST':
                break;
        }
        return array('template' => '_Emergente/formatosDocumentos.html.twig', 'values' => $this->values);
    }

    /**
     * Renderiza el template _Emergente/historicoCompras.html.twig
     * mostrando las compras realizadas a un proveedor de un articulo y un periodo dado.
     *
     * La información se obtiene en base a los pedidos RECEPCIONADOS o FACTURADOS.
     *
     * No se tienen en cuenta los pedidos no confirmados.
     *
     * Puede entrar por POST o por GET.
     *
     * Si es por POST los parametros vienen en:
     *
     *  idArticulo, idProveedor, periodo
     *
     * Si es por GET los parametros vienen en:
     *
     *  posicion 2 (idArticulo), 3 (idProveedor), 4 (periodo)
     *
     * @return array El template y los datos
     */
    public function HistoricoComprasAction() {

        switch ($this->request["METHOD"]) {
            case 'GET':
                $idArticulo = $this->request['2'];
                if ($idArticulo == '0') {
                    $idArticulo = '';
                }
                $idProveedor = $this->request['3'];
                if ($idProveedor == '0') {
                    $idProveedor = '';
                }
                $periodo = $this->request['4'];
                break;
            case 'POST':
                $idArticulo = $this->request['idArticulo'];
                if ($idArticulo == '0') {
                    $idArticulo = '';
                }
                $idProveedor = $this->request['idProveedor'];
                if ($idProveedor == '0') {
                    $idProveedor = '';
                }
                $periodo = $this->request['periodo'];
                break;
        }

        // Construir la parte del filtro que depende del periodo
        switch ($periodo) {
            case '' :
                $periodo = 0;
            case '0': // Ultimo mes
                $diferenciaDias = -30;
                break;
            case '1': // Ultimo Trimestre
                $diferenciaDias = -90;
                break;
            case '2': // Ultimo año
                $diferenciaDias = -365;
                break;
            case '3': // Todo, 20 años hacia atrás
                $diferenciaDias = -7300;
                break;
        }

        $fecha = new Fecha(date('Y-m-d'));
        $desdeFecha = $fecha->sumaDias($diferenciaDias);
        unset($fecha);

        $proveedor = new Proveedores($idProveedor);

        // Calcular el total de unidades compradas y el precio medio de compra
        // Solo calcula los pedidos que están recepcionados o facturados
        if ($idArticulo != '' or $idProveedor != '') {
            $articulo = new Articulos($idArticulo);

            $em = new EntityManager("datos" . $_SESSION['emp']);
            if ($em->getDbLink()) {
                $query = "SELECT SUM(t1.UnidadesRecibidas) as Unidades, SUM(t1.Importe) as Importe
                FROM pedidos_lineas as t1, pedidos_cab as t2";
                $query .= " WHERE t1.IDPedido=t2.IDPedido AND t2.IDEstado>'2' AND t2.FechaEntrada>='{$desdeFecha}'";
                if ($idProveedor !== '') {
                    $query .= " AND  t2.IDProveedor='{$idProveedor}'";
                }
                if ($idArticulo !== '') {
                    $query .= " AND t1.IDArticulo='{$idArticulo}'";
                }
                $em->query($query); //echo $query,"<br/>";
                $rows = $em->fetchResult();
                $em->desConecta();
            }

            ($rows[0]['Unidades'] != 0) ? $precioMedio = $rows[0]['Importe'] / $rows[0]['Unidades'] : $precioMedio = 0;
            ($rows[0]['Unidades'] == '') ? $unidades = 0 : $unidades = $rows['0']['Unidades'];
        }

        $this->values['datos'] = array(
            'idsucursal' => $_SESSION['suc'],
            'articulo' => $articulo,
            'proveedor' => $proveedor,
            'unidades' => $unidades,
            'periodo' => $periodo,
            'periodos' => $this->periodos,
            'precioMedio' => number_format($precioMedio, 3),
        );

        // Obtener el litado historico de compras para el articulo y proveedor
        // Solo muestra los pedidos que están confirmador o facturados
        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "SELECT t2.IDLinea,t1.IDPedido,DATE_FORMAT(t1.FechaEntrada,'%d-%m-%Y') as FechaEntrada,t4.Descripcion,t2.UnidadesRecibidas,t2.Precio,t2.Descuento,t2.Importe
                FROM pedidos_cab as t1, pedidos_lineas as t2";
            if ($idProveedor !== '') {
                $query .= ", proveedores as t3";
            }
            if ($idArticulo !== '') {
                $query .= ", articulos as t4";
            }
            $query .= " WHERE t1.IDPedido=t2.IDPedido";
            if ($idProveedor !== '') {
                $query .= " AND t1.IDProveedor=t3.IDProveedor AND t1.IDProveedor='{$idProveedor}' ";
            }
            if ($idArticulo !== '') {
                $query .= " AND t2.IDArticulo=t4.IDArticulo AND t2.IDArticulo='{$idArticulo}'";
            }

            $query .= "
                AND t1.IDEstado>='2'
                AND t1.FechaEntrada>='{$desdeFecha}'
                ORDER BY t1.FechaEntrada DESC, t1.IDPedido DESC";

            $em->query($query); //echo $query,"<br/>";
            $rows = $em->fetchResult();
            $em->desConecta();
        }

        $this->values['listado'] = $rows;

        unset($em);
        unset($articulo);
        unset($proveedor);

        return array('template' => '_Emergente/historicoCompras.html.twig', 'values' => $this->values);
    }

    /**
     * Renderiza el template _Emergente/historicoVentas.html.twig
     * mostrando las ventas realizadas a un cliente de un articulo dado en un periodo.
     *
     * La información se obtiene en base a los albaranes confimardos o facturados.
     * No se tienen en cuenta los albaranes no confirmados.
     *
     * Puede entrar por POST o por GET.
     *
     * Si es por POST los parametros vienen en:
     *
     *  idArticulo, idCliente, periodo
     *
     * Si es por GET los parametros vienen en:
     *
     *  posicion 2 (idArticulo), 3 (idCliente), 4 (periodo)
     *
     * @return array El template y los datos
     */
    public function HistoricoVentasAction() {

        switch ($this->request["METHOD"]) {
            case 'GET':
                $idArticulo = $this->request['2'];
                if ($idArticulo == '0') {
                    $idArticulo = '';
                }
                $idCliente = $this->request['3'];
                if ($idCliente == '0') {
                    $idCliente = '';
                }
                $periodo = $this->request['4'];
                break;
            case 'POST':
                $idArticulo = $this->request['idArticulo'];
                if ($idArticulo == '0') {
                    $idArticulo = '';
                }
                $idCliente = $this->request['idCliente'];
                if ($idCliente == '0') {
                    $idCliente = '';
                }
                $periodo = $this->request['periodo'];
                break;
        }

        // Construir la parte del filtro que depende del periodo
        switch ($periodo) {
            case '' :
                $periodo = 0;
            case '0': // Ultimo mes
                $diferenciaDias = -30;
                break;
            case '1': // Ultimo Trimestre
                $diferenciaDias = -90;
                break;
            case '2': // Ultimo año
                $diferenciaDias = -365;
                break;
            case '3': // Todo, 20 años hacia atrás
                $diferenciaDias = -7300;
                break;
        }

        $fecha = new Fecha(date('Y-m-d'));
        $desdeFecha = $fecha->sumaDias($diferenciaDias);
        unset($fecha);

        $cliente = new Clientes($idCliente);
        $clienteTabla = $cliente->getDataBaseName() . "." . $cliente->getTableName();
        $articulo = new Articulos($idArticulo);
        $articuloTabla = $articulo->getDataBaseName() . "." . $articulo->getTableName();
        $albaran = new AlbaranesCab();
        $albaranTabla = $albaran->getDataBaseName() . "." . $albaran->getTableName();
        unset($albaran);
        $lineas = new AlbaranesLineas();
        $lineasTabla = $lineas->getDataBaseName() . "." . $lineas->getTableName();
        unset($lineas);

        // Calcular el total de unidades vendidas y el precio medio de venta
        // No tiene en cuenta los albaranes que no están confirmados
        if ($idArticulo != '' or $idCliente != '') {

            $em = new EntityManager($articulo->getConectionName());
            if ($em->getDbLink()) {
                $query = "SELECT SUM(t1.Unidades) as Unidades, SUM(t1.Importe) as Importe
                FROM {$lineasTabla} as t1, {$albaranTabla} as t2";
                $query .= " WHERE t1.IDAlbaran=t2.IDAlbaran AND t2.IDEstado<>'0' AND t2.Fecha>='{$desdeFecha}'";
                if ($idCliente !== '') {
                    $query .= " AND  t2.IDCliente='{$idCliente}'";
                }
                if ($idArticulo !== '') {
                    $query .= " AND t1.IDArticulo='{$idArticulo}'";
                }
                $em->query($query); //echo $query;
                $rows = $em->fetchResult();
                $em->desConecta();
            }

            $precioMedio = ($rows[0]['Unidades'] != 0) ? $rows[0]['Importe'] / $rows[0]['Unidades'] : 0;
            $unidades = ($rows[0]['Unidades'] == '') ? 0 : $rows['0']['Unidades'];
        }

        $this->values['datos'] = array(
            'idsucursal' => $_SESSION['suc'],
            'articulo' => $articulo,
            'cliente' => $cliente,
            'unidades' => $unidades,
            'periodo' => $periodo,
            'periodos' => $this->periodos,
            'precioMedio' => number_format($precioMedio, 3),
        );

        // Obtener el litado histórico de ventas para el articulo y cliente
        // Solo muestra los albaranes que están confirmador o facturados
        $em = new EntityManager($articulo->getConectionName());
        if ($em->getDbLink()) {
            $query = "SELECT t2.IDLinea,t1.IDAlbaran,t1.NumeroAlbaran,DATE_FORMAT(t1.Fecha,'%d-%m-%Y') as Fecha,t1.IDEstado,t1.IDFactura,t2.Descripcion,t2.Unidades,t2.Precio,t2.Descuento,t2.Importe,t2.IDPromocion
                FROM {$albaranTabla} as t1, {$lineasTabla} as t2";
            if ($idCliente !== '') {
                $query .= ", {$clienteTabla} as t3";
            }
            if ($idArticulo !== '') {
                $query .= ", {$articuloTabla} as t4";
            }
            $query .= " WHERE t1.IDAlbaran=t2.IDAlbaran";
            if ($idCliente !== '') {
                $query .= " AND t1.IDCliente=t3.IDCliente AND t1.IDCliente='{$idCliente}' ";
            }

            if ($idArticulo !== '') {
                $query .= " AND t2.IDArticulo=t4.IDArticulo AND t2.IDArticulo='{$idArticulo}'";
            }

            $query .= "
                AND t1.IDEstado<>'0'
                AND t1.Fecha>='{$desdeFecha}'
                ORDER BY t1.Fecha DESC, t1.IDAlbaran DESC";

            $em->query($query);
            //echo $query;
            $rows = $em->fetchResult();
            $em->desConecta();
        }

        // Recorro el array de resultados y convierto (si procede) la columna IDPromocion
        // en un objeto promocion para tener todos los datos de la promocion en el template.
        foreach ($rows as $key => $value) {
            $rows[$key]['Estado'] = new EstadosAlbaranes($value['IDEstado']);
            if ($value['IDFactura'])
                $rows[$key]['Factura'] = new FemitidasCab($value['IDFactura']);
            if ($value['IDPromocion'])
                $rows[$key]['IDPromocion'] = new Promociones($value['IDPromocion']);
        }

        $this->values['listado'] = $rows;

        unset($em);
        unset($articulo);
        unset($cliente);

        return array('template' => '_Emergente/historicoVentas.html.twig', 'values' => $this->values);
    }

}

?>