<?php

/**
 * CONTROLLER FOR ImportarFemitidas
 *
 * Sube un archivo con facturas recibidas y lo carga en la BD
 *
 * Solo carga las cabeceras de la facturas, NO carga líneas de facturas
 *
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 22.08.2012 13:25
 */
class ImportarFemitidasController extends Controller {

    protected $entity = 'ImportarFemitidas';

    public function __construct($request) {

        // Cargar lo que viene en el request
        $this->request = $request;

        // Cargar la configuracion del modulo (modules/moduloName/config.yml)
        $this->form = new Form($this->entity);

        // Cargar los permisos.
        // Si la entidad no está sujeta a control de permisos, se habilitan todos
        if ($this->form->getPermissionControl()) {
            if ($this->parentEntity == '')
                $this->permisos = new ControlAcceso($this->entity);
            else
                $this->permisos = new ControlAcceso($this->parentEntity);
        } else
            $this->permisos = new ControlAcceso();

        $this->values['titulo'] = $this->form->getTitle();
        $this->values['ayuda'] = $this->form->getHelpFile();
        $this->values['permisos'] = $this->permisos->getPermisos();
        $this->values['request'] = $this->request;
        $this->values['linkBy'] = array(
            'id' => $this->form->getLinkBy(),
            'value' => '',
        );

        // Si se ha indicado una entidad en el config.yml del controlador
        // pero no se ha definido la conexion, se muestra un error
        if (($this->form->getEntity()) and (!$this->form->getConection())) {
            echo "No se ha definido la conexión para la entidad: " . $this->entity;
        }

        // QUITAR LOS COMENTARIOS PARA Actualizar los favoritos para el usuario
        //if ($this->form->getFavouriteControl())
        //    $this->actualizaFavoritos();;
    }

    public function indexAction() {

        $formaPago = new FormasPago();
        $this->values['formasPago'] = $formaPago->fetchAll('Descripcion', false);
        unset($formaPago);

        return array('template' => $this->entity . '/index.html.twig', 'values' => $this->values);
    }

    /**
     * Sube al servidor un archivo externo con las cabeceras de facturas emitidas
     *
     * No carga líneas de detalle de facturas, solo las cabeceras
     *
     * @return array
     */
    public function UploadAction() {

        if ($this->values['permisos']['I']) {

            // Ruta al archivo destino sin la extensión
            $path = "docs/docs" . $_SESSION['emp'] . "/tmp/femitidas" . "_" . date('His');
            $archivo = new Archivo($path);

            if ($archivo->upLoad($_FILES['importar'])) {
                $this->values['archivoLog'] = $this->importarArchivo($archivo->getUpLoadedFileName());
            } else
                $this->values['errores'] = $archivo->getErrores();
            unset($archivo);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }

        return $this->indexAction();
    }

    /**
     * Carga facturas emitidas desde un archivo de texto externo
     * Las columnas deben venir separadas por tabuladores
     * Las facturas se cargan en la sucursal en curso
     *
     * @param string $fileName El path completo del archivo a importar
     */
    private function importarArchivo($fileName) {


        $archivo = new Archivo($fileName);

        if ($archivo->open("r")) {

            set_time_limit(0);

            // Lee contador
            $contador = new Contadores();
            $contador = $contador->dameContador($_SESSION['suc'], 2);
            $idContador = $contador->getIDContador();

            // Buscar la cuenta contable de ventas para la sucursal
            $sucursal = new Sucursales($_SESSION['suc']);
            $ctaVentas = $sucursal->getCtaContableVentas();
            unset($sucursal);

            while (($linea = $archivo->readLine()) !== FALSE) {
          
                $fecha = explode("-", $linea[2]);

                $cliente = new Clientes();
                $cliente = $cliente->find('CContable', $linea[3]);
                if ($cliente->getIDCliente() != '') {
                    
                    // Pongo los valores a cero en caso de que no venga nada
                    for ($i = 6; $i <= 11; $i++)
                        if ($linea[$i] == '')
                            $linea[$i] = 0;
                    $b1 = $linea[6];
                    $i1 = $linea[7];
                    $ci1 = round($b1 * $i1 / 100, 2);
                    $r1 = $linea[8];
                    $cr1 = round($b1 * $r1 / 100, 2);

                    $b2 = $linea[9];
                    $i2 = $linea[10];
                    $ci2 = round($b2 * $i2 / 100, 2);
                    $r2 = $linea[11];
                    $cr2 = round($b2 * $r2 / 100, 2);

                    $factura = new FemitidasCab();

                    $factura->setIDContador($idContador);
                    if ($this->request['RespectarNumerosFactura'] == 'on')
                        $numeroFactura = $contador->getSerie() . (1200000 + $linea[1]);
                    else
                        $numeroFactura = $contador->asignaContador();

                    $factura->setNumeroFactura($numeroFactura);
                    $factura->setIDSucursal($_SESSION['suc']);
                    $factura->setIDAgente($_SESSION['USER']['user']['id']);
                    $factura->setIDComercial(1);
                    $factura->setFecha($fecha[0] . "/" . $fecha[1] . "/20" . $fecha[2]);
                    $factura->setIDCliente($cliente->getIDCliente());
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
                    $factura->setIDFP($this->request['IDFP']);
                    $factura->setAsiento(9999);
                    $factura->setCuentaVentas($ctaVentas);
                    $factura->setClave(md5($factura->getNumeroFactura()));
                    if ($factura->create()) {
                        $factura->creaVctos();
                    } else
                        $this->values['errores'] = $factura->getErrores();
                } else
                    $this->values['errores'][] = "No existe el cliente " . $linea[3] . " " . $linea[5] . ". No se carga la factura " . $linea[1];
            }
            $archivo->close();
        } else
            $this->values['errores'][] = "El fichero de importación " . $fileName . " no existe";

        unset($archivo);
    }

}

?>
