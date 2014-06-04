<?php

/**
 * Class Log
 *
 * Registra evento en un fichero de texto
 * Se va generando un fichero log para cada semana del año
 * El archivo log se guarda en docs/docsxxx/log
 *
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright Informatica ALBATRONIC, SL
 * @version 1.0 05.10.2013
 */
class Log {

    /**
     * Escribe en el archivo log si el usuario no es el super:
     * 
     * la ip
     * la fecha y hora
     * json con los datos del usuario
     * json con el request
     */
    static function write($request) {

        if ($_SESSION['usuarioPortal']['Id'] != '1') {
            $ficheroLog = "docs/docs" . $_SESSION['emp'] . "/log/" . date('YW');
            $ip = substr($_SERVER['REMOTE_ADDR'] . str_repeat(" ", 15), 0, 15);
            $time = date('d-m-Y H:i:s');
            $user = json_encode($_SESSION['USER']['user']);
            $accion = json_encode($request);

            $evento = "{$ip}\t{$time}\t{$user}\t{$accion}\n";

            if (!file_exists($ficheroLog)) {
                $fp = fopen($ficheroLog, 'w');
            } else {
                $fp = fopen($ficheroLog, 'a');
            }

            if ($fp) {
                fwrite($fp, $evento);
                fclose($fp);
            }
        }
    }

}

?>
