<?php

/**
 * Clase paa gestionar el DashBoard. Cuadro de mandos
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 12-10-2013
 *
 */
class DashBoard {

    /**
     * Devuelve un array con el dashboard de los presupuestos
     * 
     * Si el rol es super o admin devuelve todo, en caso contrario
     * filtra con el usuario en curso
     * 
     * @param integer $desdeDias Los días hacia atrás a tener en cuenta desde la fecha actual
     * @return array detalle,resumen
     */
    static function getPresupuestos($desdeDias = 365) {

        $idRol = $_SESSION['USER']['user']['IdRol'];

        if ($idRol != '0' and $idRol != '9')
            $filtro = "IDComercial='{$_SESSION['USER']['user']['id']}'";
        else
            $filtro = "1";

        $hoy = new Fecha();
        $desde = $hoy->sumaDias(-$desdeDias);
        $filtro .= " AND (Fecha>='{$desde}')";

        $psto = new PstoCab();
        $rows = $psto->cargaCondicion("DATE_FORMAT(Fecha,'%Y-%m') as Mes,IDEstado as Estado,count(IDPsto) as N,sum(TotalBases) as Importe", "{$filtro} GROUP BY DATE_FORMAT(Fecha,'%Y-%m'),IDEstado", "Fecha,IDEstado");
        $detalle = array();
        foreach ($rows as $row)
            $detalle[$row['Mes']][$row['Estado']] = array("N" => $row['N'], 'importe' => $row['Importe']);

        $rows = $psto->cargaCondicion("IDEstado as Estado,count(IDPsto) as N,sum(TotalBases) as Importe", "{$filtro} GROUP BY IDEstado", "IDEstado");
        $resumen = array();
        foreach ($rows as $row)
            $resumen[$row['Estado']] = array("N" => $row['N'], 'importe' => $row['Importe']);

        unset($psto);

        return array(
            'detalle' => $detalle,
            'resumen' => $resumen,
        );
    }

    static function getTesoreria($diasAtras = 180, $diasADelante = 180) {
        return array(
            'comprometido' => self::getTesoreriaComprometido($diasAtras, $diasADelante),
            'prevision' => self::getTesoreriaPrevision()
        );
    }

    /**
     * Devuelve un array con los recibos de clientes pendientes de cobro
     * y los recibos de proveedoes pendientes de pago en el periodo indicado
     * 
     * @param integer $diasAtras El número de días hacia atrás de la fecha actual
     * @param integer $diasADelante El número de días hacia a delante de la fecha actual
     * @return array detalle, resumen
     */
    static function getTesoreriaComprometido($diasAtras = 180, $diasADelante = 180) {

        $hoy = new Fecha();
        $desde = $hoy->sumaDias(-$diasAtras);
        $hasta = $hoy->sumaDias($diasADelante);

        $filtroFecha = "Vencimiento>='{$desde}' and Vencimiento<='{$hasta}'";
        $detalle = array();
        $resumen = array();

        $recClientes = new RecibosClientes();
        $rows = $recClientes->cargaCondicion("DATE_FORMAT(Vencimiento,'%Y-%m') as Mes,sum(Importe) as Importe", "IDEstado='0' AND ({$filtroFecha}) GROUP BY DATE_FORMAT(Vencimiento,'%Y-%m')", "Vencimiento ASC");
        foreach ($rows as $row) {
            $detalle[$row['Mes']]['cobros'] = $row['Importe'];
        }
        unset($recClientes);

        $recProveedores = new RecibosProveedores();
        $rows = $recProveedores->cargaCondicion("DATE_FORMAT(Vencimiento,'%Y-%m') as Mes,sum(Importe) as Importe", "IDEstado='0' AND ({$filtroFecha}) GROUP BY DATE_FORMAT(Vencimiento,'%Y-%m')", "Vencimiento ASC");
        foreach ($rows as $row) {
            $detalle[$row['Mes']]['pagos'] = $row['Importe'];
        }

        foreach ($detalle as $value) {
            $resumen['cobros'] += $value['cobros'];
            $resumen['pagos'] += $value['pagos'];
        }

        return array(
            'detalle' => $detalle,
            'resumen' => $resumen,
        );
    }

    /**
     * Devuelve array con la prevision de cobros y pagos en base a los
     * albaranes confirmados o expedidos sin facturas y a los pedidos de
     * compra confirmados o recepcionados sin facturar respectivamente.
     * 
     * @return array previstoCobro,previstoPago
     */
    static function getTesoreriaPrevision() {

        // Albaranes confirmados o expedidos sin facturar
        $alb = new AlbaranesCab();
        $rows = $alb->cargaCondicion("sum(TotalBases) as total", "(IDEstado='1' or IDEstado='2')");
        $previstoCobro = $rows[0]['total'];
        unset($alb);

        // Pedidos confirmados o recepcionados sin facturar
        $ped = new PedidosCab();
        $rows = $ped->cargaCondicion("sum(TotalBases) as total", "(IDEstado='1' or IDEstado='2')");
        $previstoPago = $rows[0]['total'];
        unset($ped);

        return array(
            'cobro' => $previstoCobro,
            'pago' => $previstoPago
        );
    }

    /**
     * Albaranes confirmados que están sin entregar.
     * 
     * Si el rol del usuario es comercial solo se muestran
     * los que ha generado dicho comercial
     * 
     * @return type
     */
    static function getPteServir() {
        $idRol = $_SESSION['USER']['user']['IdRol'];

        if ($idRol != '0' and $idRol != '9')
            $filtro = "a.IDComercial='{$_SESSION['USER']['user']['id']}'";
        else
            $filtro = "1";

        $alb = new AlbaranesCab();
        $tablaAlbaranes = $alb->getDataBaseName() . "." . $alb->getTableName();
        $cli = new Clientes();
        $tablaClientes = $cli->getDataBaseName() . "." . $cli->getTableName();

        $rows = array();

        $em = new EntityManager($alb->getConectionName());
        if ($em->getDbLink()) {
            $query = "select a.IDAlbaran,NumeroAlbaran,Fecha,RazonSocial,TotalBases from {$tablaAlbaranes} as a left join {$tablaClientes} as c on a.IDCliente=c.IDCliente where {$filtro} AND (IDEstado='1') order by Fecha ASC";
            $em->query($query);
            $rows = $em->fetchResult();
        }
        unset($em);
        unset($alb);
        unset($cli);

        return $rows;
    }

    /**
     * Pedidos confirmados con fecha prevista de entrega = hoy
     * 
     * @return type
     */
    static function getEntradasHoy() {

        $ped = new PedidosCab();
        $tablaPedidos = $ped->getDataBaseName() . "." . $ped->getTableName();
        $prov = new Proveedores();
        $tablaProveedores = $prov->getDataBaseName() . "." . $prov->getTableName();

        $rows = array();
        $hoy = date('Y-m-d');

        $em = new EntityManager($ped->getConectionName());
        if ($em->getDbLink()) {
            $query = "select IDPedido,Fecha,RazonSocial,TotalBases from {$tablaPedidos} as a left join {$tablaProveedores} as c on a.IDProveedor=c.IDProveedor where (IDEstado='1') and FechaEntrega='{$hoy}' order by Fecha ASC";
            $em->query($query);
            $rows = $em->fetchResult();
        }
        unset($em);
        unset($ped);
        unset($prov);

        return $rows;
    }

    /**
     * Pedidos que estando confirmados, su fecha prevista de
     * entrega es anterior al día actual.
     * 
     * @return type
     */
    static function getEntradasRetrasadas() {

        $ped = new PedidosCab();
        $tablaPedidos = $ped->getDataBaseName() . "." . $ped->getTableName();
        $prov = new Proveedores();
        $tablaProveedores = $prov->getDataBaseName() . "." . $prov->getTableName();

        $rows = array();
        $hoy = date('Y-m-d');

        $em = new EntityManager($ped->getConectionName());
        if ($em->getDbLink()) {
            $query = "select IDPedido,Fecha,RazonSocial,TotalBases from {$tablaPedidos} as a left join {$tablaProveedores} as c on a.IDProveedor=c.IDProveedor where (IDEstado='1') and FechaEntrega<'{$hoy}' order by Fecha ASC";
            $em->query($query);
            $rows = $em->fetchResult();
        }
        unset($em);
        unset($ped);
        unset($prov);

        return $rows;
    }

    /**
     * Devuelve los artículos que son inventariables y están bajo mínimos
     * 
     * @return type
     */
    static function getRoturasStock() {

        $arti = new Articulos();
        $tablaArticulos = $arti->getDataBaseName() . "." . $arti->getTableName();
        $exi = new Existencias();
        $tablaExistencias = $exi->getDataBaseName() . "." . $exi->getTableName();

        $em = new EntityManager($arti->getConectionName());
        if ($em->getDbLink()) {
            $query = "select a.Codigo,a.Descripcion,a.StockMinimo,sum(e.Reales) as sumaReales,sum(e.Reservadas) as sumaReservadas,sum(e.Entrando) as sumaEntrando,a.StockMinimo-sum(e.Reales-e.Reservadas+e.Entrando) as pedidoMinimo
                FROM {$tablaExistencias} as e left join {$tablaArticulos} as a on e.IDArticulo=a.IDArticulo
                WHERE (a.Inventario='1')
                GROUP BY e.IDArticulo
                HAVING ( (a.StockMinimo>0) and (sumaReales < a.StockMinimo) ) or (sumaReales<0)
                ORDER BY a.IDCategoria,a.Descripcion";
            $em->query($query);
            $rows = $em->fetchResult();
        }
        unset($em);
        unset($arti);
        unset($exi);

        return $rows;
    }

    static function getTopNClientes($n = 10, $diasAtras = 365) {

        $femi = new FemitidasCab();
        $tablaFacturas = $femi->getDataBaseName() . "." . $femi->getTableName();
        $cli = new Clientes();
        $tablaClientes = $cli->getDataBaseName() . "." . $cli->getTableName();

        $idRol = $_SESSION['USER']['user']['IdRol'];

        if ($idRol != '0' and $idRol != '9')
            $filtro = "(f.IDComercial='{$_SESSION['USER']['user']['id']}')";
        else
            $filtro = "(1)";

        $hoy = new Fecha();
        $desde = $hoy->sumaDias(-$diasAtras);

        $filtro .= " AND (Fecha>='{$desde}')";
        $em = new EntityManager($femi->getConectionName());
        if ($em->getDbLink()) {
            $query = "select RazonSocial,sum(TotalBases) as Total 
                from {$tablaFacturas} as f left join {$tablaClientes} as c on f.IDCliente=c.IDCliente
                where {$filtro}
                group by f.IDCliente
                order by sum(TotalBases) DESC
                limit 0,{$n}";
            $em->query($query);
            $rows = $em->fetchResult();
        }
        unset($em);
        unset($femi);
        unset($cli);
        return $rows;
    }

    static function getTopNArticulos($n = 10, $diasAtras = 365) {

        $femi = new FemitidasCab();
        $tablaFacturas = $femi->getDataBaseName() . "." . $femi->getTableName();
        $lineas = new FemitidasLineas();
        $tablaLineas = $lineas->getDataBaseName() . "." . $lineas->getTableName();
        $arti = new Articulos();
        $tablaArticulos = $arti->getDataBaseName() . "." . $arti->getTableName();

        $idRol = $_SESSION['USER']['user']['IdRol'];

        if ($idRol != '0' and $idRol != '9')
            $filtro = "(f.IDComercial='{$_SESSION['USER']['user']['id']}')";
        else
            $filtro = "(1)";

        $hoy = new Fecha();
        $desde = $hoy->sumaDias(-$diasAtras);

        $filtro .= " AND (f.Fecha>='{$desde}')";
        $em = new EntityManager($femi->getConectionName());
        if ($em->getDbLink()) {
            $query = "select a.Descripcion,sum(l.Importe) as Total 
                from {$tablaLineas} as l 
                    left join {$tablaArticulos} as a on l.IDArticulo=a.IDArticulo
                    left join {$tablaFacturas} as f on l.IDFactura=f.IDFactura
                where {$filtro}
                group by l.IDArticulo
                order by sum(l.Importe) DESC
                limit 0,{$n}";
            $em->query($query);
            $rows = $em->fetchResult();
        }

        unset($em);
        unset($femi);
        unset($lineas);
        unset($arti);
        return $rows;
    }

    static function getTopNCategorias($n = 10, $diasAtras = 365) {

        $femi = new FemitidasCab();
        $tablaFacturas = $femi->getDataBaseName() . "." . $femi->getTableName();
        $lineas = new FemitidasLineas();
        $tablaLineas = $lineas->getDataBaseName() . "." . $lineas->getTableName();
        $arti = new Articulos();
        $tablaArticulos = $arti->getDataBaseName() . "." . $arti->getTableName();
        $fami = new Familias();
        $tablaFamilias = $fami->getDataBaseName() . "." . $fami->getTableName();

        $idRol = $_SESSION['USER']['user']['IdRol'];

        if ($idRol != '0' and $idRol != '9')
            $filtro = "(f.IDComercial='{$_SESSION['USER']['user']['id']}')";
        else
            $filtro = "(1)";

        $hoy = new Fecha();
        $desde = $hoy->sumaDias(-$diasAtras);

        $filtro .= " AND (f.Fecha>='{$desde}')";
        $em = new EntityManager($femi->getConectionName());
        if ($em->getDbLink()) {
            $query = "select c.Familia,sum(l.Importe) as Total 
                from {$tablaLineas} as l 
                    left join {$tablaArticulos} as a on l.IDArticulo=a.IDArticulo
                    left join {$tablaFacturas} as f on l.IDFactura=f.IDFactura
                    left join {$tablaFamilias} as c on a.IDCategoria=c.IDFamilia
                where {$filtro}
                group by l.IDArticulo
                order by sum(l.Importe) DESC
                limit 0,{$n}";
            $em->query($query);
            $rows = $em->fetchResult();
        }

        unset($em);
        unset($femi);
        unset($lineas);
        unset($arti);
        return $rows;
    }

    static function getTopNFamilias($n = 10, $diasAtras = 365) {

        $femi = new FemitidasCab();
        $tablaFacturas = $femi->getDataBaseName() . "." . $femi->getTableName();
        $lineas = new FemitidasLineas();
        $tablaLineas = $lineas->getDataBaseName() . "." . $lineas->getTableName();
        $arti = new Articulos();
        $tablaArticulos = $arti->getDataBaseName() . "." . $arti->getTableName();
        $fami = new Familias();
        $tablaFamilias = $fami->getDataBaseName() . "." . $fami->getTableName();

        $idRol = $_SESSION['USER']['user']['IdRol'];

        if ($idRol != '0' and $idRol != '9')
            $filtro = "(f.IDComercial='{$_SESSION['USER']['user']['id']}')";
        else
            $filtro = "(1)";

        $hoy = new Fecha();
        $desde = $hoy->sumaDias(-$diasAtras);

        $filtro .= " AND (f.Fecha>='{$desde}')";
        $em = new EntityManager($femi->getConectionName());
        if ($em->getDbLink()) {
            $query = "select c.Familia,sum(l.Importe) as Total 
                from {$tablaLineas} as l 
                    left join {$tablaArticulos} as a on l.IDArticulo=a.IDArticulo
                    left join {$tablaFacturas} as f on l.IDFactura=f.IDFactura
                    left join {$tablaFamilias} as c on a.IDFamilia=c.IDFamilia
                where {$filtro}
                group by c.IDFamilia
                order by sum(l.Importe) DESC
                limit 0,{$n}";
            $em->query($query);
            $rows = $em->fetchResult();
        }

        unset($em);
        unset($femi);
        unset($lineas);
        unset($arti);
        return $rows;
    }

    static function getTopNComerciales($n = 10, $diasAtras = 365) {

        $femi = new FemitidasCab();
        $tablaFacturas = $femi->getDataBaseName() . "." . $femi->getTableName();

        $idRol = $_SESSION['USER']['user']['IdRol'];

        if ($idRol != '0' and $idRol != '9')
            $filtro = "(IDComercial='{$_SESSION['USER']['user']['id']}')";
        else
            $filtro = "(1)";

        $hoy = new Fecha();
        $desde = $hoy->sumaDias(-$diasAtras);

        $filtro .= " AND (Fecha>='{$desde}')";
        $em = new EntityManager($femi->getConectionName());
        if ($em->getDbLink()) {
            $query = "select IDComercial,sum(TotalBases) as Total 
                from {$tablaFacturas}
                where {$filtro}
                group by IDComercial
                order by sum(TotalBases) DESC
                limit 0,{$n}";
            $em->query($query);
            $rows = $em->fetchResult();
        }

        foreach ($rows as $key=>$row) {
            $comercial = new Agentes($row['IDComercial']);
            $rows[$key]['Nombre'] = $comercial->getNombreApellidos();
        }

        unset($comercial);
        unset($em);
        unset($femi);

        return $rows;
    }

}

?>
