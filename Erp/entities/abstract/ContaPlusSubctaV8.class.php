<?php

/**
 * Description of ContaPlusSubcta
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 09-may-2012
 *
 * NOTA: Las posiciones 8 a 10 son exclusivas de la versión ÉLITE. No todos las
 * propiedades son obligatorias, aunque sí hay algunas(*), otras que además lo son
 * cuando la subcuenta es de IVA (**) y otros que también lo son si la subcuenta
 * es de tercero (*4)
 */
class ContaPlusSubctaV8 {

    /**
     * Código de la Subcuenta
     * @ *
     * @var CHAR(12)
     */
    protected $Codigo = '            ';

    /**
     * Nombre de la Subcuenta
     * @ *
     * @var CHAR(40)
     */
    protected $Titulo = '                                        ';

    /**
     * Nif
     * @ *4
     * @var CHAR(15)
     */
    protected $Nif = '               ';

    /**
     * Domicilio
     * @var CHAR(35)
     */
    protected $Domicilio = '                                   ';

    /**
     * Población
     * @var CHAR(25)
     */
    protected $Poblacion = '                         ';

    /**
     * Provincia
     * @var CHAR(20)
     */
    protected $Provincia = '                    ';

    /**
     * Código Postal
     * @var CHAR(5)
     */
    protected $CodPostal = '     ';

    /**
     * Subcuenta en moneda extranjeta
     * @var BOOLEAN
     */
    protected $Divisa = 'F';

    /**
     * Código de divisa
     * @var CHAR(5)
     */
    protected $CodDivisa = '     ';

    /**
     * Uso obligatorio del documento
     * @var BOOLEAN
     */
    protected $Documento = 'F';

    /**
     * Ajustes M.E.
     * @var BOOLEAN
     */
    protected $Ajustame = 'F';

    /**
     * Tipo de Iva de la Subcuenta
     * @ **
     * @var CHAR(1)
     */
    protected $TipoIVA = ' ';

    /**
     * Proyecto
     * @var CHAR(9)
     */
    protected $Proye = '         ';

    /**
     * Subcuenta de Recargo de Equivalencia
     * @ **
     * @var CHAR(12)
     */
    protected $SubEquiv = '            ';

    /**
     * Subcuenta Cierra 8-9
     * @var CHAR(12)
     */
    protected $Subcierre = '            ';

    /**
     * Operaciones Interrumpidas
     * @var BOOLEAN
     */
    protected $LInterrump = 'F';

    /**
     * Segmento Asociado
     * @var CHAR(12)
     */
    protected $Segmento = '            ';

    /**
     * Porcentaje de IVA
     * @ **
     * @var DECIMAL(5,2)
     */
    protected $TPC = ' 0.00';

    /**
     * Porcentaje Recargo Equivalencia
     * @ **
     * @var DECIMAL(5,2)
     */
    protected $RecEquiv = ' 0.00';

    /**
     * Fax
     * @var CHAR(15)
     */
    protected $Fax01 = '               ';

    /**
     * Email
     * @var CHAR(50)
     */
    protected $Email = '                                                  ';

    /**
     * Descripcion larga de la subcuenta
     * @var CHAR(100)
     */
    protected $TituloL = '                                                                                                    ';

    /**
     * Clave de identificacion del tercero
     * @ *4
     * @var INTEGER(1)
     */
    protected $IdNif = '1';

    /**
     * Código ISO del país
     * @ *4
     * @var CHAR(2)
     */
    protected $CodPais = 'ES';

    /**
     * NIF del representante legal para menores de 14 años
     * @var CHAR(9)
     */
    protected $Rep14NIF = '         ';

    /**
     * Nombre del representante legal para menores de 14 años
     * @var CHAR(40)
     */
    protected $Rep14Nom = '                                        ';

    /**
     * Subcuenta de cobro en metálico
     * @var BOOLEAN
     */
    protected $MetCobro = 'F';

    /**
     * Subcuenta de cobro en metálico frecuente
     * @var BOOLEAN
     */
    protected $MetCobFre = 'F';

    /**
     * Subcuenta de suplido
     * @var boolena(1)
     */
    protected $Suplido = 'F';

    /**
     * Subcuenta de provision
     * @var boolena(1)
     */
    protected $Provision = 'F';

    /**
     * Subcuenta de IRPF
     * @var boolena(1)
     */
    protected $IEsIRPF = 'F';
    protected $Karem = '           ';

    /**
     * Porcentaje de IRPF
     * @var decimal(5,2)
     */
    protected $nIRPF = ' 0.00';

    /**
     * Clave IRPF (*7)
     * @var int(2)
     */
    protected $nClaveIRPF = ' 0';

    /**
     * Retención para el modelo 130
     * @var boolean(1)
     */
    protected $IEsMod130 = 'F';

    /**
     * Deducible/Computable para el 130
     * @var boolena(1)
     */
    protected $IDeducible = 'F';

    // --------------------------------
    // COLUMNAS AÑADIDAS EN LA RELASE 8
    // --------------------------------
    /**
     * Criterio de caja
     * @var boolean(1)
     */
    protected $ICritCaja = 'F';

    /**
     *
     * @var string(12)
     */
    protected $cSubivaas = '            ';

    public function __toString() {

        $subCta = '';
        foreach ($this as $field)
            $subCta .= $field;
        return $subCta . chr(13) . chr(10);
    }

    public function setCodigo($codigo) {
        $this->Codigo = substr(str_pad($codigo, 12), 0, 12);
    }

    public function setTitulo($titulo) {
        $titulo = $this->limpia($titulo);
        $this->Titulo = substr(str_pad(utf8_decode($titulo), 40), 0, 40);
    }

    public function setNif($nif) {
        $this->Nif = substr(str_pad($nif, 15), 0, 15);
    }

    public function setDomicilio($domicilio) {
        $domicilio = $this->limpia($domicilio);
        $this->Domicilio = substr(str_pad(utf8_decode($domicilio), 35), 0, 35);
    }

    public function setPoblacion($poblacion) {
        $poblacion = $this->limpia($poblacion);
        $this->Poblacion = substr(str_pad(utf8_decode($poblacion), 25), 0, 25);
    }

    public function setProvincia($provincia) {
        $provincia = $this->limpia($provincia);
        $this->Provincia = substr(str_pad(utf8_decode($provincia), 20), 0, 20);
    }

    public function setCodPostal($codigoPostal) {
        $this->CodPostal = substr(str_pad($codigoPostal, 5), 0, 5);
    }

    public function setDivisa($divisa) {
        ($divisa) ? $this->Divisa = 'T' : $this->Divisa = 'F';
    }

    public function setCodDivisa($codigoDivisa) {
        $this->CodDivisa = substr(str_pad($codigoDivisa, 5), 0, 5);
    }

    public function setDocumento($documento) {
        $documento = $this->limpia($documento);
        $this->Documento = ($documento) ? 'T' : 'F';
    }

    public function setAjustesME($AjustesME) {
        ($AjustesME) ? $this->AjustesME = 'T' : $this->AjustesME = 'F';
    }

    public function setTipoIva($tipoIva) {
        $this->TipoIva = str_pad($tipoIva, 1);
    }

    public function setProyecto($proyecto) {
        $this->Proyecto = substr(str_pad($proyecto, 9), 0, 9);
    }

    public function setCodPais($codigoPais) {
        $this->CodPais = substr(str_pad($codigoPais, 2), 0, 2);
    }

    /**
     * Recibe un texto y lo limpia en base al array
     * de correspondencias $limpieza
     * 
     * @param string $texto El texto a limpiar
     * @return string El texto limpio
     */
    private function limpia($texto) {

        $limpieza = array(
            'ñ' => 'n',
            'Ñ' => 'N',
            '/' => ' ',
            'º' => '.',
            'ª' => '.',
        );
        foreach ($limpieza as $key => $value)
            $texto = str_replace($key, $value, $texto);

        return $texto;
    }

}

?>
