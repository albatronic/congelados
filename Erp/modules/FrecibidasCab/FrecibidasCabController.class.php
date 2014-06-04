<?php

/**
 * CONTROLLER FOR FrecibidasCab
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 07.06.2011 00:45:16

 * Extiende a la clase controller
 */
class FrecibidasCabController extends Controller {

    protected $entity = "FrecibidasCab";
    protected $parentEntity = "";

    /**
     * Edita, actualiza o borrar un registro
     *
     * Si viene por GET es editar
     * Si viene por POST puede ser actualizar o borrar
     * según el valor de $this->request['accion']
     *
     * @return array con el template y valores a renderizar
     */
    public function editAction() {

        switch ($this->request["METHOD"]) {
            case 'GET':
                if ($this->values['permisos']['C']) {
                    //SI EN LA POSICION 3 DEL REQUEST VIENE ALGO,
                    //SE ENTIENDE QUE ES EL VALOR DE LA CLAVE PARA LINKAR CON LA ENTIDAD PADRE
                    //ESTO SE UTILIZA PARA LOS FORMULARIOS PADRE->HIJO
                    if ($this->request['3'] != '')
                        $this->values['linkBy']['value'] = $this->request['3'];

                    //MOSTRAR DATOS. El ID viene en la posicion 2 del request
                    $datos = new $this->entity($this->request[2]);
                    if ($datos->getStatus()) {
                        $this->values['datos'] = $datos;
                        $this->values['errores'] = $datos->getErrores();
                        return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
                    } else {
                        $this->values['errores'] = array("Valor no encontrado. El objeto que busca no existe. Es posible que haya sido eliminado por otro usuario.");
                        return array('template' => $this->entity . '/new.html.twig', 'values' => $this->values);
                    }
                } else {
                    return array('template' => '_global/forbiden.html.twig');
                }
                break;

            case 'POST':
                //COGER DEL REQUEST EL LINK A LA ENTIDAD PADRE
                if ($this->values['linkBy']['id'] != '') {
                    $this->values['linkBy']['value'] = $this->request[$this->entity][$this->values['linkBy']['id']];
                }

                switch ($this->request['accion']) {
                    case 'Guardar': //GUARDAR DATOS
                        if ($this->values['permisos']['A']) {
                            // Cargo la entidad
                            $datos = new $this->entity($this->request[$this->entity][$this->form->getPrimaryKey()]);
                            // Comprar si se han cambiado la forma de pago.
                            // En cuyo caso hay que borrar los vencimientos y volverlos a crear.
                            if ($datos->getIDFP()->getIDFP() != $this->request[$this->entity]['IDFP']) {
                                $cambioFormaPago = $datos->borraVctos();
                            }
                            // Vuelco los datos del request
                            $datos->bind($this->request[$this->entity]);
                            if ($datos->valida($this->form->getRules())) {
                                $this->values['alertas'] = $datos->getAlertas();
                                $datos->save();
                                if ($cambioFormaPago) {
                                    $datos->creaVctos();
                                    // Anotar en caja sin procede
                                    $datos->anotaEnCaja();
                                }

                                //Recargo el objeto para refrescar las propiedas que
                                //hayan podido ser objeto de algun calculo durante el proceso
                                //de guardado.
                                $datos = new $this->entity($this->request[$this->entity][$datos->getPrimaryKeyName()]);
                            } else {
                                $this->values['errores'] = $datos->getErrores();
                                $this->values['alertas'] = $datos->getAlertas();
                            }
                            $this->values['datos'] = $datos;
                            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
                        } else {
                            return array('template' => '_global/forbiden.html.twig');
                        }
                        break;

                    case 'Borrar': //BORRAR DATOS
                        if ($this->values['permisos']['B']) {
                            $datos = new $this->entity($this->request[$this->entity][$this->form->getPrimaryKey()]);

                            if ($datos->erase()) {
                                $datos = new $this->entity();
                                $this->values['datos'] = $datos;
                                $this->values['errores'] = array();
                                unset($datos);
                                return array('template' => $this->entity . '/index.html.twig', 'values' => $this->values);
                            } else {
                                $this->values['datos'] = $datos;
                                $this->values['errores'] = $datos->getErrores();
                                $this->values['alertas'] = $datos->getAlertas();
                                unset($datos);
                                return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
                            }
                        } else {
                            return array('template' => '_global/forbiden.html.twig');
                        }
                        break;
                }
                break;
        }
    }

    /**
     * Devuelve el template "listVencimientos" con un listado
     * de todos los vencimientos de la factura en curso
     *
     * El template extiende al popup y está pensado para ser mostrado
     * en una solapa
     *
     * @return array
     */
    public function listVencimientosAction() {

        $idFactura = $this->request[2];

        $datos = new FrecibidasCab($idFactura);
        $this->values['recibos'] = $datos->getRecibos();

        unset($datos);

        return array('template' => $this->entity . "/listVencimientos.html.twig", 'values' => $this->values);
    }

    /**
     * Carga facturas recibidas desde un archivo de texto externo
     * Las columnas deben venir separadas por tabuladores
     * Las facturas se cargan en la sucursal en curso
     *
     * @param string $fileName El path completo del archivo a importar
     * @return array
     */
    public function ImportarAction($fileName = '') {

        if ($this->values['permisos']['I']) {
            if ($fileName == '')
                $fileName = $this->request[2];

            if ($fileName != '')
                $fileName = "docs/docs{$_SESSION['emp']}/tmp/" . $fileName;

            $archivo = new Archivo($fileName);

            if ($archivo->open("r")) {

                set_time_limit(0);

                // Lee contador
                $contador = new Contadores();
                $contador = $contador->dameContador($_SESSION['suc'], 4);
                $idContador = $contador->getIDContador();

                // Buscar la cuenta contable de compras para la sucursal
                $sucursal = new Sucursales($_SESSION['suc']);
                $ctaCompras = $sucursal->getCtaContableCompras();
                unset($sucursal);

                while (($linea = $archivo->readLine()) !== FALSE) {

                    $fecha = explode("-", $linea[1]);

                    $proveedor = new Proveedores();
                    $proveedor = $proveedor->find('CContable', $linea[2]);
                    if ($proveedor->getIDProveedor() != '') {
                        $b1 = $linea[5];
                        $i1 = $linea[6];
                        $ci1 = round($b1 * $i1 / 100, 2);
                        $r1 = $linea[7];
                        $cr1 = round($b1 * $r1 / 100, 2);

                        $b2 = $linea[8];
                        $i2 = $linea[9];
                        $ci2 = round($b2 * $i2 / 100, 2);
                        $r2 = $linea[10];
                        $cr2 = round($b2 * $r2 / 100, 2);

                        $factura = new FrecibidasCab();
                        $factura->setIDContador($idContador);
                        $factura->setIDSucursal($_SESSION['suc']);
                        $factura->setNumeroFactura($contador->asignaContador());
                        $factura->setSuFactura($linea[0]);
                        $factura->setFecha($fecha[0] . "/" . $fecha[1] . "/20" . $fecha[2]);
                        $factura->setIDProveedor($proveedor->getIDProveedor());
                        $factura->setImporte($b1 + $b2);
                        $factura->setBaseImponible1($b1);
                        $factura->setIva1($i1);
                        $factura->setCuotaIva1($ci1);
                        $factura->setRecargo1($r1);
                        $factura->setCuotaRecargo1($cr1);
                        $factura->setBaseImponible2($b2);
                        $factura->setIva2($i2);
                        $factura->setCuotaIva2($ci2);
                        $factura->setRecargo2($r2);
                        $factura->setCuotaRecargo2($cr2);
                        $factura->setTotalBases($b1 + $b2);
                        $factura->setTotalIva($ci1 + $ci2);
                        $factura->setTotalRecargo($cr1 + $cr2);
                        $factura->setTotal($b1 + $b2 + $ci1 + $ci2 + $cr1 + $cr2);
                        $factura->setIDFP(1);
                        $factura->setAsiento(9999);
                        $factura->setCuentaCompras($ctaCompras);
                        if ($factura->create()) {
                            $factura->creaVctos();
                        } else
                            $this->values['errores'][] = $factura->getErrores();
                        //print_r($factura->getErrores());
                    } else
                        $this->values['errores'][] = "No existe el proveedor " . $linea[2] . " " . $linea[4] . ". No se carga la factura " . $linea[0];
                }
                $archivo->close();
            } else
                $this->values['errores'][] = "El fichero de importación " . $fileName . " no existe";

            unset($archivo);

            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

}

?>