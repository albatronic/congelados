<?php

/**
 * Description of Clientes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Clientes extends ClientesEntity {

    public function __toString() {
        if ($this->RazonSocial) {
            return $this->getRazonSocial() . " - " . $this->getNombreComercial();
        } else {
            return "";
        }
    }

    /**
     * Carga de datos en las variables de la clase
     */
    protected function load() {
        $this->IDSucursal = $_SESSION['suc'];
        parent::load();
    }

    /**
     * Crea un registro (insert)
     */
    public function create() {

        $lastId = parent::create();

        if ($lastId != NULL) {
            $this->setIDCliente($lastId);

            //ACTUALIZAR LA CUENTA CONTABLE EN BASE AL PREFIJO PARA LA SUCURSAL
            //Y AL NUMERO DE DIGITOS PARA LA EMPRESA
            $cContable = '';

            $this->conecta();

            // localiza la cta. contable mayor para la sucursal
            if (is_resource($this->_dbLink)) {
                $query = "SELECT `CContable` FROM `{$this->_dataBaseName}`.`{$this->_tableName}` WHERE IDSucursal='{$this->IDSucursal}' ORDER BY `CContable` DESC Limit 1;";
                $this->_em->query($query);
                $row = $this->_em->fetchResult();
                $this->_em->desConecta();
                unset($this->_em);
                $cContable = (int) $row[0]['CContable'];
            }

            $idSucursal = $this->IDSucursal;

            if ($cContable > 0) {
                $cContable += 1;
                $this->setCContable($cContable);
            } else {
                $prefijo = $this->getIDSucursal()->getCtaContableClientes();
                $empresa = new Empresas($_SESSION['emp']);
                $digitos = $empresa->getDigitosCuentaContable();
                unset($empresa);
                $this->setCContable(str_pad($prefijo, $digitos - 1, '0') . '1');
            }

            //PONER EL MISMO NOMBRE COMERCIAL SI ES QUE ESTE ESTÁ VACIO
            if ($this->getNombreComercial() == '')
                $this->setNombreComercial($this->getRazonSocial());
            $this->setIDSucursal($idSucursal);
            $this->save();

            //CREAR LA DIRECCION DE ENTREGA POR DEFECTO
            $this->createDireccionEntrega();
        }

        return $lastId;
    }

    /**
     * Si el cliente tiene recibos pendientes de cobro, no se puede descatalogar
     * Si se descataloga el cliente, se borra de las rutas de reparto y de venta
     */
    public function validaLogico() {
        if ($this->Vigente == 0) {
            $recibosPtes = $this->getPteCobro();
            if ($recibosPtes['Recibos'] > 0) {
                $this->_errores[] = "El cliente tiene {$recibosPtes['Recibos']} recibos pendientes de cobro por importe de {$recibosPtes['Importe']}. No se puede descatalogar";
                $this->Vigente = 1;
            }
            // Al descatalogar se borra de las rutas de venta y de reparto
            // Borrar de las rutas de venta
            $rutaVenta = new RutasVentas();
            $rutaVenta->queryDelete("IDCliente='{$this->IDCliente}'");
            unset($rutaVenta);

            // Borrar de las rutas de reparto
            $condicion = "IDDirec in (select d.IDDirec from clientes_dentrega d left join clientes c on d.IDCliente=c.IDCliente where c.IDCliente='{$this->IDCliente}')";
            $rutaReparto = new RutasRepartoDetalle();
            $rutaReparto->queryDelete($condicion);
            unset($rutaReparto);
        }
    }

    /**
     * Crea la direccion de entrega por defecto del cliente $IDCliente
     */
    public function createDireccionEntrega() {
        $de = new ClientesDentrega();
        $de->setIDCliente($this->IDCliente);
        $de->setNombre($this->RazonSocial);
        $de->setDireccion($this->Direccion);
        $de->setIDPais($this->IDPais);
        $de->setPoblacion($this->Poblacion);
        $de->setIDProvincia($this->IDProvincia);
        $de->setCodPostal($this->CodigoPostal);
        $de->setTelefono($this->Telefono);
        $de->setMovil($this->Movil);
        $de->setFax($this->Fax);
        $de->setEMail($this->EMail);
        $de->setIDComercial($this->IDComercial);
        $de->setIDZona($this->IDZona);
        $de->create();
    }

    /**
     * Calcula el riesgo de un cliente
     * Devuelve el siguiente array:
     *
     * array(
     *       'AL' => array(),  //Albaranes pendientes de facturar (numero e importe)
     *       'RE' => array(),  //Recibos pendientes de cobro (numero e importe)
     *       'RI' => decimal,  //El límite de riesgo asignado por la aseguradora
     * );
     *
     * @return array $pteCobro Array con el riesgo del cliente
     */
    public function getRiesgo() {
        $pteCobro = array(
            'AL' => $this->getPteFacturar(), //Albaranes pendientes de facturar (numero e importe)
            'RE' => $this->getPteCobro(), //Recibos pendientes de cobro (numero e importe)
            'RI' => $this->getLimiteRiesgo(), //El límite de riesgo asignado por la aseguradora
        );

        return $pteCobro;
    }

    /**
     * Devuelve true o false indicando si el cliente
     * ha superado el riesgo concedido
     * 
     * @return boolean
     */
    public function superadoRiesgo() {
        $recibos = $this->getPteCobro();
        $pteCobro = $recibos['Importe'];
        $riesgo = $this->getLimiteRiesgo();

        return (($riesgo > 0) and ( $pteCobro >= $riesgo));
    }

    /**
     * Calcula el número de albaranes y el importe total con impuestos
     * de los albaranes del cliente indicado que están SERVIDOS (IDEstado=2) pero
     * NO facturados (IDFactura = '0')
     *
     * Devuelve el array (
     *      'Albaranes' =>  Numero de albaranes,
     *      'Importe'   =>  Suma del importe de todos los albaranes con impuestos
     * )
     *
     * @return Array $pteFacturar Array con lo pendiente de facturar
     */
    public function getPteFacturar() {
        $pteFacturar = array();

        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "select count(IDAlbaran) as Albaranes, sum(Total) as Importe from {$this->_dataBaseName}.albaranes_cab where IDCliente='{$this->IDCliente}' and IDEstado='2' and IDFactura='0'";
            if ($this->_em->query($query)) {
                $rows = $this->_em->fetchResult();
                $pteFacturar = $rows[0];
            } else {
                $this->_errores[] = $this->_em->getError();
            }
            $this->_em->desConecta();
        } else {
            $this->_errores[] = $this->_em->getError();
        }

        return $pteFacturar;
    }

    /**
     * Calcula el número de recibos y el importe total de los
     * recibos del cliente indicado que no están PENDIENTES DE COBRO (IDEstado<>6)
     *
     * Devuelve el array (
     *      'Recibos'   =>  Numero de recibos,
     *      'Importe'   =>  Suma del importe de todos los recibos
     * )
     *
     * @return Array $pteCobro Array con lo pendiente de cobro
     */
    public function getPteCobro() {
        $pteCobro = array();

        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "select count(IDRecibo) as Recibos, sum(Importe) as Importe from {$this->_dataBaseName}.recibos_clientes where IDCliente='{$this->IDCliente}' and IDEstado<>'6'";
            if ($this->_em->query($query)) {
                $rows = $this->_em->fetchResult();
                $pteCobro = $rows[0];
            } else {
                $this->_errores[] = $this->_em->getError();
            }
            $this->_em->desConecta();
        } else {
            $this->_errores[] = $this->_em->getError();
        }

        return $pteCobro;
    }

    /**
     * Devuelve un array de objetos recibos del cliente
     * que están en el estado $idEstado. Por defecto todos
     *
     * La ordenación es descendente repecto a la fecha de Vencimiento
     *
     * @param integer $idEstado El estado de los recibos (opcional)
     * @return array Objetos recibos de la factura
     */
    public function getRecibos($idEstado = '') {
        $recibos = array();

        $filtro = "IDCliente='{$this->IDCliente}'";
        if ($idEstado != '')
            $filtro .= " AND IDEstado='{$idEstado}'";

        $recibo = new RecibosClientes();
        $rows = $recibo->cargaCondicion("IDRecibo", $filtro, "Vencimiento DESC");
        foreach ($rows as $row) {
            $recibos[] = new RecibosClientes($row['IDRecibo']);
        }
        unset($recibo);

        return $recibos;
    }

    /**
     * Devuelve un array de objetos RutasVentas
     * del cliente.
     * 
     * @return array Array de objetos de rutas de venta
     */
    public function getRutasVentas() {

        $rutasVentas = array();

        $rutaVentas = new RutasVentas();
        $rows = $rutaVentas->cargaCondicion("Id", "IDCliente={$this->IDCliente}", "Dia ASC, OrdenZona ASC");
        unset($rutaVentas);

        foreach ($rows as $row) {
            $rutasVentas[] = new RutasVentas($row['Id']);
        }

        return $rutasVentas;
    }

    /**
     * Devuelve un array de objeto RutasRepartoDetalle
     * 
     * Si se indica $idDirec el array tendrá las rutas de reparto de la dirección de entrega
     * 
     * Si no se indica $idDirec el array tendrá las rutas de reparto de todas las 
     * direcciones de entrega del cliente
     * 
     * @param integer $idDirec El id de la direccion de entregra, opcional
     * @return array Array con objetos RutasRepartoDetalle
     */
    public function getRutasReparto($idDirec = '') {

        $rutasReparto = array();

        if ($idDirec == '') {

            $em = new EntityManager("datos" . $_SESSION['emp']);
            if ($em->getDbLink()) {
                $query = "SELECT Id
                FROM {$this->_dataBaseName}.rutas_reparto_detalle as t1
                WHERE t1.IDDirec IN
                (SELECT IDDirec from clientes_dentrega where IDCliente='{$this->IDCliente}')
                ORDER BY t1.Dia ASC, t1.OrdenDirec ASC";
                $em->query($query);
                $rows = $em->fetchResult();
                $em->desConecta();
            }
            unset($em);
        } else {
            $rutaReparto = new RutasRepartoDetalle();
            $rows = $rutaReparto->cargaCondicion('Id', "IDDirec='{$idDirec}'", "Dia ASC, OrdenDirec ASC");
            unset($rutaReparto);
        }

        foreach ($rows as $row) {
            $rutasReparto[] = new RutasRepartoDetalle($row['Id']);
        }

        return $rutasReparto;
    }

    /**
     * Devuelve un array con todos los clientes de la sucursal (opcional) y agente (opcional) indicado
     * Si no se indica sucursal, se toma la sucursal en curso.
     * Si el agente indicado es comercial, solo se muestran sus clientes, en caso contrato
     * se muestran todos los clientes de la sucursal indicada.
     *
     * Cada elemento tiene la primarykey y el valor de $column
     */
    public function fetchAll($idSucursal = '', $idAgente = '', $column = 'RazonSocial') {
        if ($idSucursal == '')
            $idSucursal = $_SESSION['suc'];

        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $filtro = "WHERE (IDSucursal='" . $idSucursal . "') ";
            if ($idAgente != '') {
                $agente = new Agentes($idAgente);
                if ($agente->getEsComercial())
                    $filtro .= " AND (IDComercial='" . $idAgente . "')";
                unset($agente);
            }
            $query = "SELECT IDCliente as Id,{$column} as Value FROM `{$this->_dataBaseName}`.`clientes` {$filtro} ORDER BY {$column} ASC;";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        $rows[] = array('Id' => '', Value => ':: Cliente');
        return $rows;
    }

    /**
     * Devuelve un array filtrando los clientes en base a:
     *   Un valor de filtro que se busca en el Cif, la razon social y el nombre comercial (%valorFiltro%)
     *   Una sucursal, si no se indica se toma la sucursal en curso
     *   Si el usuario en curso es del rol comercial, solo se muestran los adscritos a el
     *
     * @param integer $idSucursal El id de la sucursal
     * @param string $valorFiltro
     * @return array
     */
    public function fetchClientesSucursal($idSucursal = '', $valorFiltro = '') {

        if ($idSucursal == '')
            $idSucursal = $_SESSION['suc'];

        $this->conecta();

        $rows = array();

        if (is_resource($this->_dbLink)) {
            $usuario = new Agentes($_SESSION['USER']['user']['id']);

            $filtro = "(IDSucursal='{$idSucursal}') and (Vigente='1') and ( (RazonSocial LIKE '%{$valorFiltro}%') or (NombreComercial LIKE '%{$valorFiltro}%') or (Cif LIKE '%{$valorFiltro}%') )";
            if ($usuario->getEsComercial())
                $filtro .= " and (IDComercial='" . $_SESSION['USER']['user']['id'] . "')";
            $query = "SELECT IDCliente as Id, CONCAT(RazonSocial,' - ',NombreComercial) as Value FROM `{$this->_dataBaseName}`.`clientes` where ( {$filtro} ) ORDER BY RazonSocial";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
            unset($usuario);
        }
        return $rows;
    }

    /**
     * Devuelve las promociones vigentes para el cliente
     * y la fecha indicada.
     *
     * El formato de fecha pasado debe ser YYYY-mm-dd
     *
     * @param date $fecha La fecha
     * @return array Array con objetos promociones
     */
    public function getPromosVigentes($fecha) {

        $promos = array();

        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "SELECT DISTINCT t1.IDPromocion
                FROM {$this->_dataBaseName}.promociones as t1,
                     {$this->_dataBaseName}.promociones_clientes as t2,
                     {$this->_dataBaseName}.familias as t3
                WHERE t1.FinPromocion>='{$fecha}'
                AND t1.IDPromocion=t2.IDPromocion
                AND ( (t2.IDCliente='{$this->IDCliente}') OR (t2.IDGrupo='{$this->getIDGrupo()->getIDGrupo()}') )
                AND ( t1.IDFamilia = t3.IDFamilia OR (1))
                ORDER BY t1.FinPromocion ASC, t1.IDArticulo DESC,t3.Familia";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();

            foreach ($rows as $row) {
                $promos[] = new Promociones($row['IDPromocion']);
            }
        }
        unset($em);

        return $promos;
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
     * Devuelve un array con los clientes que tienen albaranes pendientes
     * de facturar (IDEstado=2) en el periodo de fechas indicado y de la
     * sucursal indicada.
     *
     * El array tiene tres columnas:
     *       Id (el id del cliente),
     *       Value (la razon social del cliente)
     *       Total (la suma de los totales de todos sus albaranes pendientes de facturar)
     *
     * @param integer $idSucursal
     * @param date $desdeFecha Fecha en formato dd/mm/aaaa
     * @param date $hastaFecha Fecha en formato dd/mm/aaaa
     * @return array Array con los clientes
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
            $filtro = "(a.IDSucursal='{$idSucursal}') and
                        (a.FechaEntrega>='{$desdeFecha}') and
                        (a.FechaEntrega<='{$hastaFecha}') and
                        (a.IDFactura='0') and
                        (c.IDCliente=a.IDCliente)";

            $query = "SELECT a.IDCliente as Id, c.RazonSocial as Value, sum(a.Total) as Total
                        FROM `{$this->_dataBaseName}`.`clientes` c, `{$this->_dataBaseName}`.`albaranes_cab` a
                        WHERE ( {$filtro} )
                        GROUP BY c.IDCliente
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
