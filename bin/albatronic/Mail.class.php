<?php

/**
 * Clase para envíos de correos electrónicos
 *
 * Está implementado para ser independiente del motor de envíos
 * Si el constructor recibe un objeto mailer, lo utiliza, en caso contrario
 * lo instancia en base a los parametros indicados en el nodo 'mailer'
 * del archivo de configuracion 'config/config.yml' donde por defecto
 * se utiliza la clase PHPMailer_v2.0.0
 *
 * Métodos Públicos:
 *
 *  send($para,$de,$deNombre,$asunto,$mensaje, array $adjuntos): envía el email
 *  compruebaEmail($email): comprueba la validez sintáctica del $email
 *
 * @author Sergio Perez. <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 29.05.2011
 */



class Mail {

    private $mailer;
    private $mensaje = array();

    public function __construct($mailer='') {
        if (is_object($mailer))
            $this->mailer = $mailer;
        else {
            // Busco el motor para enviar correos, que debe estar
            // indicado en el nodo 'mailer' del fichero de configuracion
            $config = sfYaml::load('config/config.yml');
            $config = $config['config']['mailer'];
            // Cargo la clase
            if (file_exists($config['plugin_dir'].$config['plugin_file'])) {
                include_once $config['plugin_dir'] . $config['plugin_file'];

                // Instancio un objeto de la clase mailer. La clase que se utilizará
                // debe estar indicada en el subnodo 'motor' del nodo 'mailer'
                $this->mailer = new $config['motor']();

                // Cargo los parametros que necesita el objeto mailer
                $this->mailer->PluginDir = $config['plugin_dir'];
                $this->mailer->Mailer = $config['socket'];
                $this->mailer->Host = $config['host'];
                $this->mailer->SMTPAuth = $config['smtp_auth'];
                $this->mailer->Username = $config['user_name'];
                $this->mailer->Password = $config['password'];
                $this->mailer->Timeout = (double) $config['timeout'];
                $this->mailer->From = $config['from'];
                $this->mailer->FromName = $config['from_name'];
                $this->mailer->setLanguage(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2),$config['plugin_dir']."language/");
            } else $this->mensaje = "Error: no se ha podido crear el objeto mailer.";
        }
    }

    /**
     * Envia un email
     *
     * @param email_adress $para La dirección del destinatario
     * @param email_adress $de La dirección del remitente
     * @param string $deNombre El nombre del remitente
     * @param string $asunto El texto del asunto
     * @param string $mensaje El texto de mensaje
     * @param array $adjuntos Array con los nombres de los ficheros adjuntos
     * @return string Mensaje de exito o fracaso al enviar
     */
    public function send($para, $de, $deNombre, $asunto, $mensaje, array $adjuntos) {

        if ( $this->valida($para, $mensaje) ) {

            if (trim($de) != '')
                $this->mailer->From = $de;
            if (trim($deNombre) != '')
                $this->mailer->FromName = $deNombre;

            $this->mailer->AddAddress($para);
            foreach ($adjuntos as $adjunto) {
                $this->mailer->AddAttachment($adjunto);
            }
            $this->mailer->Subject = trim($asunto);
            $this->mailer->Body = trim($mensaje);
            $this->mailer->IsHTML(true);

            if (!$this->mailer->Send())
                $this->mensaje[] = "Error en el envio: " . $this->mailer->ErrorInfo;
        }
        return (count($this->mensaje) == 0);
    }

    /**
     * Comprueba que los parámetros sean válidos
     * Devuelve el mensaje de error o NULL si es todo correcto
     *
     * @param email_address $email
     * @param string $contenido
     * @return string
     */
    private function valida($email, $contenido) {

        $this->compruebaEmail($email);

        if (trim($contenido) == "")
            $this->mensaje[] = "No ha indicado ningun contenido.";

        return (count($this->mensaje) == 0);
    }

    /**
     * Comprueba la validez sintáctica de un email
     * Devuelve true o false
     *
     * @param string $email El correo electrónico
     * @return boolean
     */
    public function compruebaEmail($email) {

        $ok = false;

        //compruebo unas cosas primeras
        if ((strlen($email) >= 6) && (substr_count($email, "@") == 1) && (substr($email, 0, 1) != "@") && (substr($email, strlen($email) - 1, 1) != "@")) {
            if ((!strstr($email, "'")) && (!strstr($email, "\"")) && (!strstr($email, "\\")) && (!strstr($email, "\$")) && (!strstr($email, " "))) {
                //miro si tiene caracter .
                if (substr_count($email, ".") >= 1) {
                    //obtengo la terminacion del dominio
                    $term_dom = substr(strrchr($email, '.'), 1);
                    //compruebo que la terminación del dominio sea correcta
                    if (strlen($term_dom) > 1 && strlen($term_dom) < 5 && (!strstr($term_dom, "@"))) {
                        //compruebo que lo de antes del dominio sea correcto
                        $antes_dom = substr($email, 0, strlen($email) - strlen($term_dom) - 1);
                        $caracter_ult = substr($antes_dom, strlen($antes_dom) - 1, 1);
                        if ($caracter_ult != "@" && $caracter_ult != ".") {
                            $ok = true;
                        }
                    }
                }
            }
        }

        if (!$ok)
            $this->mensaje[] = "La direccion email indicada no es valida";

        return (count($this->mensaje) == 0);
    }

    /**
     * Devuelve un array con los eventuales mensajes de error
     * @return array Array de mensajes
     */
    public function getMensaje() {
        return $this->mensaje;
    }
}

?>
