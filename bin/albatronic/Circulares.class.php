<?php

/**
 * Description of Circulares
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 06-jun-2012
 *
 */
class Circulares extends Controller {

    protected $entity = "Circulares";
    protected $parentEntity = "";
    protected $request;
    protected $form;
    protected $listado;
    protected $permisos;
    protected $values;
    protected $circulares;
    protected $queryMaster;
    protected $queryDetail;
    protected $filter;
    protected $idCircular;
    protected $breakFields = array();
    protected $separadorOpen = "#";
    protected $separadorClose = "#";
    protected $separadorDetailOpen = "INICIODETALLE";
    protected $separadorDetailClose = "FINDETALLE";
    protected $pathPlantillas;
    protected $filePlantilla;
    protected $textoPlantilla;
    protected $pathCirculares;
    protected $tiposPermitidos = array("text/rtf", "text/html", "text/plain");

    public function __construct($request) {

        // Cargar lo que viene en el request
        $this->request = $request;

        // Cargar la configuracion del modulo (modules/moduloName/config.yaml)
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

        // Cargar los formatos de circulares
        $file = $_SERVER['DOCUMENT_ROOT'] . $_SESSION['appPath'] . "/docs/docs{$_SESSION['emp']}/circulares/circulares.yml";
        $existe = file_exists($file);
        if (!$existe) {
            $file = $_SERVER['DOCUMENT_ROOT'] . $_SESSION['appPath'] . "/docs/informes/circulares.yml";
            $existe = file_exists($file);
        }

        if ($existe) {
            $this->circulares = sfYaml::load($file);
            $this->circulares = $this->getFormatosCirculares();
            $this->values['circulares'] = $this->circulares;
        }

        // Cargar las plantillas de circulares      
        $this->pathPlantillas = $_SERVER['DOCUMENT_ROOT'] . $_SESSION['appPath'] . "/docs/docs{$_SESSION['emp']}/circulares/plantillas";
        $this->values['plantillas'] = Archivo::getDirectorios($this->pathPlantillas);

        if (!count($this->values['plantillas'])) {
            $this->pathPlantillas = $_SERVER['DOCUMENT_ROOT'] . $_SESSION['appPath'] . "/docs/plantillas";
            $this->values['plantillas'] = Archivo::getDirectorios($this->pathPlantillas);
        }

        $this->pathCirculares = "docs/docs" . $_SESSION['emp'] . "/circulares/circulares";

        // QUITAR LOS COMENTARIOS PARA Actualizar los favoritos para el usuario
        //if ($this->form->getFavouriteControl())
        //    $this->actualizaFavoritos();
    }

    public function IndexAction() {

        $this->values['archivos'] = Archivo::getDirectorios($this->pathCirculares);

        return array('template' => $this->entity . '/index.html.twig', 'values' => $this->values);
    }

    /**
     * Devuelve la configuracion del informe seleccionado
     * @param integer $idCircular El id del informe seleccionado
     * @return <type>
     */
    public function SelectAction() {

        if ($this->idCircular == '')
            $this->idCircular = $this->request[2];

        // Construir los Filtros
        $filtros = $this->getFilters($this->idCircular);
        foreach ($filtros as $key => $value) {
            if (($value['entity'] != '') and ($value['type'] == 'select')) {
                $claseConId = explode(',', $value['entity']);
                $objeto = new $claseConId[0]($claseConId[1]);
                $filtros[$key]['values'] = $objeto->{$value['method']}($value['params']);
            }
        }

        $this->values['circular'] = array(
            'idCircular' => $this->idCircular,
            'title' => $this->getTitle($this->idCircular),
            'comment' => $this->getComment($this->idCircular),
            'plantilla' => $this->getPantilla($this->idCircular),
            'formatos' => $this->getFormatos($this->idCircular),
            'archivosSeparados' => $this->getArchivosSeparados($this->idCircular),
            'filters' => $filtros,
        );

        return $this->IndexAction();
    }

    /**
     * Sube al servidor el archivo de plantilla a la carpeta
     * docs/docsXXX/circulares/plantillas.
     * 
     * Debe existir la carpeta docs/docsXXX/circulares.
     * 
     * @return type
     */
    public function SubirPlantillaAction() {

        $fichero = $_FILES['filePlantilla'];

        if (in_array($fichero['type'], $this->tiposPermitidos)) {
            $carpetaPlantillas = "docs/docs{$_SESSION['emp']}/circulares/plantillas/" . $fichero['name'];
            $archivo = new Archivo($carpetaPlantillas);

            if (!$archivo->upLoad($fichero))
                $this->values['errores'][] = $archivo->getErrores();
            unset($archivo);
        } else {
            $this->_errores[] = "Tipo de archivo no permitido. Sólo se admiten archivos rtf,txt y html";
        }

        $this->values['errores'] = $this->_errores;

        return $this->IndexAction();
    }

    /**
     * Borrar todas las circulares generadas
     * @return type
     */
    public function BorrarAction() {
        $archivos = Archivo::getDirectorios($this->pathCirculares);
        foreach ($archivos as $archivo) {
            unlink($this->pathCirculares . "/" . $archivo);
        }
        return $this->IndexAction();
    }

    /**
     * Lee el archivo de plantilla y mapea las variables indicadas
     * en la configuración de la plantilla y los definidas en la plantilla
     * 
     * @return type
     */
    public function MapearAction() {

        $this->idCircular = $this->request['idCircular'];
        $this->filePlantilla = $this->pathPlantillas . "/" . $this->request['plantilla'];

        $formato = $this->circulares[$this->idCircular];

        $mapeo = array();

        foreach ($this->circulares[$this->idCircular]['columnsMaster'] as $columna) {
            $mapeo[$columna['title']] = $columna['field'];
        }
        foreach ($this->circulares[$this->idCircular]['columnsDetail'] as $columna) {
            $mapeo[$columna['title']] = $columna['field'];
        }

        $variablesDocumento = $this->getVariablesDocumento($this->filePlantilla);
        $variables = array_merge($variablesDocumento, $mapeo);

        foreach ($variables as $variable => $valor) {
            if ($valor == '') {
                $libres[$variable] = null;
            }
        }

        // guardar en el yml el archivo de plantilla seleccionado asociándolo al tipo de circular
        $this->circulares[$this->idCircular]['plantilla'] = $this->request['plantilla'];
        $yml = sfYaml::dump(array('circulares' => $this->circulares), 4);
        $carpeta = "docs/docs{$_SESSION['emp']}/circulares";
        if (!file_exists($carpeta)) {
            mkdir($carpeta);
        }
        $file = $carpeta . "/circulares.yml";
        $archivo = new Archivo($file);
        $archivo->write($yml);
        unset($archivo);

        $this->values['variables'] = array('disponibles' => $mapeo, 'libres' => $libres);

        return $this->SelectAction($this->idCircular);
    }

    public function GenerarAction() {

        if ($this->values['permisos']['L']) {

            $dataBase = "interpra_ppuerp" . $_SESSION['emp'];

            $this->idCircular = $this->request['idCircular'];
            $this->filePlantilla = $this->pathPlantillas . "/" . $this->request['plantilla'];
            $formato = $this->circulares[$this->idCircular];

            // Leer el contenido de la plantilla
            $archivo = new Archivo($this->filePlantilla);
            $this->textoPlantilla = $archivo->read("rb");
            unset($archivo);

            // HACER EL QUERY MASTER
            $this->queryMaster = $this->circulares[$this->idCircular]['queryMaster'];
            // Reemplazar en el query los valores del filtro
            $this->filter = $this->request['filter'];
            foreach ($this->filter['columnsSelected'] as $key => $value) {
                $valor = $this->filter['valuesSelected'][$key];
                if ($formato['filters'][$key]['type'] == 'date') {
                    $fecha = new Fecha($valor);
                    $valor = $fecha->getaaaammdd();
                    unset($fecha);
                }
                $this->queryMaster = str_replace($value, $valor, $this->queryMaster);
            }
            $this->queryMaster = str_replace("DBNAME", $dataBase, $this->queryMaster);
            $em = new EntityManager("datos" . $_SESSION['emp']);
            if ($em->getDbLink()) {
                $em->query($this->queryMaster);
                $master = $em->fetchResult();
            }
            unset($em);

            // HACER EL QUERY DETAIL
            $this->queryDetail = $this->circulares[$this->idCircular]['queryDetail'];
            if ($this->queryDetail) {
                // Reemplazar en el query los valores del filtro
                $this->filter = $this->request['filter'];
                foreach ($this->filter['columnsSelected'] as $key => $value) {
                    $valor = $this->filter['valuesSelected'][$key];
                    if ($formato['filters'][$key]['type'] == 'date') {
                        $fecha = new Fecha($valor);
                        $valor = $fecha->getaaaammdd();
                        unset($fecha);
                    }
                    $this->queryDetail = str_replace($value, $valor, $this->queryDetail);
                }
                $this->queryDetail = str_replace("DBNAME", $dataBase, $this->queryDetail);
            }

            if (!file_exists($this->pathCirculares)) {
                mkdir($this->pathCirculares);
            }
            foreach ($master as $key => $registro) {
                $texto = $this->SustituyeValores($registro, $this->request['variables']);
                $archivo = $this->pathCirculares . "/" . $_SESSION['USER']['user']['id'] . "_" . $key . "_" . $this->request['plantilla'];
                $archivo = new Archivo($archivo);
                if (!$archivo->write($texto)) {
                    $this->values['errores'] = "No se ha generado {$archivo}";
                }
            }

            $this->generarZip();

            return $this->IndexAction();
        } else {
            $template = "_global/forbiden.html.twig";
        }

        return array('template' => $template, 'values' => $this->values);
    }

    private function generarZip() {

        $zip = new ZipArchive();
        $ret = $zip->open($this->pathCirculares . "/circulares{$_SESSION['USER']['user']['id']}.zip", ZipArchive::OVERWRITE);
        if ($ret !== TRUE) {
            printf('Erróneo con el código %d', $ret);
        } else {
            $options = array('add_path' => "/", 'remove_all_path' => TRUE);
            $ok = $zip->addGlob("{$this->pathCirculares}/{$_SESSION['USER']['user']['id']}_*.rtf", GLOB_BRACE, $options);
            $zip->close();

            if ($ok) {
                // Borrar los archivos
                foreach (glob("{$this->pathCirculares}/{$_SESSION['USER']['user']['id']}_*.rtf") as $nombreArchivo) {
                    @unlink($nombreArchivo);
                }
            }
        }
    }

    private function SustituyeValores($master, $variables) {

        $documento = $this->textoPlantilla;

        if ($this->queryDetail) {
            $linkValue = $master[$this->circulares[$this->idCircular]['linkColumn']];
            $query = str_replace("LINKVALUE", $linkValue, $this->queryDetail);
            $em = new EntityManager("datos" . $_SESSION['emp']);
            if ($em->getDbLink()) {
                $em->query($query);
                $detail = $em->fetchResult();
            }
            unset($em);

            // Buscar el bloque de detalle
            $inicio = stristr($this->textoPlantilla, $this->separadorDetailOpen);
            $bloqueDetail = stristr($inicio, $this->separadorDetailClose, true);
            $bloqueDetail = str_replace($this->separadorDetailOpen, "", $bloqueDetail);
            $bloqueDetailEnmarcado = $this->separadorDetailOpen . $bloqueDetail . $this->separadorDetailClose;

            $textoDetail = "";
            foreach ($detail as $registro) {
                $registroDetail = $bloqueDetail;
                foreach ($variables['db'] as $key => $value) {
                    $registroDetail = str_replace($this->separadorOpen . $key . $this->separadorClose, utf8_decode($registro[$value]), $registroDetail);
                }
                if (is_array($variables['libres'])) {
                    foreach ($variables['libres'] as $key => $value) {
                        $registroDetail = str_replace($this->separadorOpen . $key . $this->separadorClose, $value, $registroDetail);
                    }
                }
                $textoDetail .= $registroDetail;
            }
            //echo $textoDetail, "<br/>";
            $documento = str_replace($bloqueDetailEnmarcado, $textoDetail, $documento);
        }

        foreach ($variables['db'] as $key => $value) {
            $documento = str_replace($this->separadorOpen . $key . $this->separadorClose, utf8_decode($master[$value]), $documento);
        }

        if (is_array($variables['libres'])) {
            foreach ($variables['libres'] as $key => $value) {
                $documento = str_replace($this->separadorOpen . $key . $this->separadorClose, $value, $documento);
            }
        }

        return $documento;
    }

    /**
     * Devuelve un array con los parámetros de configuración de TODOS los informes
     * a los que TIENE ACCESO el id de perfil del usuario en curso
     *
     * @return array
     */
    private function getFormatosCirculares() {
        $formatos = array();

        if (is_array($this->circulares['circulares'])) {
            $perfilUsuario = $_SESSION['usuarioPortal']['IdPerfil'];
            foreach ($this->circulares['circulares'] as $value) {
                $perfiles = (string) $value['idPerfil'];
                $arrayPerfiles = explode(',', $perfiles);
                if (($perfiles == '') or (in_array($perfilUsuario, $arrayPerfiles))) {
                    $formatos[] = $value;
                }
            }
        }

        return $formatos;
    }

    public function getTitle($idCircular) {
        return $this->circulares[$idCircular]['title'];
    }

    public function getComment($idCircular) {
        return $this->circulares[$idCircular]['comment'];
    }

    public function getPantilla($idCircular) {
        return $this->circulares[$idCircular]['plantilla'];
    }

    public function getFormatos($idCircular) {
        return $this->circulares[$idCircular]['formatos'];
    }

    public function getArchivosSeparados($idCircular) {
        return $this->circulares[$idCircular]['archivosSeparados'];
    }

    /**
     * Obtiene los filtros definidos en la plantilla
     * @param type $idCircular
     * @return string
     */
    public function getFilters($idCircular) {
        $filters = array();

        if (is_array($this->circulares[$idCircular]['filters'])) {
            foreach ($this->circulares[$idCircular]['filters'] as $index => $filter) {
                $type = strtolower(trim((string) $filter['type']));
                if (!$type)
                    $type = "input";
                if (($type != 'input') and ($type != 'select') and ($type != 'check') and ($type != 'date'))
                    $type = 'input';
                $event = trim((string) $filter['event']);
                $default = trim((string) $filter['default']);
                $operator = trim((string) $filter['operator']);
                if ($operator == '')
                    $operator = '=';

                $filters[$index]['field'] = trim((string) $filter['field']);
                $filters[$index]['caption'] = trim((string) $filter['caption']);
                $filters[$index]['entity'] = trim((string) $filter['entity']);
                $filters[$index]['method'] = trim((string) $filter['method']);
                $filters[$index]['params'] = trim((string) $filter['params']);
                $filters[$index]['type'] = $type;
                $filters[$index]['event'] = $event;
                $filters[$index]['default'] = $default;
                $filters[$index]['operator'] = $operator;
            }
        }

        return $filters;
    }

    /**
     * Obtiene las variables difinidas en el documento de plantilla
     * Las variables debe están envueltas en el carácter seperador
     * definido en $this->seperador
     * 
     * @param string $texto El contenido de la plantilla
     * @param integer $puntero El puntero a partir del que se realiza la búsqueda
     * @return array Array con dos elementos, la variable y su posición dentro del documento
     */
    private function getVariable($texto, $puntero) {

        //Busco el primer separador
        $i = strpos($texto, $this->separadorOpen, $puntero);
        if ($i === false) {
            //No lo encuentra
            return(array('', -1));
        }
        $i = $i + strlen($this->separadorOpen);

        //Busco el segundo separador
        $f = strpos($texto, $this->separadorClose, $i);
        if ($f === false) {
            //No lo encuentra
            return(array('', -1));
        }

        // La variable está entre los dos separadores
        $variable = trim(substr($texto, $i, $f - $i));
        $f += strlen($this->separadorClose);
        return(array($variable, $f));
    }

    /**
     * Devuelve un array con el nombre de las variables definidas en el documento
     * Las variables deben estar envueltas por el caracter indicado en $this->separador
     * 
     * @param string $archivoPlantilla El nombre del archivo de la plantilla
     * @return array Array cuyo índices son los nombres de la variables encontradas en el documento
     */
    private function getVariablesDocumento($archivoPlantilla) {

        $archivo = new Archivo($archivoPlantilla);
        $this->textoPlantilla = $archivo->read("rb");
        unset($archivo);
        $largo = strlen($this->textoPlantilla);
        $i = 0;
        $array = array();

        while (($i < $largo) and ($i >= 0)) {
            list($variable, $i) = $this->getVariable($this->textoPlantilla, $i);
            if ($i > 0) {
                //$sql = "select Columna from variables where IDVariable='$variable'";
                //$res = mysql_query($sql);
                //$row = mysql_fetch_array($res);
                $array[$variable] = ""; //$row['Columna'];
            }
        }
        return $array;
    }

    /**
     * Genera un archivo pdf con el listado
     * @param array $parametros Array con los parámetros de configuración del listado
     * @param string $aditionalFilter
     * @return string $archivo El nombre completo (con la ruta) del archivo pdf generado
     */
    public function getPdf($parametros, $aditionalFilter = '') {

        set_time_limit(0);

        // Orientación de página, unidad de medida y tipo de papel
        $orientation = strtoupper(trim($parametros['orientation']));
        if (($orientation != 'P') and ($orientation != 'L'))
            $orientation = 'P';
        $unit = strtolower(trim($parametros['unit']));
        if (($unit != 'pt') and ($unit != 'mm') and ($unit != 'cm') and ($unit != 'in'))
            $unit = 'mm';
        $format = strtolower(trim($parametros['format']));
        if (($format != 'a4') and ($format != 'a3') and ($format != 'a5') and ($format != 'letter') and ($format != 'legal'))
            $format = 'A4';

        // Márgenes: top,right,bottom,left
        $margenes = explode(',', trim($parametros['margins']));
        if (count($margenes) != 4)
            $margenes = array('10', '10', '15', '10');

        // Tipo y tamaño de letra para el cuerpo del listado
        $bodyFont = explode(',', trim($parametros['body_font']));
        if (count($bodyFont) != 3)
            $bodyFont = array('Courier', '', '8');
        else {
            $bodyFont[0] = trim($bodyFont[0]);
            $bodyFont[1] = trim($bodyFont[1]);
            $bodyFont[2] = trim($bodyFont[2]);
        }

        // Altura de la línea. Por defecto 4 mm.
        $lineHeight = trim($parametros['line_height']);
        if ($lineHeight <= 0)
            $lineHeight = 4;

        // Construir la leyenda del filtro
        $leyendaFiltro = array();

        if (is_array($this->filter['columnsSelected'])) {
            foreach ($this->filter['columnsSelected'] as $key => $column) {
                if ($this->filter['valuesSelected'][$key] != '') {
                    $entidad = $this->circulares[$this->idCircular]['filters'][$key]['entity'];
                    if ($entidad) {
                        $aux = explode(",", $entidad);
                        $entidad = $aux[0];
                        $idEntidad = $this->filter['valuesSelected'][$key];
                        $objeto = new $entidad($idEntidad);
                        $valor = $objeto->__toString();
                    } else
                        $valor = $this->filter['valuesSelected'][$key];

                    $leyendaFiltro[] = array('Column' => $parametros['filters'][$key]['caption'], 'Value' => $valor);
                }
            }
        }

        // CREAR EL DOCUMENTO
        $pdf = new ListadoPDF(
                $orientation, $unit, $format, array(
            'title' => $parametros['title'],
            'titleFont' => $bodyFont,
            'columns' => $parametros['columns'],
            'leyendaFiltro' => $leyendaFiltro,
                )
        );
        $pdf->SetTopMargin($margenes[0]);
        $pdf->SetRightMargin($margenes[1]);
        $pdf->SetLeftMargin($margenes[3]);
        $pdf->SetAuthor("Informatica ALBATRONIC, SL");
        $pdf->SetTitle($parametros['title']);
        $pdf->AliasNbPages();
        $pdf->SetFillColor(210);
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, $margenes[2]);

        // CUERPO
        $pdf->SetFont($bodyFont[0], $bodyFont[1], $bodyFont[2]);

        $em = new EntityManager($_SESSION['project']['conection']);
        $em->query($this->query);
        $rows = $em->fetchResult();
        $nRegistros = $em->numRows();
        $em->desConecta();
        unset($em);

        $breakPage = ( strtoupper(trim((string) $parametros['break_page'])) == 'YES' );

        // ----------------------------------------------
        // Cargo la configuración de la línea del listado
        // En el array $columnasMulticell guardo el nombre de los
        // campos que se imprimirán en Multicell y su anchura en la unidad de medida
        // establecida para calcular la altura máxima y controlar el salto de página
        // ----------------------------------------------
        $configLinea = array();
        $columnsMulticell = array();
        $caracteresLinea = 0;
        foreach ($parametros['columns'] as $key => $value) {
            $caracteres = (int) $value['length'];
            $anchoColumna = $pdf->getStringWidth(str_pad(" ", $caracteres)) + 1; //Le sumo 1 para que haya 1 mm de separación entre cada columna
            $caracteresLinea += $caracteres;
            $tipo = trim((string) $value['type']);
            $align = strtoupper(trim((string) $value['align']));
            if (($align != 'R') and ($align != 'C') and ($align != 'L') and ($align != 'J'))
                $align = "L";
            $formato = trim((string) $value['format']);
            $total = ( strtoupper(trim((string) $value['total'])) == 'YES' );

            $configLinea[$value['field']] = array(
                'field' => $value['field'],
                'caracteres' => $caracteres,
                'ancho' => $anchoColumna,
                'align' => $align,
                'formato' => $formato,
                'type' => $tipo,
                'total' => $total,
            );
            if ($tipo == "text")
                $columnsMulticell[] = array('field' => $value['field'], 'width' => $anchoColumna);
        }
        // -----------------

        $valorAnterior = array();
        $subtotalRegistros = 0;

        // Itero el array con los datos para generar cada renglón del listado
        $totales = array();
        $subTotales = array();
        foreach ($rows as $row) {

            $subtotalRegistros++;

            // Control (si se ha definido) del(los) campo(s) de ruptura
            if (count($this->breakFields)) {
                // Recorro en orden inverso el array de campos de ruptura para
                // comprobar si ha cambiado el valor actual respecto al anterior.
                for ($i = 0; $i < count($this->breakFields); $i++) {
                    //for ($i = count($breakField)-1; $i >= 0 ; $i--) {
                    $columnaRuptura = $this->breakFields[$i];
                    $valorActual[$columnaRuptura] = $row[$columnaRuptura];
                    if ($valorAnterior[$columnaRuptura] != $valorActual[$columnaRuptura]) {
                        if ($valorAnterior[$columnaRuptura] != '') {
                            $this->pintaTotales($pdf, $parametros['columns'], $subTotales);
                            $subTotales = array();
                            // Pinta el subtotal de registos
                            if ($parametros['print_total_records']) {
                                $pdf->Cell(0, 4, 'Subtotal Registos ' . $subtotalRegistros, 0, 1);
                                $subtotalRegistros = 0;
                            }
                            // Cambio de página si procede
                            if ($breakPage)
                                $pdf->AddPage();
                        }
                        // Pinto el valor del campo de ruptura
                        $pdf->SetFont($bodyFont[0], 'B', $bodyFont[2]);
                        $pdf->Cell(0, 10, $valorActual[$columnaRuptura]);
                        $pdf->Ln();
                        $pdf->SetFont($bodyFont[0], $bodyFont[1], $bodyFont[2]);
                    }
                    $valorAnterior[$columnaRuptura] = $valorActual[$columnaRuptura];
                }
            }

            $pdf->CheckLinePageBreak($lineHeight, $row, $columnsMulticell);

            // Coordenadas X e Y del renglón
            $x0 = $pdf->GetX();
            $y0 = $pdf->GetY();
            // Para controlar el desplazamiento vertical de los multicell
            $y1 = 0;

            // Recorro las columnas que componen cada renglón
            foreach ($configLinea as $value) {

                $texto = trim($row[$value['field']]);
                if ($value['formato']) {
                    if ($value['type'] == 'money')
                        $texto = money_format($value['formato'], $texto);
                    else
                        $texto = sprintf($value['formato'], $texto);
                }

                if ($value['type'] == 'text') {
                    // Pinto un multicell sin recortar el texto
                    $x = $pdf->GetX() + $value['ancho'];
                    $pdf->MultiCell($value['ancho'], $lineHeight, $texto, 0, $value['align']);
                    if ($pdf->GetY() > $y1)
                        $y1 = $pdf->GetY();
                    $pdf->SetXY($x, $y0);
                } else {
                    // Pinto una celda normal
                    $pdf->Cell($value['ancho'], $lineHeight, $pdf->DecodificaTexto($texto, $value['caracteres']), 0, 0, $value['align']);
                }

                // Calcular Eventuales totales y subtotales de cada columna
                if ($value['total']) {
                    $totales[(string) $value['field']] += (float) trim($row[$value['field']]);
                    $subTotales[(string) $value['field']] += (float) trim($row[$value['field']]);
                }
            }
            // Si ha habido algun multicell, cambio la coordenada Y
            // al desplazamiento vertical mayor producido ($y1)
            if ($y1 != 0)
                $pdf->SetXY($margenes[3], $y1);
            else
                $pdf->Ln();

            // Si se ha definido interlinea, se imprime a todo lo ancho
            if ($parametros['print_interline'])
                $pdf->Cell(0, $lineHeight, str_repeat($parametros['print_interline'], $caracteresLinea + 5), 0, 1);
        }
        unset($objeto);

        // Pintar los subtotales y totales si hay
        if (count($totales)) {
            if (count($this->breakFields)) {
                $this->pintaTotales($pdf, $parametros['columns'], $subTotales);
            }
            $pdf->Ln();
            $this->pintaTotales($pdf, $parametros['columns'], $totales);
        } elseif (count($this->breakFields)) {
            // Pinta el subtotal de registos
            $pdf->Cell(0, 4, 'Subtotal Registos ' . $subtotalRegistros, 0, 1);
        }

        if ($parametros['print_total_records']) {
            if (count($this->breakFields)) {
                // Pinta el subtotal de registos
                //$subtotalRegistros++;
                $pdf->Cell(0, 4, 'Subtotal Registos ' . $subtotalRegistros, 0, 1);
            }

            // Total de registros impresos
            $pdf->Ln();
            $pdf->SetFont('Arial', 'B', '8');
            $pdf->Cell(0, 4, "Total Registros: " . $nRegistros);
        }

        // Pintar el gráfico
        if (is_array($parametros['grafico']))
            $this->pintaGrafico($pdf, $query, $parametros);

        // Leyenda a pie de la última página
        if ($parametros['comment']) {
            $pdf->SetY(-25);
            $pdf->Write(4, $parametros['comment']);
        }

        $fichero = Archivo::getTemporalFileName();
        if ($fichero)
            $pdf->Output($fichero, 'F');

        unset($objeto);
        unset($pdf);

        return $fichero;
    }

}

class ListadoPDF extends FPDF {

    //Cabecera de página
    function Header() {
        $empresa = new PcaeEmpresas($_SESSION['emp']);
        $sucursal = new Sucursales($_SESSION['suc']);

        $this->Image($empresa->getLogo(), 10, 8, 23);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, $empresa->getRazonSocial(), 0, 1, "R");
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 5, $sucursal->getNombre(), 0, 1, "R");
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, $this->opciones['title'], 0, 1, "C");

        // Pintar la leyenda del filtro en la primera página
        if ($this->page == 1) {
            $this->Ln(5);
            $this->SetFont('Arial', '', '8');
            $this->Line($this->GetX(), $this->GetY(), $this->getPrintablePageWidth(), $this->GetY());
            foreach ($this->opciones['leyendaFiltro'] as $filtro) {
                $this->Cell(20, 4, $filtro['Column'], 0, 0);
                $this->Cell(0, 4, $filtro['Value'], 0, 1);
            }
            $this->Line($this->GetX(), $this->GetY(), $this->getPrintablePageWidth(), $this->GetY());
        }

        // Para los títulos pongo el mismo font que para el cuerpo del listado
        $this->Ln(5);
        $this->SetFont($this->opciones['titleFont'][0], $this->opciones['titleFont'][1], $this->opciones['titleFont'][2]);
        //PINTAR LOS TITULOS DE LAS COLUMNAS
        foreach ($this->opciones['columns'] as $value) {
            $caracteres = (integer) $value['length'];
            $texto = trim((string) $value['title']);
            $ancho = $this->getStringWidth(str_pad(" ", $caracteres)) + 1;
            $this->Cell($ancho, 4, $this->DecodificaTexto($texto, $caracteres), 0, 0, "C", 1);
        }
        $this->Ln();
        //$this->Line($this->GetX(), $this->GetY(), $this->GetX() + 190, $this->GetY());
    }

}

if (!function_exists('money_format')) {

    function money_format($format, $number) {
        $regex = array('/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?(?:#([0-9]+))?',
            '(?:\.([0-9]+))?([in%])/'
        );
        $regex = implode('', $regex);
        if (setlocale(LC_MONETARY, null) == '') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        $number = floatval($number);
        if (!preg_match($regex, $format, $fmatch)) {
            trigger_error("No format specified or invalid format", E_USER_WARNING);
            return $number;
        }
        $flags = array('fillchar' => preg_match('/\=(.)/', $fmatch[1], $match) ? $match[1] : ' ',
            'nogroup' => preg_match('/\^/', $fmatch[1]) > 0,
            'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ? $match[0] : '+',
            'nosimbol' => preg_match('/\!/', $fmatch[1]) > 0,
            'isleft' => preg_match('/\-/', $fmatch[1]) > 0
        );
        $width = trim($fmatch[2]) ? (int) $fmatch[2] : 0;
        $left = trim($fmatch[3]) ? (int) $fmatch[3] : 0;
        $right = trim($fmatch[4]) ? (int) $fmatch[4] : $locale['int_frac_digits'];
        $conversion = $fmatch[5];
        $positive = true;
        if ($number < 0) {
            $positive = false;
            $number *= - 1;
        }
        $letter = $positive ? 'p' : 'n';
        $prefix = $suffix = $cprefix = $csuffix = $signal = '';
        if (!$positive) {
            $signal = $locale['negative_sign'];
            switch (true) {
                case $locale['n_sign_posn'] == 0 || $flags['usesignal'] == '(':
                    $prefix = '(';
                    $suffix = ')';
                    break;
                case $locale['n_sign_posn'] == 1:
                    $prefix = $signal;
                    break;
                case $locale['n_sign_posn'] == 2:
                    $suffix = $signal;
                    break;
                case $locale['n_sign_posn'] == 3:
                    $cprefix = $signal;
                    break;
                case $locale['n_sign_posn'] == 4:
                    $csuffix = $signal;
                    break;
            }
        }
        if (!$flags['nosimbol']) {
            $currency = $cprefix;
            $currency .= ( $conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']);
            $currency .= $csuffix;
            $currency = iconv('ISO-8859-1', 'UTF-8', $currency);
        } else {
            $currency = '';
        }
        $space = $locale["{$letter}_sep_by_space"] ? ' ' : '';

        $number = number_format($number, $right, $locale['mon_decimal_point'], $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
        $number = explode($locale['mon_decimal_point'], $number);

        $n = strlen($prefix) + strlen($currency);
        if ($left > 0 && $left > $n) {
            if ($flags['isleft']) {
                $number[0] .= str_repeat($flags['fillchar'], $left - $n);
            } else {
                $number[0] = str_repeat($flags['fillchar'], $left - $n) . $number[0];
            }
        }
        $number = implode($locale['mon_decimal_point'], $number);
        if ($locale["{$letter}_cs_precedes"]) {
            $number = $prefix . $currency . $space . $number . $suffix;
        } else {
            $number = $prefix . $number . $space . $currency . $suffix;
        }
        if ($width > 0) {
            $number = str_pad($number, $width, $flags['fillchar'], $flags['isleft'] ? STR_PAD_RIGHT : STR_PAD_LEFT);
        }
        $format = str_replace($fmatch[0], $number, $format);
        return $format;
    }

//	function money_format()
}
?>
