<?php

/**
  XML parser & validator

  @authorAndrey Nikishaev

  If parent node tag name equal children tag name but ends with 's' then
  it will be an array.
  For example:
  <tournaments>
  <tournament></tournament>
  </tournaments>

  Else it will be an associative array and duplicated tags will be deleted.

  If node has no elements and ends with "s" like "<pages></pages>" then it will
  be empty array.

  Example of use:

  $xmltools = new XMLTools();

  $isvalid        = $xmltools->ValidateXML('test.xml','test.xsd');
  if($isvalid)
  // return True
  $data       = $xmltools->XML2Array('test.xml')
  else
  // returned array of errors
  print_r($isvalid);

  XML like:

  <tournaments>
  <tournament>
  <options>
  <resultType>maxWeight|maxSumWeight|totalCount</resultType>
  <startType>weekly|monthly</startType>
  <startDay>2</startDay>
  <fishtypes>
  <fishtype>1</fishtype>
  <fishtype>2</fishtype>
  </fishtypes>
  <pages>
  </pages>
  <map_pid>1</map_pid>
  <location_pid>1</location_pid>
  </options>
  <variants>
  <variant timeStart="01:50" timeEnd="02:50" name="Canadian losos" />
  <variant timeStart="02:50" timeEnd="03:50" name="Canadian losos" />
  <variant timeStart="03:50" timeEnd="04:50" name="Canadian losos" />
  </variants>
  </tournament>
  </tournaments>

  Will be parsed as:

  Array
  (
  [0] => Array
  (
  [options] => Array
  (
  [resultType] => maxWeight|maxSumWeight|totalCount
  [startType] => weekly|monthly
  [startDay] => 2
  [fishtypes] => Array
  (
  [0] => 1
  [1] => 2
  )

  [pages] => Array()
  [map_pid] => 1
  [location_pid] => 1
  )

  [variants] => Array
  (
  [0] => Array
  (
  [timeStart] => 01:52
  [timeEnd] => 02:50
  [name] => Canadian losos
  )

  )

  )

  )

 */
class XmlTools {

    /**
     * Lee el archivo $file XML y devuelve un array con
     * su contenido.
     *
     * Si no existe el archivo, muestra un mensaje de error
     *
     * @param string Nombre del archivo XML
     * @return array Array con el contenido del archivo XML
     */
    public function Xml2Array($file) {
        if (!($fp = fopen($file, "r"))) {
            die("could not open XML input");
        }

        $contents = fread($fp, filesize($file));
        fclose($fp);

        if (!$contents)
            return array();

        if (!function_exists('xml_parser_create')) {
            throw new Exception("'xml_parser_create()' function not found!");
            return null;
        }

        //Get the XML parser of PHP - PHP must have this module for the parser to work
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);

        if (!$xml_values)
            return; //Hmm...
            //Initializations
        $xml_array = array();
        $parents = array();
        $level = 0;
        $is_array = array();

        $current = &$xml_array; //Refference

        $dc = count($xml_values);
        for ($i = 0; $i < $dc; $i++) {

            $data = $xml_values[$i];
            if (!isset($is_array[$data['level']]) && ($data['level'] > 1) && ($data['tag'] . 's' == $xml_values[$i - 1]['tag'] || $data['tag'] . 'es' == $xml_values[$i - 1]['tag'])) {
                $is_array[$data['level']] = true;
            }
            $level = $data['level'];

            if ($data['type'] == 'open') {

                $c = array();
                if (isset($data['attributes']))
                    $c = $data['attributes'];
                if (!isset($is_array[$level])) {
                    $current[$data['tag']] = $c;
                    $parents[$level] = &$current;
                    $current = &$current[$data['tag']];
                } else {
                    $current[] = $c;
                    $parents[$level] = &$current;
                    $current = &$current[count($current) - 1];
                }
            } else if ($data['type'] == 'complete') {
                $c = (substr($data['tag'], -1) == "s") ? array() : null;
                if (isset($data['attributes']))
                    $c = $data['attributes'];
                if (isset($data['value']) && isset($data['attributes']))
                    $c = array_merge($c, array('value' => trim($data['value'])));
                if (isset($data['value']) && !isset($data['attributes']))
                    $c = trim($data['value']);

                if (isset($is_array[$level])) {
                    $current[] = $c;
                } else {
                    $current[$data['tag']] = $c;
                }
            } else {
                unset($is_array[$level + 1]);
                $current = &$parents[$level];
            }
        }

        return $xml_array;
    }

    private function _libxml_display_error($error) {
        $return = "Error in $error->file (Line:{$error->line}):";
        $return .= trim($error->message);
        return $return;
    }

    private function _libxml_display_errors() {

        $errors = libxml_get_errors();
        $res = array();
        foreach ($errors as $error) {
            $res[] = $this->_libxml_display_error($error);
        }
        libxml_clear_errors();
        return $res;
    }

    public function ValidateXML($xml, $schema) {
        if (!class_exists('DOMDocument')) {
            throw new Exception("'DOMDocument' class not found!");
            return False;
        }

        libxml_use_internal_errors(true);

        if (!($fp = fopen($xml, "r"))) {
            die("could not open XML input");
        }

        $contents = fread($fp, filesize($xml));
        fclose($fp);

        $xml = new DOMDocument();
        $xml->loadXML($contents);

        if (!$xml->schemaValidate($schema)) {
            return $this->_libxml_display_errors();
        } else {
            return True;
        }
    }

}

?>