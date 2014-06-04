<?php

/**
 * CONTROLLER FOR HistoricoLog
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 07.06.2011 00:45:16
 */
class HistoricoLogController extends Controller {

    protected $entity = "HistoricoLog";
    protected $parentEntity = "";

    public function __construct($request) {

        parent::__construct($request);

        if ($_SESSION['usuarioPortal']['Id'] === '1') {
            $this->values['permisos']['permisosModulo']['CO'] = true;
        }
    }

    /**
     * Generar el arbol de años y meses donde hay histórico
     * @return array
     */
    public function IndexAction() {

        $archivo = $this->request[2];

        if ($archivo == '') {
            $carpetas = Archivo::getDirectorios("docs/docs{$_SESSION['emp']}/log");

            $historico = array();

            foreach ($carpetas as $carpeta)
                $historico[substr($carpeta, 0, 4)][substr($carpeta, 4, 2)] = $carpeta;

            $this->values['historico'] = $historico;
            $template = "list.html.twig";
        } else {
            $fileLog = "docs/docs{$_SESSION['emp']}/log/{$archivo}";
            $detalle = $this->leeArchivo($fileLog);
            $this->values['detalle'] = $detalle;
            $this->values['fileLog'] = $fileLog;
            $template = "detalle.html.twig";
            //print_r($detalle);
        }

        return array('values' => $this->values, 'template' => "{$this->entity}/{$template}");
    }

    private function leeArchivo($fileName) {

        $archivo = new Archivo($fileName);

        $detalle = array();

        if ($archivo->open("r")) {
            set_time_limit(0);
            while (($linea = $archivo->readLine()) !== FALSE) {
                $resto = json_decode($linea[3],true);
                $metodo = $resto['METHOD'];
                switch ($metodo) {
                    case 'GET':
                        $controller = $resto[0];
                        $action = $resto[1];
                        $mas = $resto[2]."/".$resto[3];
                        break;
                    case 'POST':
                        $controller = $resto['controller'];
                        $action = $resto['action'];
                        $accion = $resto['accion'];
                        $mas = print_r($resto[$resto['controller']],true);
                        break;
                }
                $detalle[] = array(
                    'ip' => $linea[0],
                    'fecha' => $linea[1],
                    'usuario' => json_decode($linea[2],true),
                    'controller' => $controller,
                    'action' => $action,
                    'accion' => $accion,
                    'mas' => $mas,
                    'metodo' => $metodo,
                );
            }
            $archivo->close();
        }
        else
            $this->values['errores'][] = "El archivo " . $fileName . " no existe o no tiene permisos de lectura";

        return $detalle;
    }

}

?>