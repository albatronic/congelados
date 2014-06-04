<?php

/**
 * CONTROLADOR FRONTAL. Recibe todas las peticiones y renderiza el resultado
 *
 * UTILIZA URLS AMIGABLES. DEBE EXISTIR UN '.htaccess' EN EL DIRECTORIO
 * RAIZ DEL SITIO WEB CON UNA REGLA QUE DERIVA TODAS LAS PETICIONES
 * A ESTE SCRIPT (CONTROLADOR FRONTAL)
 *
 * EJEMPLO .htaccess:
 * <IfModule mod_rewrite.c>
 *   RewriteEngine On
 *   RewriteCond %{REQUEST_FILENAME} !-f
 *   RewriteRule ^(.*)$ index.php [QSA,L]
 * </IfModule>
 *
 * LAS PETICIONES DEBEN SER EN EL FORMATO:
 * http://www.sitioweb.com/apppath/controller/action/resto de valores...
 *
 * El apppath puede estar compuesto de varios subcarpetas. Ej:
 * http://www.sitioweb.com/apps/gestion/controller/action/ resto de valores...
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informatica ALBATRONIC
 * @date 27.05.2011
 */
session_start();

if (!file_exists('config/config.yml'))
    die("NO EXISTE EL FICHERO DE CONFIGURACION");

if (file_exists("bin/yaml/lib/sfYaml.php"))
    include "bin/yaml/lib/sfYaml.php";
else
    die("NO EXISTE LA CLASE PARA LEER ARCHIVOS YAML");

// ---------------------------------------------------------------
// CARGO LOS PARAMETROS DE CONFIGURACION.
// ---------------------------------------------------------------
$yaml = sfYaml::load('config/config.yml');
$config = $yaml['config'];

$_SESSION['audit'] = $config['audit_mode'];

$app = $config['app'];
$app['audit'] = $_SESSION['audit'];

$_SESSION['appPath'] = $app['path'];

// ---------------------------------------------------------------
// ACTIVAR EL AUTOLOADER DE CLASES Y FICHEROS A INCLUIR
// ---------------------------------------------------------------
define(APP_PATH, $_SERVER['DOCUMENT_ROOT'] . $app['path'] ."/");
include_once $app['framework'] . "Autoloader.class.php";
Autoloader::setCacheFilePath(APP_PATH . 'tmp/class_path_cache.txt');
Autoloader::excludeFolderNamesMatchingRegex('/^CVS|\..*$/');
Autoloader::setClassPaths(array(
            $app['framework'],
            'entities/',
            'lib/',
        ));
spl_autoload_register(array('Autoloader', 'loadClass'));

// ---------------------------------------------------------------
// ACTIVAR EL IDIOMA.
// ---------------------------------------------------------------
/**
include_once "lang/languages.php";
if ($_SESSION['IDIOMA'] == '') {
    $idioma = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    if ((file_exists('lang/' . $idioma . '.php')) and (in_array($idioma, $_SESSION['IDIOMAS']))) {
        $_SESSION['IDIOMA'] = $idioma;
    } else
        $_SESSION['IDIOMA'] = 'es';

    include_once "lang/" . $_SESSION['IDIOMA'] . ".php";
}
*/
//----------------------------------------------------------------
// ACTIVAR EL MOTOR DE PDF'S
// ---------------------------------------------------------------
if (file_exists($config['pdf']))
    include_once $config['pdf'];
else
    die("NO SE PUEDE ENCONTRAR EL MOTOR PDF");

//----------------------------------------------------------------
// ACTIVAR EL MOTOR TWIG PARA LOS TEMPLATES.
//----------------------------------------------------------------
if (file_exists($config['twig']['motor'])) {
    include_once $config['twig']['motor'];
    Twig_Autoloader::register();

    $cache = $config['twig']['cache_folder'];
    if ($cache != '')
        $ops['cache'] = $cache;
    $debug = $config['twig']['debug_mode'];
    if ($debug != '')
        $ops['debug'] = $debug;
    $charset = $config['twig']['charset'];
    if ($charset != '')
        $ops['charset'] = $charset;
    $ops['autoescape'] = true;
    $loader = new Twig_Loader_Filesystem($config['twig']['templates_folder']);
    $twig = new Twig_Environment($loader, $ops);
} else
    die("NO SE PUEDE ENCONTRAR EL MOTOR TWIG");

//-----------------------------------------------------------------
// INSTANCIAR UN OBJETO DE LA CLASE REQUEST PARA TENER DISPONIBLES
// TODOS LOS VALORES QUE CONSTITUYEN LA PETICION E IDENTIFICAR
// SI LA PETICION ES 'GET' O 'POST', ASI COMO EL CONTROLADOR Y
// ACCION SOLICITADA.
//-----------------------------------------------------------------
$rq = new Request();
switch ($rq->getMethod()) {
    case 'GET':
        $request = $rq->getParameters($app['path']);
        $request['METHOD'] = "GET";
        $controller = ucfirst($request[0]);
        $action = $request[1];
        break;

    case 'POST':
        $request = $rq->getRequest();
        $request['METHOD'] = "POST";
        $controller = ucfirst($request['controller']);
        $action = $request['action'];
        break;
}

// Si en la peticion no viene controller ni action, las pongo a 'Index'
if ($controller == '')
    $controller = 'Index';
if ($action == '')
    $action = "Index";


// Si no existe el controller lo pongo a 'Index'
$fileController = "modules/" . $controller . "/" . $controller . "Controller.class.php";
if (!file_exists($fileController)) {
    $controller = "Index";
    $fileController = "modules/Index/IndexController.class.php";
}

$clase = $controller . "Controller";
$metodo = $action . "Action";

//---------------------------------------------------------------
// INSTANCIAR EL CONTROLLER REQUERIDO
// SI EL METODO SOLICITADO EXISTE, LO EJECUTO, SI NO EJECUTO EL METODO INDEX
// RENDERIZAR EL RESULTADO CON EL TEMPLATE Y DATOS DEVUELTOS
// SI NO EXISTE EL TEMPLATE DEVUELTO, MUESTRO UNA PAGINA DE ERROR
//---------------------------------------------------------------
include_once $fileController;
$con = new $clase($request);
if (!method_exists($con, $metodo))
    $metodo = "IndexAction";
$result = $con->{$metodo}();

$result['values']['controller'] = $controller;

// Cargo los valores para el modo debuger
if ($config['debug_mode']) {
    $result['values']['_debugMode'] = true;
    $result['values']['_auditMode'] = (string) $config['audit_mode'];
    $result['values']['_session'] = print_r(array('emp' => $_SESSION['emp'], 'suc' => $_SESSION['suc'],), true);
    $result['values']['_user'] = print_r($_SESSION['USER'], true);
    $result['values']['_debugValues'] = print_r($result['values'], true);
}

// Si el método no devuelve template o no exite, muestro un template de error.
if (!file_exists($config['twig']['templates_folder'] . '/' . $result['template']) or ($result['template'] == '')) {
    $result['values']['error'] = 'No existe el template: "' . $result['template'] . '" devuelto por el método "' . $clase . ':' . $action . 'Action"';
    $result['template'] = '_global/error.html.twig';
}

// Renderizo el template y los valores devuelte por el método
$twig->loadTemplate($result['template'])
        ->display(array(
            'values' => $result['values'],
            'app' => $app,
            'user' => $_SESSION['USER'],
            'emp' => new Empresas($_SESSION['emp']),
            'suc' => $_SESSION['suc'],
        ));

//------------------------------------------------------------

unset($rq);
unset($con);
unset($loader);
unset($twig);
unset($config);
?>