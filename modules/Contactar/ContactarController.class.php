<?php

/**
 * Description of ContactarController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 28-may-2011
 *
 */
class ContactarController {

    /**
     * Variables enviadas en el request por POST o por GET
     * @var array
     */
    protected $request;

    public function __construct($request) {
        $this->request = $request;
    }

    public function IndexAction() {
        switch ($this->request['METHOD']) {
            case 'GET':
                $values = array();
                return array('template' => 'Contactar/Index.html.twig', 'values' => $values);
                break;

            case 'POST':
                $config = simplexml_load_file('config/config.xml');

                $para = $config->mailer->from;
                $nombre = trim($this->request['nombre']);
                $email = trim($this->request['email']);
                $consulta = trim($this->request['consulta']);

                $mensaje = "";
                if ($nombre == '')
                    $mensaje .= "Debe indicar un nombre. ";
                if ($email == '')
                    $mensaje .= "Debe indicar un email. ";
                if ($consulta == '')
                    $mensaje .= "Debe indicar la consulta.";

                if ($mensaje == '') {
                    $message = "<p>" . $nombre . "</p><p>" . $email . "</p><p>" . $consulta . "</p>";
                    $envio = new mail();
                    $mensaje = $envio->send($para, $email, $nombre, 'Consulta desde formulario de contacto', $message, array());
                    unset($envio);
                }

                $values = array(
                    'mensaje' => $mensaje,
                );

                return array('template' => 'Contactar/Index.html.twig', 'values' => $values);
                break;
        }
    }

}

?>