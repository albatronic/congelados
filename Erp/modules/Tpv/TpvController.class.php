<?php

/**
 * Description of TpvController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 08-oct-2012 12:36:02
 */
class TpvController extends Controller {

    public function NavegarAction($accion, $idAlbaran) {

        if ($this->values['permisos']['C']) {
            switch ($accion) {
                case '';
                case 'U':
                    $albaranes = new AlbaranesCab();
                    $albaran = $albaranes->getLastObject();
                    unset($albaranes);
                    break;
                case 'P':
                    $albaranes = new AlbaranesCab();
                    $albaran = $albaranes->getFirstObject();
                    unset($albaranes);
                    break;
                case 'A':
                    $albaranes = new AlbaranesCab();
                    $rows = $albaranes->cargaCondicion("IDAlbaran","IDSucursal='{$_SESSION['emp']}' AND IDTpv='{$_SESSION['suc']}' AND IDAlbaran<'{$idAlbaran}'","IDAlbaran DESC LIMIT 1");
                    $albaran = new AlbaranesCab($rows[0]['IDAlbaran']);
                    unset($albaranes);
                    break;
                case 'S':
                    $albaranes = new AlbaranesCab();
                    $rows = $albaranes->cargaCondicion("IDAlbaran","IDSucursal='{$_SESSION['emp']}' AND IDTpv='{$_SESSION['suc']}' AND IDAlbaran>'{$idAlbaran}'","IDAlbaran ASC LIMIT 1");
                    $albaran = new AlbaranesCab($rows[0]['IDAlbaran']);
                    unset($albaranes);
                    break;
            }

            $lineas = new AlbaranesLineas();
            $rows = $lineas->cargaCondicion("IDLinea","IDAlbaran='{$albaran->getIDAlbaran()}'","IDLinea ASC");
            unset($lineas);

            foreach ($rows as $linea)
                $lineas[] = new AlbaranesLineas($linea['IDLinea']);

            $this->values['albaran'] = $albaran;
            $this->values['lineas'] = $lineas;

            $template = "ticket.html.twig";
        } else {
            $template = "_global/forbiden.html.twig";
        }

        return array('template' => $template, 'values' => $this->values);
    }

    public function EditAction() {
        
    }

    public function NewAction() {

    }

    public function ImprimirAction() {

    }

    public function CerrarVentaAction() {

    }
}

?>
