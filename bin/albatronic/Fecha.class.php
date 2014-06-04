<?php

/**
 * Class for fechas
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 28.09.2010 20:14:08
 * @verison 1.0
 *
 * MANEJO DE FECHAS
 *
 * RECIBE UNA FECHA EN CUALQUIERA DE LOS SIGUIENTES FORMATOS:
 * ddmmaaa, dd-mm-aaaa, dd/mm/aaaa, dd.mm.aaaa, aaaa-mm-dd, aaaa/mm/dd, aaaa.mm.dd
 * y lo almacena en $fecha en formato aaaa-mm-dd.
 *
 * SI NO SE INDICA FECHA, SE PONE LA DEL DÍA EN CURSO.
 * 
 * Si la fecha recibida no fuese correcta, almacena en $fecha FALSE.
 *
 */
class Fecha {

    private $fecha;
    private $plantilla = array(
        /**
        "/^\d{1,2}\d{1,2}\d{4} \d{2}:\d{2}:\d{2}$/",        // ddmmaaaa hh:mm:ss
        "/^\d{1,2}.\d{1,2}.\d{4} \d{2}:\d{2}:\d{2}$/",      // dd.mm.aaaa hh:mm:ss
        "/^\d{1,2}-\d{1,2}-\d{4} \d{2}:\d{2}:\d{2}$/",      // dd-mm-aaaa hh:mm:ss    
        "/^\d{1,2}\/\d{1,2}\/\d{4} \d{2}:\d{2}:\d{2}$/",    // dd/mm/aaaa hh:mm:ss      
        
        
        "/^\d{4}\d{1,2}\d{1,2} \d{2}:\d{2}:\d{2}$/",        // aaaammdd hh:mm:ss
        "/^\d{4}.\d{1,2}.\d{1,2} \d{2}:\d{2}:\d{2}$/",      // aaaa.mm.dd hh:mm:ss
        "/^\d{4}-\d{1,2}-\d{1,2} \d{2}:\d{2}:\d{2}$/",      // aaaa-mm-dd hh:mm:ss    
        "/^\d{4}\/\d{1,2}\/\d{1,2} \d{2}:\d{2}:\d{2}$/",    // aaaa/mm/dd hh:mm:ss  
        */
        "/^\d{1,2}\d{1,2}\d{4}$/",      // ddmmaaaa        
        "/^\d{1,2}\.\d{1,2}\.\d{4}$/",    // dd.mm.aaaa        
        "/^\d{1,2}\-\d{1,2}\-\d{4}$/",    // dd-mm-aaaa
        "/^\d{1,2}\/\d{1,2}\/\d{4}$/",  // dd/mm/aaaa
        
          
        "/^\d{4}\d{1,2}\d{1,2}$/",      // aaaammdd        
        "/^\d{4}\.\d{1,2}\.\d{1,2}$/",    // aaaa.mm.dd        
        "/^\d{4}\-\d{1,2}\-\d{1,2}$/",    // aaaa-mm-dd
        "/^\d{4}\/\d{1,2}\/\d{1,2}$/",  // aaaa/mm/dd        

    );
    private $plantilla1 = array(
        "([0-9]{1,2})([0-9]{1,2})([0-9]{4})", //ddmmaaaa
        "([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", //dd-mm-aaaa
        "([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", //dd/mm/aaaa
        "([0-9]{1,2}).([0-9]{1,2}).([0-9]{4})", //dd.mm.aaaa
        "([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", //aaaa-mm-dd
        "([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})", //aaaa/mm/dd
        "([0-9]{4}).([0-9]{1,2}).([0-9]{1,2})", //aaaa.mm.dd
    );

    public function __construct($fecha='') {
        $formatoCorrecto = 0;

        if ($fecha == '')
            $fecha = date('d-m-Y');

        //Buscar en que formato viene la fecha. Lo indicará el valor del índice $i
        for ($i = 0; $i < count($this->plantilla); $i++) {
            if (preg_match($this->plantilla[$i], $fecha)) {
                $formatoCorrecto = $i;
                break;
            }
        }

        if ($formatoCorrecto) {
            switch ($formatoCorrecto) {
                case '0':
                    $fecha = substr($fecha,4,2) . "-" . substr($fecha,2,2) . "-" . substr($fecha,0,2);
                    break;
                case '1':
                    $registro = explode(".",$fecha);
                    $fecha = $registro[2] . "-" . $registro[1] . "-" . $registro[0];
                    break;                    
                case '2':
                    $registro = explode("-",$fecha);
                    $fecha = $registro[2] . "-" . $registro[1] . "-" . $registro[0];
                    break;                    
                case '3':
                    $registro = explode("/",$fecha);
                    $fecha = $registro[2] . "-" . $registro[1] . "-" . $registro[0];
                    break;
                case '4':
                    $fecha = substr($fecha,0,4) . "-" . substr($fecha,4,2) . "-" . substr($fecha,6,2);
                    break;                   
                case '5':
                    $registro = explode(".",$fecha);  
                    $fecha = $registro[0] . "-" . $registro[1] . "-" . $registro[2]; 
                    break;                    
                case '6':
                    $registro = explode("-",$fecha);
                    $fecha = $registro[0] . "-" . $registro[1] . "-" . $registro[2]; 
                    break;
                case '7':
                    $registro = explode("/",$fecha);
                    $fecha = $registro[0] . "-" . $registro[1] . "-" . $registro[2];
                    break;
            }
            $this->fecha = $fecha;
        } else
            $this->fecha = false;
        
    }

    /**
     * Devuelve la fecha en formato dd/mm/aaaa
     * @return string
     */
    public function getddmmaaaa() {
        if ($this->fecha)
            return substr($this->fecha, -2) . "/" . substr($this->fecha, 5, 2) . "/" . substr($this->fecha, 0, 4);
        else
            return "00/00/0000";
    }

    /**
     * Devuelve la fecha en formato aaaa/mm/dd
     * @return string
     */
    public function getaaaammdd() {
        if ($this->fecha)
            return $this->fecha;
        //return substr($this->fecha, 0, 4) . "/" . substr($this->fecha, 5, 2) . "/" . substr($this->fecha, 8, 2);
        else
            return "0000/00/00";
    }

    /**
     * Devuelve el año de la fecha en formato aaaa
     * @return string
     */
    public function getaaaa() {
        if ($this->fecha)
            return substr($this->fecha, 0, 4);
        else
            return "0000";
    }

    /**
     * Devuelve el mes de la fecha en formato mm
     * @return string El mes
     */
    public function getmm() {
        if ($this->fecha)
            return substr($this->fecha, 5, 2);
        else
            return "00";
    }

    /**
     * Devuelve el mes de la fecha en formato literal
     * @return string El mes
     */
    public function getMes() {
        if ($this->fecha)
            return date('M', substr($this->fecha, 5, 2));
    }

    /**
     * Devuelve el dia de la fecha en formato dd
     * @return string
     */
    public function getdd() {
        if ($this->fecha)
            return substr($this->fecha, 8, 2);
        else
            return "00";
    }

    /**
     * Le suma o resta días a la fecha actual y devuelve la fecha resultante
     * @param integer $dias Numero de días a sumar o restar
     * @return date  La nueva fecha calculada
     */
    public function sumaDias($dias) {
        return date('Y-m-d', mktime(0, 0, 0, $this->getmm(), $this->getdd() + $dias, $this->getaaaa()));
    }

    /**
     * Devuelve la fecha en formato aaaa-mm-dd
     * @return string
     */
    public function getFecha() {
        return $this->fecha;
    }

    /**
     * Devuelve la representación numérica ISO-8601 del día 
     * de la semana (1 para Lunes ... 7 para Domingo)
     */
    public function getDiaSemana() {
        return date("N", mktime(0, 0, 0, $this->getmm(), $this->getdd(), $this->getaaaa()));
    }
}

?>