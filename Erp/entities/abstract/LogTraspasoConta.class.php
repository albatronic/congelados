<?php

/**
 * Description of LogTraspasoConta
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 26-may-2012
 *
 */

class LogTraspasoConta {

    protected $IDTraspaso;
    protected $Dia;
    protected $Hora;
    protected $IDUsuario;
    protected $IDSucursal;
    protected $DesdeFecha;
    protected $HastaFecha;
    protected $Emitidas;
    protected $Recibidas;
    protected $Cobros;
    protected $Pagos;
    protected $Asientos;
    protected $Subcuentas;
    protected $ArchivoDiario;
    protected $ArchivoSubcuentas;
    protected $ArchivoLogTraspasos;
    protected $Traspasos = array(
        'traspasos' => array()
    );

    public function __construct() {

        $this->ArchivoLogTraspasos = $_SERVER['DOCUMENT_ROOT'] . $_SESSION['appPath'] . "/docs/docs{$_SESSION['emp']}/interfaces/contaplus/logtraspasos.yml";
        if (file_exists($this->ArchivoLogTraspasos)) {
            $this->Traspasos = sfYaml::load($this->ArchivoLogTraspasos);
        }
    }

    public function getTraspaso($idTraspaso) {
        return $this->Trapasos['traspasos'][$idTraspaso];
    }

    public function save() {
        $ok = false;

        $stream = sfYaml::dump($this->Traspasos, 3);
        $fp = fopen($this->ArchivoLogTraspasos, "w");
        if ($fp) {
            fwrite($fp, $stream);
            fclose($fp);
            $ok = true;
        }

        return $ok;
    }

    public function add() {
        $this->Traspasos['traspasos'][$this->IDTraspaso] = array(
            'Dia' => $this->Dia,
            'Hora' => $this->Hora,
            'IDUsuario' => $this->IDUsuario,
            'IDSucursal' => $this->IDSucursal,
            'DesdeFecha' => $this->DesdeFecha,
            'HastaFecha' => $this->HastaFecha,
            'Emitidas' => $this->Emitidas,
            'Recibidas' => $this->Recibidas,
            'Cobros' => $this->Cobros,
            'Pagos' => $this->Pagos,
            'Asientos' => $this->Asientos,
            'Subcuentas' => $this->Subcuentas,
            'ArchivoDiario' => $this->ArchivoDiario,
            'ArchivoSubcuentas' => $this->ArchivoSubcuentas,
        );
    }

    /**
     * Quita un elemento del array de traspasos.
     * Para hacerlo permantenente hay que llamar al método save
     * 
     * @param string $idTraspaso 
     */
    public function delete($idTraspaso) {
        unset($this->Traspasos['traspasos'][$idTraspaso]);
    }

    public function getListTraspasos() {
        $lista = $this->Traspasos['traspasos'];
        krsort($lista);
        return $lista;
    }

    public function setIDTraspaso($idTraspaso) {
        $this->IDTraspaso = $idTraspaso;
    }

    public function setDia($dia) {
        $this->Dia = $dia;
    }

    public function setHora($hora) {
        $this->Hora = $hora;
    }

    public function setIDUsuario($idUsuario) {
        $this->IDUsuario = $idUsuario;
    }

    public function setIDSucursal($idSucursal) {
        $this->IDSucursal = $idSucursal;
    }

    public function setDesdeFecha($fecha) {
        $this->DesdeFecha = $fecha;
    }

    public function setHastaFecha($fecha) {
        $this->HastaFecha = $fecha;
    }

    public function setEmitidas($n) {
        $this->Emitidas = $n;
    }

    public function setRecibidas($n) {
        $this->Recibidas = $n;
    }

    public function setCobros($n) {
        $this->Cobros = $n;
    }

    public function setPagos($n) {
        $this->Pagos = $n;
    }

    public function setAsientos($n) {
        $this->Asientos = $n;
    }

    public function setSubcuentas($n) {
        $this->Subcuentas = $n;
    }

    public function setArchivoDiario($archivo) {
        $this->ArchivoDiario = $archivo;
    }

    public function setArchivoSubcuentas($archivo) {
        $this->ArchivoSubcuentas = $archivo;
    }

}

?>
