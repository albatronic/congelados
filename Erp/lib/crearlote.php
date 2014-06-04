<?php

/**
 * INSERTA UN LOTE NUEVO EN LA TABLA DE LOTES
 * Y CREA UN TAG <SELECT> CON LOS LOTES DEL ARTICULO INDICADO
 * INCLUYENDO EL QUE ACABA DE CREAR.
 *
 * SE HACEN VALIDACIONES EN CUANTO A LA IDONEIDAD DE LAS FECHAS
 *
 * LOS PARAMETROS POST QUE RECIBE SON:
 *
 * idArticulo   -> EL ID DEL ARTICULO
 * lote         -> EL TEXTO DEL LOTE
 * fFabricacion -> LA FECHA DE FABRICACION
 * fCaducidad   -> LA FECHA DE CADUCIDAD
 *
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright Informatica ALBATRONIC
 * @since 27.05.2011
 */

session_start();

if (!file_exists('../config/config.yml')) {
    echo "NO EXISTE EL FICHERO DE CONFIGURACION";
    exit;
}

if (file_exists("../../bin/yaml/lib/sfYaml.php")) {
    include "../../bin/yaml/lib/sfYaml.php";
} else {
    echo "NO EXISTE LA CLASE PARA LEER ARCHIVOS YAML";
    exit;
}

// ---------------------------------------------------------------
// CARGO LOS PARAMETROS DE CONFIGURACION.
// ---------------------------------------------------------------
$config = sfYaml::load('../config/config.yml');
$app = $config['config']['app'];

// ---------------------------------------------------------------
// ACTIVAR EL AUTOLOADER DE CLASES Y FICHEROS A INCLUIR
// ---------------------------------------------------------------
define(APP_PATH, $_SERVER['DOCUMENT_ROOT'] . $app['path'] . "/");
include_once "../" . $app['framework'] . "Autoloader.class.php";
Autoloader::setCacheFilePath(APP_PATH . 'tmp/class_path_cache.txt');
Autoloader::excludeFolderNamesMatchingRegex('/^CVS|\..*$/');
Autoloader::setClassPaths(array(
    '../' . $app['framework'],
    '../entities/',
    '../lib/',
));
spl_autoload_register(array('Autoloader', 'loadClass'));


// Crear el lote nuevo
$formulario = new Form('Lotes');
$rules = $formulario->getRules();
unset($formulario);

$lote = new Lotes();
$lote->setIDArticulo($_GET['idArticulo']);
$lote->setLote($_GET['lote']);
$lote->setFechaFabricacion($_GET['fFabricacion']);
$lote->setFechaCaducidad($_GET['fCaducidad']);
if ($lote->valida($rules))
    $idLote = $lote->create();
$errores = $lote->getErrores();

if (count($errores))
    foreach ($errores as $error) {
        $tag .= $error . "\n";
    }

$rows = $lote->fetchAll($_GET['idArticulo']);

if (!$_GET['width'])
    $_GET['width'] = 100;

$tag .= "<select name='{$_GET['nameSelect']}' id='{$_GET['idSelect']}' class='Select' style='width:{$_GET['width']}px;'>\n";
$tag .= "<option value=''>:: Lote</option>\n";
foreach ($rows as $row) {
    if ($idLote == $row['Id'])
        $selected = "selected";
    else
        $selected = "";
    $tag .= "<option value='" . $row['Id'] . "' " . $selected . " >" . $row['Value'] . "</option>\n";
}
$tag .= "</select>\n";

unset($lote);

// DEVUELVE EL <SELECT> CREADO
echo $tag;
?>
