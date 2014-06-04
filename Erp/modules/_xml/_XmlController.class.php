<?php

/**
 * CONTROLLER FOR ZonasTransporte
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 07.06.2011 00:45:19

 * Extiende a la clase controller
 */

class _XmlController extends Controller {

    public function __construct($request) {
        $this->request = $request;

        $this->values = array(
            'request' => $this->request,
        );
    }

    public function indexAction() {
        $template = "_xml" . '/index.html.twig';

        $dir = "modules";
        $directorio = opendir($dir);
        $i = 0;
        while ($archivo = readdir($directorio))
            if (($archivo[0] != "_") & ($archivo[0] != ".") && ($archivo[0] != "..")) {
                $this->values['modules'][$i] = array("Id" => $i, "Value" => $archivo);
                $i++;
            }

        closedir($directorio);


        return array('template' => $template, 'values' => $this->values);
    }

    public function editAction() {
        switch ($this->request["METHOD"]) {
            case 'GET':
                $xml = new Form($this->request[2]);
                break;

            case 'POST':
                $xml = new Form($this->request['modulo']);
                break;
        }

        $template = "_xml" . '/edit.html.twig';
        $this->values['xml'] = $xml->getXml();

        switch ($this->request['accion']) {
            case 'Guardar': //GUARDAR DATOS
                $template = "_xml" . '/index.html.twig';
                return array('template' => $template, 'values' => $this->values);
                break;
        }
        return array('template' => $template, 'values' => $this->values);
    }

}

?>