<?php

/**
 * CLASE REQUEST.
 *
 * Almacena y devuelve diferentes variables
 * descriptivas de la petición request.
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC, SL
 * @version 1.0 22.05.2011
 */
class Request {

    /**
     * Parámetros que vienen por GET
     * @var array
     */
    private $parameters;
    /**
     * Método empleado en la peticion:
     * GET, POST, COOKIE
     * @var string
     */
    private $method;
    /**
     * Parámetros que viene por POST
     * @var array
     */
    private $request;
    /**
     * Lenguaje aceptado
     * @var string
     */
    private $acceptLanguage;
    /**
     * Direccion IP del visitante
     * @var string
     */
    private $remoteAddr;
    /**
     * Navegador
     * @var string
     */
    private $userAgent;
    /**
     * Tipo de contenido
     * @var string
     */
    private $contentType;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->request = $_REQUEST;
        $this->acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $this->remoteAddr = $_SERVER['REMOTE_ADDR'];
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->contentType = $_SERVER['CONTENT_TYPE'];
    }

    /**
     * Analiza la Url amigable, la trozea y almacena
     * sus componentes en el array $parametros
     * @return array Parametros de la url
     */
    private function getUrl() {
        $parameters = array();
        $url = parse_url($_SERVER['REQUEST_URI']);
        foreach (explode("/", $url['path']) as $p)
            if ($p != '')
                $parameters[] = $p;

        return $parameters;
    }

    /**
     * Devuelve un array con los valores enviados por GET de la url amigable.
     * 
     * IMPORTANTE:
     *   No se incluye en el array el path de la aplicación, solo lo parámetros.
     * 
     *   Ej: en el caso http://www.demo.com/app/Erp/Clientes/edit/1
     * 
     *   Si el path de la aplicacion es "app/Erp", los parametros que se
     *   devuelven son:
     *   array('Clientes','edit','1')
     *
     * @param string $appPath path de la aplicacion
     * @return array Array con los parametros de la url
     */
    public function getParameters($appPath) {

        $this->parameters = array();
        
        // Cojo la url, incluido el path a la aplicacion
        $url = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
        // A la url le quito la parte del path a la aplicacion
        $params = str_replace($appPath, "", $url);
        // Troceo los parámetros y los meto en un array.
        // El primer parámetro tendrá el índice 0 en el array
        foreach (explode("/", $params) as $p)
            if ($p != '')
                $this->parameters[] = $p;

        return $this->parameters;
    }

    /**
     * Devuelve un string con el método (GET,POST,COOKIE)
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Devuelve un array con las variables enviadas por POST
     * @return array
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Devuelve un string con el idioma aceptado por el cliente (ej: es-ES)
     * @return string
     */
    public function getLanguage() {
        return $this->acceptLanguage;
    }
    
    /**
     * Devuelve un string con el idiona aceptado por el cliente con 2 caracteres
     * @return string El lenguaje expresado en 2 caracteres
     */
    public function getShortLanguage() {
        return substr($this->acceptLanguage,0,2);
    }

    /**
     * Devuelve un string con la dirección IP del cliente
     * @return string
     */
    public function getRemoteAddr() {
        return $this->remoteAddr;
    }

    /**
     * Devuelve un string con el navegador utilizado por el cliente
     * @return string
     */
    public function getUserAgent() {
        return $this->userAgent;
    }

    /**
     * Devuelve un string con el tipo de contenido de la pagina
     * @return string
     */
    public function getContentType() {
        return $this->contentType;
    }

}
?>
