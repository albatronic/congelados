<?php

/**
 * CLASE PARA TRATAMIENTOS DE TEXTOS
 *
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 19-sep-2012 18:15:06
 */
class Textos {

    /**
     * Recibe una cadena de texto, la trata y devuelve el resultado
     *
     * @param string $texto EL texto a tratar
     * @return string El texto resultante
     */
    static function limpia($texto) {

        $tabla = array(
            '-' => '',
            ' ' => '-',
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'à' => 'a',
            'è' => 'e',
            'ì' => 'i',
            'ò' => 'o',
            'ù' => 'u',
            'ñ' => 'n',
            'Ñ' => 'N',
        );


        // Pasar a minúsculas
        $texto = strtolower($texto);

        // Quito espacios anteriores y posteriores
        $texto = trim($texto);

        // Quito dobles espacios
        $texto = str_replace("  "," ", $texto);

        // Sustituir caracteres po su correspondencias según $tabla
        foreach ($tabla as $key => $value)
            $texto = str_replace($key, $value, $texto);

        // Eliminamos y Reemplazamos demás caracteres especiales
        $find = array('/[^a-z0-9-<>]/', '/[-]+/', '/<[^>]*>/');
        $repl = array('', '-', '');
        $texto = preg_replace($find, $repl, $texto);

        // Quito el eventual primer guión
        if ($texto[0] == "-") $texto = substr($texto, 1);

        // Quito el eventual último guión
        if ($texto[strlen($texto)-1] == "-") $texto = substr($texto, 0,strlen($texto)-1);

        return $texto;
    }

}

?>
