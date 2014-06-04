<?php

/**
 * Description of ConformarFacturasController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 10-junio-2012
 *
 */
class ConformarFacturasController extends Controller {

    protected $entity = "ConformarFacturas";
    protected $parentEntity = "";

    public function __construct($request) {

        $usuario = new Agentes($_SESSION['USER']['user']['id']);
        $this->values['sucursales'] = $usuario->getSucursales();

        $this->values['filtro'] = $this->request['filtro'];
        if ($this->values['filtro']['desdeFecha'] == '')
            $this->values['filtro']['desdeFecha'] = '01/01/' . date('Y');
        if ($this->values['filtro']['hastaFecha'] == '')
            $this->values['filtro']['hastaFecha'] = '31/12/' . date('Y');

        parent::__construct($request);
    }

    public function listAction() {

        $filtro = $this->request['filtro'];

        $totales = array('pedidosSeleccionados' => 0, 'facturable' => 0, 'seleccion' => 0);

        $pedido = new PedidosCab();
        $rows = $pedido->getPendientesFacturar($filtro['idSucursal'], $filtro['idProveedor'], $filtro['desdeFecha'], $filtro['hastaFecha']);
        unset($pedido);
        foreach ($rows as $row) {
            $pedido = new PedidosCab($row['IDPedido']);
            $datos[] = $pedido;
            $totales['facturable'] += $pedido->getTotal();
            if ($pedido->getFlagFacturar()->getIDTipo()) {
                $totales['pedidosSeleccionados']++;
                $totales['seleccion'] += $pedido->getTotal();
            }
            unset($pedido);
        }

        $this->values['filtro'] = $this->request['filtro'];
        $this->values['listado'] = $datos;
        $this->values['totales'] = $totales;
        $this->values['proveedor'] = new Proveedores($filtro['idProveedor']);
        $this->values['contador'] = new Contadores();
        $template = "ConformarFacturas/list.html.twig";

        return array('template' => $template, 'values' => $this->values);
    }

    /**
     * Cambia la forma de pago el flag de facturar del albaran
     * a peticion del usuario
     *
     * @return <type>
     */
    public function cambiaAction() {

        if ($this->request['sino'] == 'on')
            $estado = '1'; else
            $estado = '0';

        $pedido = new PedidosCab($this->request['idPedido']);
        $pedido->setFlagFacturar($estado);
        $pedido->setIDFP($this->request['idFormaPago']);
        $pedido->save();
        unset($pedido);

        return $this->listAction();
    }

    /**
     * Proceso de facturación agrupada.
     *
     * Se pueden agrupar pedidos de distintas sucursales, en cuyo caso
     * la factura generada se asignará a la sucursal en curso.
     *
     * Se agrupan las facturas en base a la forma de pago.
     *
     * @return <type>
     */
    public function ConformarAction() {

        $facturados = array();

        $filtro = $this->request['filtro'];

        $fecha = new Fecha($filtro['desdeFecha']);
        $desdeFecha = $fecha->getaaaammdd();
        $fecha = new Fecha($filtro['hastaFecha']);
        $hastaFecha = $fecha->getaaaammdd();
        unset($fecha);

        if ($filtro['idSucursal'] == '') {
            $filterSucursal = "(1)";
            $idSucursal = $_SESSION['suc'];
        } else {
            $filterSucursal = "(IDSucursal='{$filtro['idSucursal']}')";
            $idSucursal = $filtro['idSucursal'];
        }
        $filter = $filterSucursal . " AND c.IDFactura='0' and c.IDEstado='2' and c.IDProveedor='{$filtro['idProveedor']}' and c.Fecha>='{$desdeFecha}' and c.Fecha<'{$hastaFecha}' and c.FlagFacturar='1'";

        //COMPRUEBO QUE NO HAYA MAS DE TRES TIPOS DE IVA ENTRE TODOS LOS ALBARANES A FACTURAR
        $em = new EntityManager("datos" . $_SESSION['emp']);
        if (!$em->getDbLink()) {
            $this->values['errores'] = $em->getError();
            return $this->listAction();
        }

        $query = "SELECT l.Iva
                    FROM
                        {$em->getDataBase()}.pedidos_lineas as l,
                        {$em->getDataBase()}.pedidos_cab as c
                    WHERE {$filter} and c.IDPedido=l.IDPedido
                    GROUP BY l.Iva";
        $em->query($query);
        $rows = $em->fetchResult();
        $em->desConecta();
        if (count($rows) > 3) {
            $this->values['alertas'] = "Hay más de tres tipos de iva distintos. No se puede agrupar";
            return $this->listAction();
        }

        $contador = new Contadores($this->request['idContador']);

        // Buscar la cuenta contable de compras para la sucursal
        $sucursal = new Sucursales($idSucursal);
        $ctaCompras = $sucursal->getCtaContableCompras();
        unset($sucursal);

        $query = "select c.IDFP, sum(c.Total) as Total from pedidos_cab c where $filter GROUP BY c.IDFP;";

        //AGRUPO LOS PEDIDOS POR FORMA DE PAGO.
        $em = new EntityManager("datos" . $_SESSION['emp']);
        $em->query($query);
        $rows = $em->fetchResult();
        $em->desConecta();
        foreach ($rows as $row) {
            $numeroFactura = $contador->asignaContador();
            $factura = new FrecibidasCab();
            $factura->setIDSucursal($idSucursal);
            $factura->setIDContador($this->request['idContador']);
            $factura->setNumeroFactura($numeroFactura);
            $factura->setSuFactura($this->request['suFactura']);
            $factura->setFecha($this->request['fecha']);
            $factura->setIDProveedor($filtro['idProveedor']);
            $factura->setCuentaCompras($ctaCompras);
            $factura->setClave(md5($numeroFactura));
            $factura->setIDFP($row['IDFP']);

            $idFactura = $factura->create();
            // Guardo en un array los id's de facturas generadas
            $facturados[] = $idFactura;

            if ($idFactura != 0) {
                // Crear las líneas de factura
                $em = new EntityManager("datos" . $_SESSION['emp']);
                $query = "select l.* from pedidos_lineas l, pedidos_cab c where (c.IDPedido=l.IDPedido) and (c.IDFP='{$row['IDFP']}') and {$filter}";
                $em->query($query);
                $lineas = $em->fetchResult();
                $em->desConecta();

                foreach ($lineas as $linea) {
                    $linFactura = new FrecibidasLineas();
                    $linFactura->setIDFactura($idFactura);
                    $linFactura->setIDArticulo($linea['IDArticulo']);
                    $linFactura->setDescripcion($linea['Descripcion']);
                    $linFactura->setUnidades($linea['UnidadesPtesFacturar']);
                    $linFactura->setPrecio($linea['Precio']);
                    $linFactura->setDescuento($linea['Descuento']);
                    $linFactura->setIva($linea['Iva']);
                    $linFactura->setIDPedido($linea['IDPedido']);
                    $linFactura->setIDLineaPedido($linea['IDLinea']);

                    if ($linFactura->create()) {
                        // Pongo el estado de la linea de pedido a "Facturado" y
                        // quitar el pendiente de facturar.
                        $lineaPedido = new PedidosLineas($linea['IDLinea']);
                        $lineaPedido->setIDEstado(3);
                        $lineaPedido->setUnidadesPtesFacturar(0);
                        $lineaPedido->save();
                    }
                    unset($linFactura);
                }

                // Totalizar la factura
                $factura->recalcula();

                // Crear vencimientos
                $factura->creaVctos();

                // Anotar en caja si procede
                $factura->anotaEnCaja();

                // Actualiza las cabecera del grupo de pedidos
                $em = new EntityManager("datos" . $_SESSION['emp']);
                $query = "update pedidos_cab c set c.IDFactura='{$idFactura}', c.IDEstado='3' where (c.IDFP='{$row['IDFP']}') and ({$filter})";
                $em->query($query);
                $em->desConecta();

                unset($factura);
            } else {
                $this->values['errores'] = $factura->getErrores();
            }
        }

        return $this->listAction();
    }

}

?>
