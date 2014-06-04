<?php

/**
 * Description of Proveedores
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Proveedores extends ProveedoresEntity {

    public function __toString() {
        if ($this->RazonSocial)
            return $this->getRazonSocial();
        else
            return "";
    }

    /*
     * Devuelve la cuenta corriente con 20 dígitos
     *
     * @return string La cuenta corriente
     */

    public function getCtaCorriente() {
        return $this->IDBanco . $this->IDOficina . $this->Digito . $this->Cuenta;
    }

    /**
     * Calcula el número de recibos y el importe total de los
     * recibos del proveedor indicado que están PENDIENTES DE COBRO (IDEstado<>6)
     *
     * Devuelve el array (
     *      'Recibos'   =>  Numero de recibos,
     *      'Importe'   =>  Suma del importe de todos los recibos
     * )
     *
     * @return Array $ptePago Array con lo pendiente de pago
     */
    public function getPtePago() {
        $ptePago = array();

        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "select count(IDRecibo) as Recibos, sum(Importe) as Importe from {$this->_dataBaseName}.recibos_proveedores where IDProveedor='{$this->IDProveedor}' and IDEstado<>'6'";
            if ($this->_em->query($query)) {
                $rows = $this->_em->fetchResult();
                $ptePago = $rows[0];
            } else {
                $this->_errores[] = $this->_em->getError();
            }
            $this->_em->desConecta();
        } else {
            $this->_errores[] = $this->_em->getError();
        }

        return $ptePago;
    }

    /**
     * Devuelve un array de objetos recibos del proveedor
     * que están en el estado $idEstado. Por defecto todos
     *
     * La ordenación es descendente repecto a la fecha de Vencimiento
     *
     * @param integer $idEstado El estado de los recibos (opcional)
     * @return array Objetos recibos de la factura
     */
    public function getRecibos($idEstado = '') {
        $recibos = array();

        $filtro = "IDProveedor='{$this->IDProveedor}'";
        if ($idEstado != '')
            $filtro .= " AND IDEstado='{$idEstado}'";

        $recibo = new RecibosProveedores();
        $rows = $recibo->cargaCondicion("IDRecibo", $filtro, "Vencimiento DESC");
        foreach ($rows as $row) {
            $recibos[] = new RecibosProveedores($row['IDRecibo']);
        }
        unset($recibo);

        return $recibos;
    }

    /**
     * Devuelve un array con los proveedores que tienen pedidos pendientes
     * de facturar (IDEstado=2) en el periodo de fechas indicado y de la
     * sucursal indicada.
     *
     * El array tiene tres columnas:
     *       Id (el id del proveedor),
     *       Value (la razon social del proveedor)
     *       Total (la suma de los totales de todos sus pedidos pendientes de facturar)
     *
     * @param integer $idSucursal
     * @param date $desdeFecha Fecha en formato dd/mm/aaaa
     * @param date $hastaFecha Fecha en formato dd/mm/aaaa
     * @return array Array con los proveedores
     */
    public function fetchConPendienteDeFacturar($idSucursal, $desdeFecha, $hastaFecha) {

        $fecha = new Fecha($desdeFecha);
        $desdeFecha = $fecha->getaaaammdd();
        $fecha = new Fecha($hastaFecha);
        $hastaFecha = $fecha->getaaaammdd();
        unset($fecha);

        $this->conecta();

        $rows = array();

        if (is_resource($this->_dbLink)) {
            if ($idSucursal == '')
                $filtroSucursal = "(1)";
            else
                $filtroSucursal = "(a.IDSucursal='{$idSucursal}')";

            $filtro = $filtroSucursal . " and
                      (a.FechaEntrega>='{$desdeFecha}') and
                      (a.FechaEntrega<='{$hastaFecha}') and
                      (a.IDEstado=2) and
                      (c.IDProveedor=a.IDProveedor)";

            $query = "SELECT distinct c.IDProveedor as Id, c.RazonSocial as Value, sum(a.Total) as Total
                        FROM `{$this->_dataBaseName}`.`proveedores` c, `{$this->_dataBaseName}`.`pedidos_cab` a
                        where ( {$filtro} )
                        GROUP BY c.IDProveedor
                        ORDER BY c.RazonSocial";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        return $rows;
    }

}

?>
