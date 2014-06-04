<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 15.04.2012 00:47:17
 */

/**
 * @orm:Entity(caja_arqueos)
 */
class CajaArqueos extends CajaArqueosEntity {

    public function __toString() {
        return $this->getIDArqueo();
    }

    /**
     * Si no esta cerrada, se recalcula antes de guardar
     */
    public function save() {

        if ($this->CajaCerrada == 0)
            $this->recalcula();

        return parent::save();
    }

    /**
     * Realizar la apertura de caja.
     * Hace un apunte de apertura en la caja que se va a abrir
     * en base al importe del cierre de la caja anterior cerrada
     *
     * Comprobar que no exista otra caja para la misma sucursal, tpv y dia
     */
    public function create() {

        if ($this->estaAbierta())
            $this->_errores[] = "Ya existe un arqueo de caja para ese día";
        else
            return $this->apertura();
    }

    /**
     * Comprueba si está abierta la caja correspondiente
     * a la sucursal, tpv y día de las propiedades del objeto
     *
     * @return int EL id de arqueo si está abierta, o cero en caso contrario (está cerrada o no abierta)
     */
    public function estaAbierta() {

        $idArqueo = 0;

        $filtro = "IDSucursal='{$this->IDSucursal}' and IDTpv='{$this->IDTpv}' and Dia='{$this->Dia}' and CajaCerrada='0'";
        $arqueo = new CajaArqueos();
        $rows = $arqueo->cargaCondicion('IDArqueo', $filtro);
        unset($arqueo);

        $idArqueo = $rows[0]['IDArqueo'];

        return $idArqueo;
    }

    /**
     * Comprueba si está cerrada la caja correspondiente
     * a la sucursal, tpv y día de las propiedades del objeto
     *
     * @return boolean True si existe y está cerrada, false si no existe o existiendo está abierta
     */
    public function estaCerrada() {

        $filtro = "IDSucursal='{$this->IDSucursal}' and IDTpv='{$this->IDTpv}' and Dia='{$this->Dia}' and CajaCerrada='1'";
        $arqueo = new CajaArqueos();
        $rows = $arqueo->cargaCondicion('IDArqueo', $filtro);
        unset($arqueo);

        return (count($rows) == 1);
    }

    /**
     * Comprobar que la fecha indicada no sea anterior a la actual
     */
    public function validaLogico() {

        // Comprobar que la fecha indicada no sea anterior a la actual.
        if ($this->Dia < date('Y-m-d'))
            $this->_errores[] = "La fecha no puede ser anterior a la actual";
    }

    /**
     * Recalcula los totales de caja
     * !! OJO: no guarda. Hay que llamar al método save
     * @return boolean
     */
    public function recalcula() {

        $this->conecta();
        if (is_resource($this->_dbLink)) {
            // Calcular el importe de los movimientos de apertura (Origen=0)
            $query = "select sum(Importe) as Importe from {$this->_dataBaseName}.caja_lineas where (IDArqueo='{$this->IDArqueo}' and Origen='0')";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->setSaldoApertura($rows[0]['Importe']);

            // Calcular el importe del resto de los movimientos (Origen<>0)
            $query = "select sum(Importe) as Importe from {$this->_dataBaseName}.caja_lineas where (IDArqueo='{$this->IDArqueo}' and Origen<>'0')";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->setSumaMvtos($rows[0]['Importe']);

            // Calcular el importe total de los movimientos
            $this->setSaldoCierre($this->SaldoApertura + $this->SumaMvtos);
        }
    }

    /**
     * Abre la caja en el día actual con los importes
     * de la caja que esté cerrada inmediatamente antes.
     *
     * Si la caja inmediatamente anterior no estuviera cerrada, la cierra
     *
     * Se crean tantos apuntes de apertura como formas de pago
     * haya en el arqueo de la caja cerrada
     *
     * @return integer El id del arqueo creado
     */
    public function apertura() {

        // Localizar el arqueo anterior.
        $filtro = "IDSucursal='{$this->IDSucursal}' and IDTpv='{$this->IDTpv}'";
        $arqueo = new CajaArqueos();
        $rows = $arqueo->cargaCondicion("IDArqueo,Dia,CajaCerrada", $filtro, "Dia DESC");

        $arqueoAnterior = $rows[0];
        if (count($rows)) {
            $arqueo = new CajaArqueos($arqueoAnterior['IDArqueo']);
            // Si el arqueo anterior está abierto, lo cierro
            if (!$arqueoAnterior['CajaCerrada'])
                $arqueo->cierra();

            // Agrupar los importes por forma de pago
            $importes = $arqueo->getResumen();
        } else
            $importes = array();

        unset($arqueo);

        // Abrir la caja
        $idArqueo = parent::create();

        if ($idArqueo) {
            // Crear los apuntes de apertura
            foreach ($importes as $key => $importe) {
                $dia = new Fecha($rows[0]['Dia']);
                $apunte = new CajaLineas();
                $apunte->setIDArqueo($idArqueo);
                $apunte->setFecha(date('Y-m-d H:i:s'));
                $apunte->setConcepto('APERTURA CON EL CIERRE DEL DIA ' . $dia->getddmmaaaa());
                $apunte->setIDFP($importe['IDFP']);
                $apunte->setOrigen(0); // Apertura
                $apunte->setEntidad('CajaArqueos');
                $apunte->setIDEntidad($arqueoAnterior['IDArqueo']);
                $apunte->setImporte($importe['Importe']);
                $apunte->setIDAgente($_SESSION['USER']['user']['id']);
                $apunte->create();
                $this->_errores = $apunte->getErrores();
                unset($apunte);
                unset($dia);
            }
        }

        return $idArqueo;
    }

    /**
     * Marca la caja como cerrada
     *
     * No apertura ninguna caja
     *
     * @return boolean TRUE si se cerró con éxito
     */
    public function cierra() {
        $this->setCajaCerrada(1);
        return $this->save();
    }

    /**
     * Devuelve un array con los totales de movimientos por tipo de forma de cobro
     *
     * No se incluyen las formas de pago que su total sea cero.
     *
     * El array es
     * array{
     *   0 => array(IDFP=> , Descripcion=> , Importe=>)
     *   . . .
     *   n => array(IDFP=> , Descripcion=> , Importe=>)
     * }
     * @return array Totalizacion de movimientos por tipo de forma de cobro
     */
    public function getResumen() {

        $resumen = array();

        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "SELECT t1.IDFP as IDFP, t2.Descripcion, sum(t1.Importe) as Importe
                FROM
                    {$this->_dataBaseName}.caja_lineas as t1,
                    {$this->_dataBaseName}.formas_pago as t2
                WHERE
                    (t1.IDArqueo='{$this->IDArqueo}') AND
                    (t1.IDFP=t2.IDFP)
                GROUP BY t1.IDFP
                HAVING (sum(t1.Importe) <> 0)";
            $this->_em->query($query);
            $resumen = $this->_em->fetchResult();
        }

        return $resumen;
    }

    /**
     * Recibe un objeto y realiza un apunte en caja con los
     * valores del mismo. Si la caja no estuviera abierta, la abre.
     *
     * El apunte se realiza en la sucursal y tpv en curso 
     * 
     * Los objetos posibles son:
     *
     *  Contratos
     *  FemitidasCab
     *  FrecibidasCab
     *  RecibosClientes
     *  RecibosProveedores
     *
     * La forma de pago que se pondrá en el apunte de caja es la indicada en el
     * parámetro $idFormaPago, y si no se indica, será la que tenga asociada
     * el objeto.
     *
     * @param object $objeto Contratos, FemitidasCab, FrecibidasCab, RecibosClientes, RecibosProveedores
     * @param integer $idFormaPago El id de la forma de pago (opcional)
     * @return boolean TRUE si se pudo crear, FALSE si no.
     */
    public function anotaEnCaja($objeto, $idFormaPago = '') {

        $ok = false;

        if ($_SESSION['tpv'] == '') {
            $this->_errores[] = "No se ha establecido el TPV";
            return $ok;
        }

        $entidad = get_class($objeto);
        //$idSucursal = $objeto->getIDSucursal()->getIDSucursal();
        if ($idFormaPago == '')
            $idFormaPago = $objeto->getIDFP()->getIDFP();

        switch ($entidad) {
            case 'Contratos':
                $concepto = "Pago Contrato " . $objeto->getIDArticulo()->getIDVenta() . " " . $objeto->getNumeroDocumento();
                $origen = 7;
                $importe = -1 * $objeto->getImportePago();
                $idEntidad = $objeto->getPrimaryKeyValue();
                break;

            case 'FemitidasCab':
                $concepto = "Cobro N/Fra. {$objeto->getNumeroFactura()} de {$objeto->getIDCLiente()}";
                $origen = 2;
                $importe = $objeto->getTotal();
                $idEntidad = $objeto->getPrimaryKeyValue();
                break;

            case 'RecibosClientes':
                $concepto = "Cobro N/Fra. {$objeto->getIDFactura()->getNumeroFactura()} de {$objeto->getIDCliente()}";
                echo $concepto;
                $origen = 3;
                $importe = $objeto->getImporte();
                $idEntidad = $objeto->getPrimaryKeyValue();
                break;

            case 'FrecibidasCab':
                $concepto = "Pago S/Fra. {$objeto->getNumeroFactura()} de {$objeto->getIDProveedor()}";
                $origen = 4;
                $importe = -1 * $objeto->getTotal();
                $idEntidad = $objeto->getPrimaryKeyValue();
                break;

            case 'RecibosProveedores':
                $concepto = "Pago S/Fra. {$objeto->getIDFactura()->getNumeroFactura()} de {$objeto->getIDProveedor()}";
                $origen = 5;
                $importe = -1 * $objeto->getImporte();
                $idEntidad = $objeto->getPrimaryKeyValue();
                break;
        }

        // Comprobar que la caja esté abierta, si no, abrirla
        $this->setIDSucursal($_SESSION['suc']);
        $this->setIDTpv($_SESSION['tpv']);
        $this->setDia(date('Y-m-d'));
        $idArqueo = $this->estaAbierta();

        if (!$idArqueo)
            $idArqueo = $this->apertura();

        if ($idArqueo) {
            $apunte = new CajaLineas();
            $apunte->setIDArqueo($idArqueo);
            $apunte->setFecha(date('Y-m-d H:i:s'));
            $apunte->setConcepto($concepto);
            $apunte->setIDFP($idFormaPago);
            $apunte->setOrigen($origen);
            $apunte->setEntidad($entidad);
            $apunte->setIDEntidad($idEntidad);
            $apunte->setImporte($importe);
            $apunte->setIDAgente($_SESSION['USER']['user']['id']);
            $ok = $apunte->create();
            $this->_errores = $apunte->getErrores();
        } else
            $this->_errores[] = "No se ha realizado el apunte de caja.";

        return $ok;
    }

    /**
     * Devuelve un array con los ids de arqueos que están
     * abiertos anteriores a la fecha actual de la sucursal
     * y tpv indicados.
     *
     * Este método se utiliza para saber los arqueos que siendo antiguos, no están cerrados
     *
     * @param string $idSucursal El id de sucursal o '%', opcional
     * @param string $idTpv El id de tpv o '%', opcional
     * @return array Array
     */
    public function getArqueosAbiertos($idSucursal='',$idTpv='') {

        if ($idSucursal == '')
            $idSucursal = $_SESSION['suc'];
        if ($idTpv == '')
            $idTpv = $_SESSION['tpv'];

        $dia = date('Y-m-d');
        $rows = $this->cargaCondicion('IDArqueo',"IDSucursal LIKE '{$idSucursal}' AND IDTpv LIKE '{$idTpv}' AND CajaCerrada='0' AND Dia<'$dia'");

        return $rows;
    }

}

?>