<?php

/**
 * Description of ContaPlusDiario
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 09-may-2012
 *
 */
class ContaPlusDiario {

    /**
     * 1
     * Numero de asiento
     * @var int(6)
     */
    protected $Asien = '      ';
    /**
     * 2
     * Fecha Asiento
     * @var date(8)
     */
    protected $Fecha = '        ';
    /**
     * 3
     * Subcuenta
     * @var char(12)
     */
    protected $SubCta = '            ';
    /**
     * 4
     * Contrapartida/IVA
     * @var char(12)
     */
    protected $Contra = '            ';
    /**
     * 5
     * PtaDebe
     * Importe al debe en pesetas. 16 posiciones en total
     * @var decimal(16,2)
     */
    protected $PtaDebe = '            0.00';
    /**
     * 6
     * Concepto
     * @var char(25)
     */
    protected $Concepto = '                         ';
    /**
     * 7
     * PtaHaber
     * Importe al haber en pesetas. 16 posiciones en total
     * @var decimal(16,2)
     */
    protected $PtaHaber = '            0.00';
    /**
     * 8
     * Numero de Factura al IVA
     * @var integer(8)
     */
    protected $Factura = '        ';
    /**
     * 9
     * Base Imponible del IVA en pesetas
     * @var decimal(16,2). 16 posiciones
     */
    protected $Baseimpo = '            0.00';
    /**
     * 10
     * Porcentaje de IVA
     * @var decimal(5,2). 5 posiciones
     */
    protected $IVA = ' 0.00';
    /**
     * 11
     * Porcentaje de Recargo de Equivalencia
     * @var decimal(5,2). 5 posiciones
     */
    protected $Recequiv = ' 0.00';
    /**
     * 12
     * Documento
     * @var char(10)
     */
    protected $Documento = '          ';
    /**
     * 13
     * Codigo Departamento
     * @var char(3)
     */
    protected $Departa = '   ';
    /**
     * 14
     * Clave, código del proyecto
     * @var char(6)
     */
    protected $Clave = '      ';
    /**
     * 15
     * Estado, punteo (interno)
     * @var char(1)
     */
    protected $Estado = ' ';
    /**
     * 16
     * Ncasado, Numérico de casación (interno)
     * @var integer(6)
     */
    protected $Ncasado = '     0';
    /**
     * 17
     * TCasado, tipo de casado (interno)
     * @var integer(1)
     */
    protected $TCasado = '0';
    /**
     * 18
     * Trans, Número de pago
     * @var integer(6)
     */
    protected $Trans = '     0';
    /**
     * 19
     * Cambio a aplicar
     * @var decimal(16,6). 16 posiciones
     */
    protected $Cambio = '        0.000000';
    /**
     * 20
     * DebeME, Importe debe moneda extranjeta
     * @var decimal(16,2). 16 posiciones
     */
    protected $DebeME = '            0.00';
    /**
     * 21
     * HaberME, Importe haber moneda extranjeta
     * @var decimal(16,2). 16 posiciones
     */
    protected $HaberME = '            0.00';
    /**
     * 22
     * Auxiliar (interno)
     * @var char(1)
     */
    protected $Auxiliar = ' ';
    /**
     * 23
     * Serie de la facturacion
     * @var char(1)
     */
    protected $Serie = ' ';
    /**
     * 24
     * Sucursal, sin uso
     * @var char(4)
     */
    protected $Sucursal = '    ';
    /**
     * 25
     * Codigo de divisa
     * @var char(5)
     */
    protected $CodDivisa = '     ';
    /**
     * 26
     * Importe auxiliar moneda extranjeta
     * @var decimal(16,2) 16 posiciones
     */
    protected $ImpAuxME = '            0.00';
    /**
     * 27
     * Moneda uso, 1=Ptas; 2=Euros
     * @var char(1)
     */
    protected $MonedaUso = '2';
    /**
     * 28
     * Importe al debe en euros
     * @var decimal(16,2). 16 posiciones
     */
    protected $EuroDebe = '            0.00';
    /**
     * 29
     * Importe al haber en euros
     * @var decimal(16,2). 16 posiciones
     */
    protected $EuroHaber = '            0.00';
    /**
     * 30
     * Base Imponible del IVA en euros
     * @var decimal(16,2). 16 posiciones
     */
    protected $BaseEuro = '            0.00';
    /**
     * 31
     * NoConv, interno
     * @var boolean
     */
    protected $NoConv = 'F';
    /**
     * 32
     * Código de Activo
     * @var char(10)
     */
    protected $NumeroInv = '          ';
    /**
     * 33
     * Serie Factura Rectificativa
     * @var char(1)
     */
    protected $Serie_RT = ' ';
    /**
     * 34
     * Numero de factura rectificativa
     * @var int(8)
     */
    protected $Factu_RT = '       0';
    /**
     * 35
     * Base imponible factura rectificativa
     * @var decimal(16,2). 16 posiciones
     */
    protected $BaseImp_RT = '            0.00';
    /**
     * 36
     * Base imponible factura rectificativa
     * @var decimal(16,2). 16 posiciones
     */
    protected $BaseImp_RF = '            0.00';
    /**
     * 37
     * Es rectificativa??. T en caso de rectificativa
     * @var boolean
     */
    protected $Rectifica = 'F';
    /**
     * 38
     * Fecha de la factura rectificativa
     * @var date(8)
     */
    protected $Fecha_RT = '        ';
    /**
     * 39
     * NIC. Dos valores posibles:
     * ' ' : Asiento válido solo P.G.C.
     * 'E' : Asiento válido para P.G.C. y para los normas NIC
     * @var char(1)
     */
    protected $NIC = 'E';
    /**
     * 40
     * Libre
     * @var boolean
     */
    protected $Libre1 = 'F';
    /**
     * 41
     * Libre
     * @var int(6)
     */
    protected $Libre2 = '     0';
    /**
     * 42
     * Operaciones interrumpidas
     * @var boolean
     */
    protected $IInterrump = 'F';
    /**
     * 43
     * Segmentos Actividades
     * @var char(6)
     */
    protected $SegActiv = '      ';
    /**
     * 44
     * Segmentos geográficos
     * @var char(6)
     */
    protected $SegGeog = '      ';
    /**
     * 45
     * Rectificación period. anteriores Md. 349 IVA
     * @var boolean
     */
    protected $IRect349 = 'F';
    /**
     * 46
     * Fecha Operacion mod 349
     * @var date(8)
     */
    protected $Fecha_OP = '        ';
    /**
     * 47
     * Fecha Expedición mod 349
     * @var date(8)
     */
    protected $Fecha_EX = '        ';
    /**
     * Columnas sin uso de la 48 a la 56 inclusive
     * @var char(255)
     */
    protected $SinUso;
    /**
     * 57
     * Clave operación (*5)
     * @var char(1)
     */
    protected $TipoOpe = '1';
    /**
     * 58 !! OJO QUITADOS LOS ESPACIOS
     * Número total de facturas o tickets que agrupa el asiento para los casos de clave de operación A ó B
     * @var char(8)
     */
    protected $nFacTick = '';
    /**
     * 59
     * Número de factura inicial de la agrupación. Sólo para claves A ó B
     * @var char(40)
     */
    protected $NumAcuIni = '                                        ';
    /**
     * 60
     * Número de factura final de la agrupación. Sólo para claves A ó B
     * @var char(40)
     */
    protected $NumAcuFin = '                                        ';
    /**
     * 61
     * Clave de identificación de tercero (*6)
     * Se pone a 1 cuando el apunte es de IVA
     * @var int(1)
     */
    protected $TerIdNif = '0';
    /**
     * 62
     * NIF del tercero (subcuenta de contrapartida del apunte de IVA)
     * @var char(15)
     */
    protected $TerNif = '               ';
    /**
     * 63
     * Nombre / Razón Social del tercero
     * @var char(40)
     */
    protected $TerNom = '                                        ';
    /**
     * 64
     * Nif del representante legal del tercero para menores de 14 años
     * @var char(9)
     */
    protected $TerNif14 = '         ';
    /**
     * 65
     * T en caso de factura de transmisión de bienes de inversión
     * @var boolean
     */
    protected $TBienTran = 'F';
    /*
     * 66
     * Código de inventario de la factura de transmisión de bienes de inversión
     * @var char(10)
     */
    protected $TBienCod = '          ';
    /**
     * 67
     * T cuando la factura sea una transmisión de inmuebles sujetos a IVA
     * @var boolean
     */
    protected $Transinm = 'F';
    /**
     * 68
     * Apunte de cobro en metálico
     * @var boolean
     */
    protected $Metal = 'F';
    /**
     * 69
     * Importe del cobro realizado en metálico
     * @var decimal(16,2). 16 posiciones
     */
    protected $MetalImp = '            0.00';
    /**
     * 70
     * Código de subcuenta del cliente del que se realiza el cobro en metálico
     * @var char(12)
     */
    protected $Cliente = '            ';
    /**
     * 71
     * Tipo de bienes en operaciones de compras. Valores posbiles:
     * 1 = Bienes y servicios corrientes.
     * 2 = Bienes de inversión
     * Campo opcional. Se podrá dejar en blanco o con valor cero para las partidas en la que no aplique.
     * @var int(1)
     */
    protected $OpBienes = '0';
    /**
     * 72
     * Nº Factura expedición. Para facturas de compra (Modelo 340)
     * @var char(40)
     */
    protected $FacturaEx = '                                        ';
    /**
     * 73
     * E = emitida; R = Recibida
     * @var char(1)
     */
    protected $TipoFac = ' ';
    /**
     * 74
     * Tipo de IVA de la Subcuenta. (***) y (****)
     * @var char(1)
     */
    protected $TipoIVA = ' ';
    /**
     * 75
     * GUID. Sin Uso
     * @var char(40)
     */
    protected $GUID = '                                        ';
    /**
     * 76
     * Incluir factura en Modelo 340.
     * @var boolean(1)
     */
    protected $L340 = ' ';
    /**
     * 77
     * Año de la factura con la que se corresponde el cobro en metálico.
     * Por defecto el año de la fecha del asiento.
     * @var int(4)
     */
    protected $MetalEje = '   0';
    /**
     * 78
     * Sin uso
     * @var char(15)
     */
    protected $Document15 = '               ';
    /**
     * 79
     * Cliente de suplido
     * @char(12)
     */
    protected $ClienteSup = '            ';
    /**
     * 80
     * Fecha de suplido
     * @var date(8)
     */
    protected $FechaSub = '        ';
    /**
     * 81
     * Importe suplido
     * @var decimal(16,2). 16 posiciones
     */
    protected $ImporteSup = '            0.00';
    /**
     * 82
     * Documento suplido
     * @var char(40)
     */
    protected $DocSup = '                                        ';
    /**
     * 83
     * Cliente Provision
     * @var char(12);
     */
    protected $ClientePro = '            ';
    /**
     * 84
     * Fecha Provisión
     * @var date(8)
     */
    protected $FechaPro = '        ';
    /**
     * 85
     * Importe provisión
     * @var decimal(16,2). 16 posiciones
     */
    protected $ImportePro = '            0.00';
    /**
     * 86
     * Documento provisión
     * @var char(40)
     */
    protected $DocPro = '                                        ';
    /**
     * 87
     * Clave IRPF (*7)
     * @var int(2)
     */
    protected $nClaveIRPF = ' ';
    
    protected $ColetillaKarem = '0F0                         0 0';


    public function  __construct($asiento,$fecha) {
        $this->setAsien($asiento);
        $this->setFecha($fecha);

        $this->SinUso = str_repeat(" ", 255);
    }
    public function __toString() {

        $apte = '';
        foreach ($this as $field) $apte .= $field;
        return $apte . chr(13) . chr(10);

    }
    public function setAsien($asiento) {
        $this->Asien = substr(str_pad($asiento, 6, " ",STR_PAD_LEFT),0,6);
    }
    public function setFecha($fecha) {
        $this->Fecha = substr(str_pad($fecha, 8),0,8);
    }
    public function setSubCta($subcta) {
        $this->SubCta = substr(str_pad($subcta, 12),0,12);
    }
    public function setContra($contrapartida) {
        $this->Contra = substr(str_pad($contrapartida, 12),0,12);
    }
    public function setConcepto($concepto) {
        $this->Concepto = substr(str_pad($concepto, 25),0,25);
    }
    public function setFactura($factura) {
        $this->Factura = substr(str_pad($factura, 8," ",STR_PAD_LEFT),0,8);
    }
    public function setSerie($serie) {
        $this->Serie = substr(str_pad($serie, 1),0,1);
    }
    public function setDocumento($documento) {
        $this->Documento =substr(str_pad($documento, 10," ",STR_PAD_RIGHT),0,10);
    }
    public function setEuroDebe($importe) {
        $this->EuroDebe = substr(str_pad($importe, 16," ",STR_PAD_LEFT),0,16);
    }
    public function setEuroHaber($importe) {
        $this->EuroHaber = substr(str_pad($importe, 16," ",STR_PAD_LEFT),0,16);
    }
    public function setBaseEuro($importe) {
        $this->BaseEuro = substr(str_pad($importe, 16," ",STR_PAD_LEFT),0,16);
    }
    public function setIVA($iva) {
        $this->IVA = substr(str_pad($iva, 5," ",STR_PAD_LEFT),0,5);
    }
    public function setRecequiv($recargo) {
        $this->Recequiv = substr(str_pad($recargo, 5," ",STR_PAD_LEFT),0,5);
    }
    public function setFecha_OP($fecha) {
        $this->Fecha_OP = substr(str_pad($fecha, 8),0,8);
    }
    public function setFecha_EX($fecha) {
        $this->Fecha_EX = substr(str_pad($fecha, 8),0,8);
    }
    public function setTerIdNif($valor) {
        $this->TerIdNif = substr(str_pad($valor, 1),0,1);
    }
    public function setTerNif($nif) {
        $this->TerNif = substr(str_pad($nif, 15),0,15);
    }
    public function setTerNom($razonSocial) {
        $this->TerNom = substr(str_pad(utf8_decode($razonSocial), 40),0,40);
    }
    public function setOpBienes($valor) {
        $this->OpBienes = substr(str_pad($valor, 1),0,1);
    }
    public function setTipoFac($tipoFactura) {
        $this->TipoFac = substr(str_pad($tipoFactura, 1),0,1);
    }
    public function setTipoIva($tipoIva) {
        $this->TipoIVA = substr(str_pad($tipoIva, 1),0,1);
    }
    public function setL340($valor) {
        $this->L340 = substr(str_pad($valor, 1),0,1);
    }
}

?>