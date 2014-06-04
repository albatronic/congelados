<?php

/**
 * Description of Documents
 *
 * Devuelve un array con objetos "Archivo" asociados
 * a la entidad indicada en el constructor
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 13-abr-2012
 *
 */
class Documents {

    protected $entidad;
    protected $path;
    protected $arrayDocuments = array();

    public function __construct($entidad, $id) {

        $this->entidad = $entidad;
        $this->path = $_SERVER['DOCUMENT_ROOT'] . $_SESSION['appPath'] . "/docs/docs" . $_SESSION['emp'] . "/images/" . $this->entidad . "/" . $id . "_*.*";

        foreach (glob($this->path) as $fileName) {
            $pathDocument = $_SERVER['DOCUMENT_ROOT'] . $_SESSION['appPath'] . "/docs/docs{$_SESSION['emp']}/images/{$this->entidad}/" . basename($fileName);
            $this->arrayDocuments[] = new Archivo($pathDocument);
        }
    }

    /**
     * Devuelve un array con el path a los documentos asociados
     * a la entidad e id indicado en el constructor.
     * 
     * El path es relativo al path de la app (no es absoluto)
     * 
     * @return array Array con el path a los documentos
     */
    public function getDocuments() {
        return $this->arrayDocuments;
    }

    /**
     * Devuelve el numero de documentos asociados a la entidad
     * e id indicado en el constructor
     *
     * @return integer El numero de documentos
     */
    public function getNumberOfDocuments() {
        return count($this->arrayDocuments);
    }

}

?>
