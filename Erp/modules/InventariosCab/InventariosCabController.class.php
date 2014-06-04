<?php

/**
 * CONTROLLER FOR InventariosCab
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 31.05.2012 23:22:38

 * Extiende a la clase controller
 */
class InventariosCabController extends Controller {

    protected $entity = "InventariosCab";
    protected $parentEntity = "";

    /**
     * Cierra el inventario
     */
    public function cerrarAction() {
        if ($this->values['permisos']['A']) {

            $datos = new InventariosCab($this->request['InventariosCab']['IDInventario']);
            $datos->cierra();
            $this->values['errores'] = $datos->getErrores();
            $this->values['alertas'] = $datos->getAlertas();

            $datos = new InventariosCab($this->request['InventariosCab']['IDInventario']);

            $this->values['datos'] = $datos;
            unset($datos);
            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Cargar archivo externo con el inventario
     * 
     * @return array
     */
    public function importarAction() {

        if ($this->values['permisos']['I']) {

            // Ruta al archivo destino sin la extensión
            $path = "docs/docs" . $_SESSION['emp'] . "/tmp/inventario" . $this->request['InventariosCab']['IDInventario'] . "_" . date('His');
            $archivo = new Archivo($path);

            if ($archivo->upLoad($_FILES['importar'])) {
                $this->values['archivoLog'] = $this->importarArchivo($archivo->getUpLoadedFileName());
            } else
                $this->values['errores'] = $archivo->getErrores();
            unset($archivo);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }

        $this->values['datos'] = new $this->entity($this->request[$this->entity][$this->form->getPrimaryKey()]);
        return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
    }

    /**
     * Genera un array con la informacion necesaria para imprimir el documento
     * Recibe un array con los ids de inventario
     *
     * @param array $idsDocumento Array con los ids de inventario
     * @return array Array con dos elementos: master es un objeto inventario y detail es un array de objetos lineas de inventario
     */
    protected function getDatosDocumento(array $idsDocumento) {

        $master = array();
        $detail = array();

        // Recorro el array de los inventarios a imprimir
        foreach ($idsDocumento as $key => $idDocumento) {
            // Instancio la cabecera del inventario
            $master[$key] = new InventariosCab($idDocumento);

            // LLeno el array con objetos de lineas de inventario
            $lineas = array();
            $inventarioLineas = new InventariosLineas();
            $rows = $inventarioLineas->cargaCondicion('IDLinea', "IDInventario='{$idDocumento}'", "IDUbicacion,IDArticulo ASC");
            foreach ($rows as $row) {
                $lineas[] = new InventariosLineas($row['IDLinea']);
            }
            $detail[$key] = $lineas;
        }

        return array(
            'master' => $master,
            'detail' => $detail,
        );
    }

    /**
     * Importar el contenido del archivo $filename, genera un archivo log
     * cuyo path devuelve.
     * 
     * Comprueba la existencias de los lotes y ubicaciones indicados
     * Si no existe el lote, lo crea.
     *
     * @param string $fileName
     * @return string El path al archivo log generado en la importación
     */
    private function importarArchivo($fileName) {

        set_time_limit(0);

        $archivoImportar = new Archivo($fileName);

        if ($archivoImportar->open("r")) {
            // Abrir en modo escritura el archivo de log.
            $usuario = new Agentes($_SESSION['USER']['user']['id']);
            $pathArchivoLog = "docs/docs" . $_SESSION['emp'] . "/tmp/logImportarInventario.txt";
            $archivoLog = new Archivo($pathArchivoLog);
            $archivoLog->open("w");
            $archivoLog->writeLine("IMPORTACIÓN DE ARCHIVO DE INVENTARIO");
            $archivoLog->writeLine("FECHA: " . date('d-m-Y H:i:s'));
            $archivoLog->writeLine("ARCHIVO: " . $archivoImportar->getBaseName());
            $archivoLog->writeLine("USUARIO: " . $usuario->getNombre());
            $archivoLog->writeLine(str_repeat("-", 50));
            unset($usuario);

            $fila = 1;
            $fallidos = 0;
            $cargados = 0;
            $primeraLinea = $archivoImportar->readLine();
            // Pongo los títulos con la primera letra en mayúscula y le doy
            // la vuelta al array
            $titulos = $primeraLinea;
            foreach ($titulos as $key => $value)
                $titulos[$key] = ucwords(trim($value));
            $titulos = array_flip($titulos);

            while (($linea = $archivoImportar->readLine()) !== FALSE) {
                $fila++;

                $lineaInventario = new InventariosLineas();
                $lineaInventario->setIDInventario($this->request['InventariosCab']['IDInventario']);

                // Buscar el id del articulo a partir del codigo
                $articulo = new Articulos();
                $articulo = $articulo->find("Codigo", trim($linea[$titulos['Codigo']]));
                $lineaInventario->setIDArticulo($articulo->getIDArticulo());

                // Buscar el id del lote a partir del nombre de lote
                $nombreLote = trim($linea[$titulos['Lote']]);
                $lote = new Lotes();
                $rows = $lote->cargaCondicion("IDLote","IDArticulo='{$articulo->getIDArticulo()}' and Lote='" . $nombreLote . "'");
                $idLote = $rows[0]['IDLote'];
                
                // Si no existe el lote, lo creo.
                if ( ($idLote == '') and ($nombreLote != '') ) {
                    $lote->setIDArticulo($articulo->getIDArticulo());
                    $lote->setLote($nombreLote);
                    $idLote = $lote->create();
                }
                $lineaInventario->setIDLote($idLote);

                // Buscar la id de la ubicacion del almacen en curso
                $ubicacion = new AlmacenesMapas();
                $rows = $ubicacion->cargaCondicion("IDUbicacion","IDAlmacen='{$this->request['InventariosCab']['IDAlmacen']}' and Ubicacion='" . trim($linea[$titulos['Ubicacion']]) . "'");
                $lineaInventario->setIDUbicacion($rows[0]['IDUbicacion']);

                // A los valores numéricos les cambio la coma decimal por el punto
                $lineaInventario->setStock(str_replace(",",".",$linea[$titulos['Stock']]));
                $lineaInventario->setCajas(str_replace(",",".",$linea[$titulos['Cajas']]));

                $lineaInventario->validaLogico();
                if (count($lineaInventario->getErrores()) == 0) {
                    $lineaInventario->create();
                    $cargados++;
                } else {
                    // Si hay errores de validacion muestro la linea y los errores
                    $fallidos++;
                    $archivoLog->writeLine("Error en línea: " . $fila);
                    foreach ($linea as $key => $value) {
                        $string = "\t" . $primeraLinea[$key] . " : " . $value;
                        $archivoLog->writeLine($string);
                    }
                    foreach ($lineaInventario->getErrores() as $error)
                        $archivoLog->writeLine("\t* " . $error);
                }
            }

            $archivoLog->writeLine("" . str_repeat("-", 50));
            $archivoLog->writeLine("Total registros   : " . ($fila-1));
            $archivoLog->writeLine("Registros cargados: " . $cargados);
            $archivoLog->writeLine("Registros fallidos: " . $fallidos);

            $archivoImportar->close();
            $archivoLog->close();
            unset($archivoLog);
        } else
            $resultado[] = "El archivo de importación no existe";

        unset($archivoImportar);

        return $pathArchivoLog;
    }

}

?>